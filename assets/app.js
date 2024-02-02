document.addEventListener('DOMContentLoaded', function() {
    const addOpenDeleteModal = (event) => {
        const deleteButton = event.currentTarget;
        const dataIdValue = deleteButton.getAttribute('data-id');

        const deleteLink = document.querySelector('#delete-link');
        if (deleteLink) {
            deleteLink.href = '/media/delete/' + dataIdValue;
        }
    }
    document.querySelectorAll('#delete-media-button').forEach(btn => btn.addEventListener('click', addOpenDeleteModal));
    const addNewVideo = (e) => {
        const videosUrlCollection = document.querySelector(e.currentTarget.dataset.collection);

        const item = document.createElement('div');
        item.className = 'mb-5 video-item flex items-center gap-2';
        item.innerHTML = videosUrlCollection.dataset.prototype.replace(/__name__/g, videosUrlCollection.dataset.index);

        item.querySelector('.btn-remove').addEventListener('click', () => item.remove());
        videosUrlCollection.appendChild(item);

        videosUrlCollection.dataset.index++;
    }

    document.querySelectorAll('.btn-remove').forEach(btn => btn.addEventListener('click', (e) => e.currentTarget.closest('.video-item').remove()));
    document.querySelectorAll('.btn-new').forEach(btn => btn.addEventListener('click', addNewVideo));
});