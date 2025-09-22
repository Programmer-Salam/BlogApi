@extends('layout')

@section('title', 'Login - Mini Blog')

@section('content')
    <div style="max-width: 400px; margin: 2rem auto;">
        <div class="card">
            <h2 style="text-align: center; margin-bottom: 2rem; color: #1f2937;">Login</h2>

            <form id="loginForm">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" id="email" class="form-input" required>
                    <span id="emailError" class="text-error hidden"></span>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" id="password" class="form-input" required>
                    <span id="passwordError" class="text-error hidden"></span>
                </div>

                <button type="submit" class="btn" style="width: 100%;">Login</button>
            </form>

            <p style="text-align: center; margin-top: 1rem; color: #6b7280;">
                Don't have an account? <a href="/register" style="color: #3b82f6;">Register</a>
            </p>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;

        try {
            // Clear previous errors
            clearErrors();
            submitBtn.disabled = true;
            submitBtn.textContent = 'Logging in...';

            const formData = {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value
            };

            const response = await fetch(`${API_BASE}/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            console.log('Login response:', data);

            if (response.ok && data.status === 'success') {
                // Store token and user data
                localStorage.setItem('authToken', data.data.token);
                localStorage.setItem('user', JSON.stringify(data.data.user));

                showNotification(data.message);

                // Update navigation immediately
                updateNavigation();

                // Redirect to home after a brief delay
                setTimeout(() => {
                    window.location.href = '/';
                }, 1000);
            } else {
                // Handle error response
                if (data.message) {
                    throw new Error(data.message);
                } else {
                    throw new Error('Login failed');
                }
            }
        } catch (error) {
            console.error('Login error:', error);
            showNotification(error.message, 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });

    function clearErrors() {
        document.querySelectorAll('[id$="Error"]').forEach(element => {
            element.classList.add('hidden');
        });
    }

    // Check if already logged in
    document.addEventListener('DOMContentLoaded', function() {
        if (isLoggedIn()) {
            showNotification('You are already logged in!', 'error');
            setTimeout(() => {
                window.location.href = '/';
            }, 2000);
        }
    });
</script>
@endsection
