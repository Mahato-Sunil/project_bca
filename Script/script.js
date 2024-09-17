
// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', () => {
    // nav bar operning for the button for landing  page 
    const menuOpen = document.querySelector('#openMenu');
    const menuClose = document.querySelector('#closeMenu');

    menuOpen.addEventListener('click', () => {
        document.querySelector('.navbar-mobile').style.display = 'flex';
    });

    menuClose.addEventListener('click', () => {
        document.querySelector('.navbar-mobile').style.display = 'none';
        document.querySelector('.navbar').style.display = 'flex';
    });
});

