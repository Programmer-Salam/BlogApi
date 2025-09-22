<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mini Blog')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset and Base Styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8fafc;
        }

        /* Navigation */
        .navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #3b82f6;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .nav-link {
            color: #6b7280;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: #3b82f6;
            background-color: #f3f4f6;
        }

        .btn {
            background: #3b82f6;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #2563eb;
        }

        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        /* Forms */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 1rem;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-textarea {
            min-height: 150px;
            resize: vertical;
        }

        /* Utilities */
        .hidden { display: none; }
        .text-center { text-align: center; }
        .text-error { color: #dc2626; }
        .text-success { color: #16a34a; }

        /* Loading Spinner */
        .spinner {
            border: 4px solid #f3f4f6;
            border-left: 4px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 2rem auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .page-btn {
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            background: white;
            border-radius: 0.375rem;
            cursor: pointer;
        }

        .page-btn:hover:not(.disabled) {
            background: #f3f4f6;
        }

        .page-btn.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        .page-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Notification */
        .notification {
            position: fixed;
            top: 1rem;
            right: 1rem;
            padding: 1rem;
            border-radius: 0.375rem;
            color: white;
            z-index: 1000;
            animation: slideIn 0.3s ease-out;
        }

        .notification.success { background: #16a34a; }
        .notification.error { background: #dc2626; }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="/" class="nav-logo">Mini Blog</a>
            <div class="nav-links" id="navLinks">
                <a href="/" class="nav-link">Home</a>
                <span id="authLinks">
                    <a href="/login" class="nav-link">Login</a>
                    <a href="/register" class="nav-link">Register</a>
                </span>
                <span id="userLinks" class="hidden">
                    <span class="nav-link" style="color: #3b82f6;" id="userName"></span>
                    <a href="/create" class="nav-link">Create Post</a>
                    <button onclick="logout()" class="nav-link" style="background: none; border: none; cursor: pointer;">Logout</button>
                </span>
            </div>
        </div>
    </nav>

    <div class="container">
        <div id="notification" class="notification hidden"></div>
        @yield('content')
    </div>

    <script>
        // API Configuration - Define these at the top
        const API_BASE = '/api/v1';

        // Authentication functions
        function getAuthToken() {
            return localStorage.getItem('authToken');
        }

        function getCurrentUser() {
            const user = localStorage.getItem('user');
            return user ? JSON.parse(user) : null;
        }

        function isLoggedIn() {
            return !!getAuthToken() && !!getCurrentUser();
        }

        // Update navigation based on auth status
        function updateNavigation() {
            const authLinks = document.getElementById('authLinks');
            const userLinks = document.getElementById('userLinks');
            const userName = document.getElementById('userName');

            if (isLoggedIn()) {
                const user = getCurrentUser();
                if (authLinks) authLinks.classList.add('hidden');
                if (userLinks) userLinks.classList.remove('hidden');
                if (userName) userName.textContent = `Welcome, ${user.name}`;
            } else {
                if (authLinks) authLinks.classList.remove('hidden');
                if (userLinks) userLinks.classList.add('hidden');
            }
        }

        // Notification system
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            if (notification) {
                notification.textContent = message;
                notification.className = `notification ${type}`;
                notification.classList.remove('hidden');

                setTimeout(() => {
                    notification.classList.add('hidden');
                }, 3000);
            }
        }

        // Logout function
        async function logout() {
            try {
                const token = getAuthToken();
                if (token) {
                    await fetch(`${API_BASE}/logout`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json'
                        }
                    });
                }
            } catch (error) {
                console.log('Logout API call failed');
            } finally {
                localStorage.removeItem('authToken');
                localStorage.removeItem('user');
                updateNavigation();
                window.location.href = '/';
            }
        }

        // Initialize navigation on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateNavigation();
        });
    </script>

    @yield('scripts')
</body>
</html>
