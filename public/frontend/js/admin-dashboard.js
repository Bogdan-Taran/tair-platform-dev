const menuToggle = document.querySelector('.menu-toggle');
const mainNav = document.querySelector('.main-nav');
if (menuToggle && mainNav) {
    menuToggle.setAttribute('aria-expanded', 'false');
    menuToggle.addEventListener('click', () => {
        const expanded = menuToggle.getAttribute('aria-expanded') !== 'true';
        menuToggle.setAttribute('aria-expanded', String(expanded));
        menuToggle.classList.toggle('active', expanded);
        mainNav.classList.toggle('active', expanded);
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            menuToggle.setAttribute('aria-expanded', 'false');
            menuToggle.classList.remove('active');
            mainNav.classList.remove('active');
        }
    });
}
document.querySelector('#admin-sort')?.addEventListener('change', (event) => event.target.form.requestSubmit());
