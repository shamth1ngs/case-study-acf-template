document.addEventListener('DOMContentLoaded', function () {
    const animatedEls = document.querySelectorAll('.cs-animate-up');

    if (!animatedEls.length) return;

    // Fallback: if IntersectionObserver isn't supported, just show them
    if (!('IntersectionObserver' in window)) {
        animatedEls.forEach(el => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');

                // IMPORTANT: stop observing so it only happens once
                obs.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.25 // trigger when ~25% is in view
    });

    animatedEls.forEach((el, index) => {
        // Optional: small stagger effect
        el.style.setProperty('--cs-delay', (index * 0.08) + 's');
        observer.observe(el);
    });
});
