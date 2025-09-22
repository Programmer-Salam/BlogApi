@extends('layout')

@section('title', 'Post - Mini Blog')

@section('content')
    <div id="loading" class="text-center">
        <div class="spinner"></div>
        <p style="margin-top: 1rem; color: #6b7280;">Loading post...</p>
    </div>

    <div id="postContainer" class="hidden">
        <div class="card">
            <a href="/" style="color: #3b82f6; text-decoration: none; margin-bottom: 1rem; display: inline-block;">
                ← Back to all posts
            </a>

            <div id="postContent">
                <!-- Post content will be loaded here -->
            </div>
        </div>
    </div>

    <div id="errorContainer" class="hidden text-center">
        <p style="color: #dc2626; margin-bottom: 1rem;" id="errorMessage"></p>
        <a href="/" style="color: #3b82f6; text-decoration: none;">← Back to all posts</a>
    </div>
@endsection

@section('scripts')
<script>
    // Get post ID from URL
    const pathParts = window.location.pathname.split('/');
    const postId = pathParts[pathParts.length - 1];

    // Load post on page load
    document.addEventListener('DOMContentLoaded', loadPost);

    async function loadPost() {
        showLoading();

        try {
            const response = await fetch(`${API_BASE}/posts/${postId}`);
            const data = await response.json();

            console.log('Single post response:', data);

            if (response.ok) {
                // Post is directly in data (not data.data)
                const post = data.data;
                displayPost(post);
            } else {
                throw new Error(data.message || 'Post not found');
            }
        } catch (error) {
            showError(error.message);
        }
    }

    function displayPost(post) {
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('errorContainer').classList.add('hidden');

        const container = document.getElementById('postContainer');
        const content = document.getElementById('postContent');

        content.innerHTML = `
            <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 1rem; color: #1f2937;">
                ${post.title}
            </h1>

            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem; color: #6b7280;">
                <span>By ${post.author.name}</span>
                <span>•</span>
                <span>${new Date(post.created_at).toLocaleDateString()}</span>
            </div>

            <div style="line-height: 1.8; color: #4b5563; white-space: pre-line;">
                ${post.body}
            </div>
        `;

        container.classList.remove('hidden');
    }

    function showLoading() {
        document.getElementById('loading').classList.remove('hidden');
        document.getElementById('postContainer').classList.add('hidden');
        document.getElementById('errorContainer').classList.add('hidden');
    }

    function showError(message) {
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('postContainer').classList.add('hidden');

        const errorContainer = document.getElementById('errorContainer');
        document.getElementById('errorMessage').textContent = message;
        errorContainer.classList.remove('hidden');
    }
</script>
@endsection
