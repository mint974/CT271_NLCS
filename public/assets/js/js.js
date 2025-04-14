

// JavaScript to scroll to the top of the page
document.getElementById('back-to-top').addEventListener('click', function() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});



function resizeCanvas() {
    canvas.width = canvas.parentElement.offsetWidth;
    canvas.height = canvas.parentElement.offsetHeight;
    animateBackground();
}

// Vẽ nền có hiệu ứng uống lượn (3s)
function animateBackground() {
    const w = canvas.width;
    const h = canvas.height;
    let progress = 0;
    const duration = 3000;
    const start = performance.now();

    function drawFrame(timestamp) {
        progress = (timestamp - start) / duration;
        if (progress > 1) progress = 1;

        ctx.clearRect(0, 0, w, h);

        ctx.fillStyle = "#29bf12";
        ctx.beginPath();
        ctx.moveTo(w * (0.6 - 0.2 * (1 - progress)), 0);
        ctx.quadraticCurveTo(
            w * (0.95 - 0.15 * (1 - progress)), h * 0.1,
            w * (0.9 - 0.1 * (1 - progress)), h * 0.4
        );
        ctx.quadraticCurveTo(
            w * (0.8 - 0.05 * (1 - progress)), h * 0.8,
            w, h
        );
        ctx.lineTo(w, 0);
        ctx.closePath();
        ctx.fill();

        if (progress < 1) {
            requestAnimationFrame(drawFrame);
        }
    }

    requestAnimationFrame(drawFrame);
}

window.addEventListener('resize', resizeCanvas);
resizeCanvas();