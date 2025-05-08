document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.delete-image').forEach(icon => {
        icon.addEventListener('click', function () {
            const imageId = this.getAttribute('data-id');
            const imageUrl = this.getAttribute('data-url');
            if (!confirm('Are you sure you want to delete this image?')) return;

            fetch('delete-images.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ image_id: imageId, image_url: imageUrl })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showResponseMessage(data.message);
                    const wrapper = this.closest('.image-wrapper');
                    if (wrapper) wrapper.remove();
                } else {
                    showResponseMessage(data.message);
                }
            })
            .catch(err => {
                console.error('Delete failed:', err);
                alert('An error occurred while deleting the image.');
            });
        });
    });
});
