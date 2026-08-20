import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/**
 * Toggle like on a post via AJAX.
 * Called by onclick="toggleLike(postId)" on the like button.
 */
window.toggleLike = function (postId) {
    const btn   = document.getElementById(`like-btn-${postId}`);
    const label = document.getElementById(`like-label-${postId}`);
    const icon  = btn.querySelector('svg');
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/community/${postId}/like`, {
        method:  'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept':       'application/json',
            'Content-Type': 'application/json',
        },
    })
    .then(res => res.json())
    .then(data => {
        if (data.liked) {
            btn.classList.add('text-comsoc-600');
            btn.classList.remove('text-gray-500');
            icon.setAttribute('fill', 'currentColor');
            label.textContent = `Liked (${data.count})`;
        } else {
            btn.classList.remove('text-comsoc-600');
            btn.classList.add('text-gray-500');
            icon.setAttribute('fill', 'none');
            label.textContent = data.count > 0 ? `Like (${data.count})` : 'Like';
        }
    })
    .catch(() => {
        // Silently fail — form fallback not needed for likes
    });
};
