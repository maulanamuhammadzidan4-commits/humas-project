
const menuToggle = document.getElementById('menuToggle');
const navMenu = document.getElementById('navMenu');
const toggleIcon = document.getElementById('toggleIcon');

menuToggle.addEventListener('click', () => {
    navMenu.classList.toggle('active');
    if (navMenu.classList.contains('active')) {
        toggleIcon.classList.remove('fa-bars');
        toggleIcon.classList.add('fa-xmark');
    } else {
        toggleIcon.classList.remove('fa-xmark');
        toggleIcon.classList.add('fa-bars');
    }
});

// Close mobile drawer when clicking a link
document.querySelectorAll('.menu a').forEach(link => {
    link.addEventListener('click', () => {
        navMenu.classList.remove('active');
        toggleIcon.classList.remove('fa-xmark');
        toggleIcon.classList.add('fa-bars');
    });
});