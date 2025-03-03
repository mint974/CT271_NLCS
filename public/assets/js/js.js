
document.addEventListener('DOMContentLoaded', function () {
    var heartIcons = document.querySelectorAll('.heart-icon');

    heartIcons.forEach(function (icon) {
        icon.addEventListener('click', function () {
            this.classList.toggle('clicked');
        });
    });
});

// JavaScript to scroll to the top of the page
document.getElementById('back-to-top').addEventListener('click', function() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});
