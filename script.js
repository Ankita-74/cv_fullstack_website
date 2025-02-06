// ==========for read more button=========
document.querySelectorAll('.read-more-btn').forEach(button => {
    button.addEventListener('click', () => {
        const extraContent = button.previousElementSibling;
        if (extraContent.style.display === 'none') {
            extraContent.style.display = 'block';
            button.textContent = 'Read Less';
        } else {
            extraContent.style.display = 'none';
            button.textContent = 'Read More';
        }
    });
});
// ============media header========
document.querySelector('.toggle-button').addEventListener('click', function() {
    const nav = document.querySelector('.navigation');
    nav.classList.toggle('active');
});
// Close navigation menu when a link is clicked
document.querySelectorAll('.navigation a').forEach(link => {
    link.addEventListener('click', function() {
        const nav = document.querySelector('.navigation');
        nav.classList.remove('active'); // Hide the menu when a link is clicked
    });
});
