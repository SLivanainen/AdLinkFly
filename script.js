document.addEventListener('DOMContentLoaded', function() {
    const shortenForm = document.getElementById('shortenForm');
    const longUrlInput = document.getElementById('longUrl');
    const shortenBtn = document.getElementById('shortenBtn');
    const resultContainer = document.getElementById('resultContainer');
    const shortUrlSpan = document.getElementById('shortUrl');
    const copyBtn = document.getElementById('copyBtn');
    
    shortenForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const longUrl = longUrlInput.value.trim();
        
        if (!longUrl) {
            alert('Please enter a valid URL');
            return;
        }
        
        // Add http:// if not present
        let processedUrl = longUrl;
        if (!/^https?:\/\//i.test(processedUrl)) {
            processedUrl = 'http://' + processedUrl;
        }
        
        shortenBtn.disabled = true;
        shortenBtn.textContent = 'Shortening...';
        
        fetch('shorten.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ url: processedUrl })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                shortUrlSpan.textContent = data.short_url;
                resultContainer.classList.remove('hidden');
                
                // Scroll to result
                resultContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                alert(data.error || 'Failed to shorten URL');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while shortening the URL');
        })
        .finally(() => {
            shortenBtn.disabled = false;
            shortenBtn.textContent = 'Shorten';
        });
    });
    
    copyBtn.addEventListener('click', function() {
        const shortUrl = shortUrlSpan.textContent;
        
        navigator.clipboard.writeText(shortUrl)
            .then(() => {
                const originalText = copyBtn.querySelector('span').textContent;
                copyBtn.querySelector('span').textContent = 'Copied!';
                
                setTimeout(() => {
                    copyBtn.querySelector('span').textContent = originalText;
                }, 2000);
            })
            .catch(err => {
                console.error('Failed to copy: ', err);
                alert('Failed to copy URL to clipboard');
            });
    });
    
    // Handle any error messages from redirect
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error')) {
        alert('The shortened link you tried to access is invalid or expired.');
    }
});
