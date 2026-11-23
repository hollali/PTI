document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const mobileNav = document.getElementById('mobileNav');
    if (menuToggle && mobileNav) {
        menuToggle.addEventListener('click', function() {
            mobileNav.classList.toggle('hidden');
        });
    }

    const scrollBtn = document.getElementById('scrollTop');
    if (scrollBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                scrollBtn.classList.remove('hidden');
            } else {
                scrollBtn.classList.add('hidden');
            }
        });
        scrollBtn.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    const header = document.getElementById('mainHeader');
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    const revealEls = document.querySelectorAll('.reveal');
    if (revealEls.length) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        revealEls.forEach(el => observer.observe(el));
    }
});
