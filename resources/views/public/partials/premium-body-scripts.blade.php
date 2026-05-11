@once
<script>
    (function () {
        const revealItems = Array.from(document.querySelectorAll('[data-reveal]'));
        if (!revealItems.length) {
            return;
        }

        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (prefersReducedMotion) {
            revealItems.forEach((item) => item.classList.add('is-visible'));
            return;
        }

        const observer = new IntersectionObserver((entries, io) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('is-visible');
                io.unobserve(entry.target);
            });
        }, {
            root: null,
            rootMargin: '0px 0px -8% 0px',
            threshold: 0.14,
        });

        revealItems.forEach((item, index) => {
            item.style.transitionDelay = `${Math.min(index * 28, 240)}ms`;
            observer.observe(item);
        });
    })();
</script>
@endonce
