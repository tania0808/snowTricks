        function loadTricks(tricksCount) {
        let loadedTricksCount = Math.max(0, Math.min(6, tricksCount || 0));
        const url = '/tricks-partial/' + loadedTricksCount;

        fetch(url)
        .then(response => response.text())
        .then(html => {
        document.getElementById('tricks').innerHTML += html;
        const tricks = document.querySelectorAll('.trick-card');
        const nextButton = document.querySelector('#next_button');

        if(tricks.length === tricksCount) {
        nextButton.style.display = 'none';
    }
        initFlowbite();
        scrollToTricks();

        loadedTricksCount = tricks.length;
    })
        .catch(error => {
        console.error('Error loading tricks:', error);
    });
    }

    function scrollToTricks() {
        var tricksElement = document.getElementById('tricks');
        if (tricksElement) {
            tricksElement.scrollIntoView({ behavior: 'instant' });
        }
    }