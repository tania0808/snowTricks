document.addEventListener('DOMContentLoaded', function() {
    const addOpenModal = (route) => {
        console.log('event',event.currentTarget)
        console.log('route', route)
        const deleteButton = event.currentTarget;
        const dataIdValue = deleteButton.getAttribute('data-id');

        const deleteLink = document.querySelector('#delete-link');
        if (deleteLink) {
            console.log(deleteLink.href)
            deleteLink.href = route + dataIdValue;
        }

        const mediaIdInput = document.querySelector('#media-id-input');
        if (mediaIdInput) {
            mediaIdInput.value = dataIdValue;
        }
    }
    document.querySelectorAll('#delete-button').forEach(btn => btn.addEventListener('click', addOpenModal));

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