@extends('layout')

@section('title', 'Mini Blog - Home')

@section('content')
    <div class="card">
        <div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
            <input type="text" id="searchInput" placeholder="Search posts..."
                   style="flex: 1; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            <button onclick="searchPosts()" class="btn">Search</button>
        </div>

        <h1 style="text-align: center; margin-bottom: 2rem; color: #1f2937;">All Blog Posts</h1>

        <div id="loading" class="text-center">
            <div class="spinner"></div>
            <p style="margin-top: 1rem; color: #6b7280;">Loading posts...</p>
        </div>

        <div id="postsContainer" class="hidden">
            <!-- Posts will be loaded here -->
        </div>

        <div id="noPosts" class="hidden text-center">
            <p style="color: #6b7280; font-size: 1.125rem;">No posts found.</p>
        </div>

        <div id="pagination" class="pagination hidden"></div>
    </div>
@endsection

@section('scripts')
<script>
    let currentPage = 1;
    let currentSearch = '';

    // Load posts on page load
    document.addEventListener('DOMContentLoaded', loadPosts);

    async function loadPosts(page = 1, search = '') {
        showLoading();

        try {
            const params = new URLSearchParams();
            if (page > 1) params.append('page', page);
            if (search) params.append('search', search);

            const response = await fetch(`${API_BASE}/posts?${params}`);
            const data = await response.json();

            console.log('Posts response:', data);

            if (response.ok) {
                // Posts are directly in data.data array
                const posts = data.data;
                displayPosts(posts);

                // Render pagination if available
                if (data.meta) {
                    renderPagination(data.meta);
                }
            } else {
                throw new Error(data.message || 'Failed to load posts');
            }
        } catch (error) {
            console.error('Load posts error:', error);
            showNotification(error.message, 'error');
            showNoPosts();
        }
    }

    function displayPosts(posts) {
        const container = document.getElementById('postsContainer');
        const loading = document.getElementById('loading');
        const noPosts = document.getElementById('noPosts');

        loading.classList.add('hidden');

        if (!posts || posts.length === 0) {
            showNoPosts();
            return;
        }

        noPosts.classList.add('hidden');
        container.classList.remove('hidden');

        const currentUser = getCurrentUser();

        container.innerHTML = posts.map(post => {
            const isOwner = currentUser && post.author.id === currentUser.id;

            return `
                <div class="card" style="margin-bottom: 1.5rem; position: relative;">
                    ${isOwner ? `
                        <div style="position: absolute; top: 1rem; right: 1rem; display: flex; gap: 0.5rem;">
                            <button onclick="editPost(${post.id})" class="nav-link" style="padding: 0.25rem; background: #fbbf24; color: white; border-radius: 0.25rem;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deletePost(${post.id})" class="nav-link" style="padding: 0.25rem; background: #ef4444; color: white; border-radius: 0.25rem;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    ` : ''}

                    <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem; padding-right: ${isOwner ? '80px' : '0'};">
                        <a href="/post/${post.id}" style="color: #1f2937; text-decoration: none;">
                            ${post.title}
                        </a>
                    </h2>

                    <p style="color: #6b7280; margin-bottom: 1rem; line-height: 1.6;">
                        ${post.body.substring(0, 200)}${post.body.length > 200 ? '...' : ''}
                    </p>

                    <div style="display: flex; justify-content: space-between; align-items: center; color: #9ca3af; font-size: 0.875rem;">
                        <span>By ${post.author.name}</span>
                        <span>${new Date(post.created_at).toLocaleDateString()}</span>
                    </div>
                </div>
            `;
        }).join('');
    }

    function renderPagination(meta) {
        const pagination = document.getElementById('pagination');

        if (meta.last_page <= 1) {
            pagination.classList.add('hidden');
            return;
        }

        pagination.classList.remove('hidden');

        let paginationHTML = '';

        // Previous button
        if (meta.current_page > 1) {
            paginationHTML += `<button onclick="loadPage(${meta.current_page - 1})" class="page-btn">Previous</button>`;
        }

        // Page numbers
        for (let i = 1; i <= meta.last_page; i++) {
            if (i === meta.current_page) {
                paginationHTML += `<button class="page-btn active">${i}</button>`;
            } else {
                paginationHTML += `<button onclick="loadPage(${i})" class="page-btn">${i}</button>`;
            }
        }

        // Next button
        if (meta.current_page < meta.last_page) {
            paginationHTML += `<button onclick="loadPage(${meta.current_page + 1})" class="page-btn">Next</button>`;
        }

        pagination.innerHTML = paginationHTML;
    }

    function loadPage(page) {
        currentPage = page;
        loadPosts(page, currentSearch);
    }

    async function editPost(postId) {
        if (!isLoggedIn()) {
            showNotification('Please login to edit posts', 'error');
            return;
        }

        // Redirect to edit page or show edit modal
        // For now, let's create a simple edit modal
        showEditModal(postId);
    }

    async function deletePost(postId) {
        if (!isLoggedIn()) {
            showNotification('Please login to delete posts', 'error');
            return;
        }

        if (confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
            try {
                const response = await fetch(`${API_BASE}/user/posts/${postId}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${getAuthToken()}`,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    showNotification(data.message || 'Post deleted successfully');
                    // Reload posts after deletion
                    setTimeout(() => {
                        loadPosts(currentPage, currentSearch);
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Failed to delete post');
                }
            } catch (error) {
                console.error('Delete post error:', error);
                showNotification(error.message, 'error');
            }
        }
    }

    function showEditModal(postId) {
        // Simple prompt-based edit for now
        const newTitle = prompt('Enter new title:');
        if (newTitle) {
            const newBody = prompt('Enter new content:');
            if (newBody) {
                updatePost(postId, newTitle, newBody);
            }
        }
    }

    async function updatePost(postId, title, body) {
        try {
            const response = await fetch(`${API_BASE}/user/posts/${postId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${getAuthToken()}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ title, body })
            });

            const data = await response.json();

            if (response.ok && data.status === 'success') {
                showNotification(data.message || 'Post updated successfully');
                // Reload posts after update
                setTimeout(() => {
                    loadPosts(currentPage, currentSearch);
                }, 1000);
            } else {
                throw new Error(data.message || 'Failed to update post');
            }
        } catch (error) {
            console.error('Update post error:', error);
            showNotification(error.message, 'error');
        }
    }

    function showLoading() {
        document.getElementById('loading').classList.remove('hidden');
        document.getElementById('postsContainer').classList.add('hidden');
        document.getElementById('noPosts').classList.add('hidden');
        document.getElementById('pagination').classList.add('hidden');
    }

    function showNoPosts() {
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('postsContainer').classList.add('hidden');
        document.getElementById('noPosts').classList.remove('hidden');
        document.getElementById('pagination').classList.add('hidden');
    }

    function searchPosts() {
        currentSearch = document.getElementById('searchInput').value;
        currentPage = 1;
        loadPosts(1, currentSearch);
    }
</script>
@endsection
