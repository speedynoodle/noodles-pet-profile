/* app.js – minimal JavaScript for Noodle's Pet Profiles */

// Highlight the current nav link
document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('.site-nav a');
    links.forEach(link => {
        if (link.href === window.location.href) {
            link.style.background = 'var(--color-accent)';
            link.style.color = 'var(--color-primary-dk)';
        }
    });
});
