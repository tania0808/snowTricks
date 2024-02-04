document.addEventListener("DOMContentLoaded", function () {
    let container = document.getElementById("mediaContainer");
    let toggleButton = document.getElementById("toggleButton");

    if(toggleButton !== null) {
        toggleButton.addEventListener("click", function () {
            if (container.style.display === "none" || container.style.display === "") {
                container.style.display = "block";
                toggleButton.textContent = "Hide Medias";
            } else {
                container.style.display = "none";
                toggleButton.textContent = "See Medias";
            }
        });
    }
});