@extends('layout')

@section('title', 'Create Post - Mini Blog')

@section('content')
    <div style="max-width: 600px; margin: 2rem auto;">
        <div class="card">
            <h2 style="text-align: center; margin-bottom: 2rem; color: #1f2937;">Create New Post</h2>

            <form id="createPostForm">
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" id="title" class="form-input" required>
                    <span id="titleError" class="text-error hidden"></span>
                </div>

                <div class="form-group">
                    <label class="form-label">Content</label>
                    <textarea id="body" class="form-input form-textarea" required></textarea>
                    <span id="bodyError" class="text-error hidden"></span>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn">Create Post</button>
                    <a href="/" class="btn" style="background: #6b7280;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>

    document.addEventListener('DOMContentLoaded', function() {
        if (!isLoggedIn()) {
            showNotification('Please login to create a post', 'error');
            setTimeout(() => {
                window.location.href = '/login';
            }, 2000);

            const submitBtn = document.getElementById('createPostForm').querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;
        }
    });

    document.getElementById('createPostForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        if (!isLoggedIn()) {
            showNotification('Please login first', 'error');
            return;
        }

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;

        try {
            clearErrors();
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating...';

            const formData = {
                title: document.getElementById('title').value,
                body: document.getElementById('body').value
            };

            const response = await fetch(`${API_BASE}/user/posts`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${getAuthToken()}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            console.log('Create post response:', data);

            if (response.ok && data.status === 'success') {
                showNotification(data.message);

                setTimeout(() => {
                    window.location.href = `/`;
                }, 1000);
            } else {
                if (data.message) {
                    throw new Error(data.message);
                } else {
                    throw new Error('Failed to create post');
                }
            }
        } catch (error) {
            console.error('Create post error:', error);
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
