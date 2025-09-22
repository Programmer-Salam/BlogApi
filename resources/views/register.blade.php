@extends('layout')

@section('title', 'Register - Mini Blog')

@section('content')
    <div style="max-width: 400px; margin: 2rem auto;">
        <div class="card">
            <h2 style="text-align: center; margin-bottom: 2rem; color: #1f2937;">Register</h2>

            <form id="registerForm">
                <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text" id="name" class="form-input" required>
                    <span id="nameError" class="text-error hidden"></span>
                </div>

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

                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" id="passwordConfirmation" class="form-input" required>
                </div>

                <button type="submit" class="btn" style="width: 100%;">Register</button>
            </form>

            <p style="text-align: center; margin-top: 1rem; color: #6b7280;">
                Already have an account? <a href="/login" style="color: #3b82f6;">Login</a>
            </p>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.getElementById('registerForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;

        try {
            clearErrors();
            submitBtn.disabled = true;
            submitBtn.textContent = 'Registering...';

            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('passwordConfirmation').value
            };

            const response = await fetch(`${API_BASE}/register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            console.log('Register response:', data);

            if (response.ok && data.status === 'success') {
                // Store token and user data
                localStorage.setItem('authToken', data.data.token);
                localStorage.setItem('user', JSON.stringify(data.data.user));

                showNotification(data.message);

                setTimeout(() => {
                    window.location.href = '/';
                }, 1000);
            } else {
                // Handle error response
                if (data.message) {
                    throw new Error(data.message);
                } else {
                    throw new Error('Registration failed');
                }
            }
        } catch (error) {
            console.error('Register error:', error);
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
</script>
@endsection
