document.addEventListener("DOMContentLoaded", function() {
    let loadedCommentsCount;

    function loadComments() {
        const commentsElement = document.getElementById('comments');
        const trickId = commentsElement.dataset.trickId;
        const commentCount = Number(commentsElement.dataset.commentCount);
        loadedCommentsCount = Math.max(0, Math.min(3, commentCount || 0));

        const url = '/comments-partial/' + loadedCommentsCount + '/' + trickId;

        fetch(url)
            .then(response => response.text())
            .then(html => {
                document.getElementById('comments').innerHTML += html;
                const comments = document.querySelectorAll('.comment_card');
                const nextButton = document.querySelector('#next_comment_button');

                if (comments.length === commentCount) {
                    nextButton.style.display = 'none';
                }
                initFlowbite();
                scrollToTricks();

                loadedCommentsCount = comments.length;
            })
            .catch(error => {
                console.error('Error loading tricks:', error);
            });
    }

    function scrollToTricks() {
        var commentsElement = document.getElementById('comments');
        if (commentsElement) {
            commentsElement.scrollIntoView({ behavior: 'instant' });
        }
    }

    // Make the loadComments function globally accessible
    window.loadComments = loadComments;
});