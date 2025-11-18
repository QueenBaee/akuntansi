// Simple authentication helper for API calls
document.addEventListener('DOMContentLoaded', function() {
    // Check if we have a token, if not try to get one
    if (!localStorage.getItem('token')) {
        // For development, we'll use a simple approach
        // In production, this should be handled properly through login
        fetch('/api/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                email: 'admin@example.com',
                password: 'password'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.token) {
                localStorage.setItem('token', data.token);
            }
        })
        .catch(error => {
            console.error('Auth error:', error);
        });
    }
});