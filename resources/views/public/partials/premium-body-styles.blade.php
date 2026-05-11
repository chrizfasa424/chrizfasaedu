<style>
    :root {
        --fx-premium-ease: cubic-bezier(.22, .61, .36, 1);
    }

    .public-premium-kicker {
        font-family: "Space Grotesk", "Outfit", sans-serif;
        letter-spacing: 0.18em;
        text-transform: uppercase;
    }

    .public-premium-title {
        font-family: "Fraunces", "Outfit", serif;
        font-variation-settings: "SOFT" 60, "WONK" 0;
        letter-spacing: -0.02em;
        line-height: 1.04;
    }

    [data-reveal] {
        opacity: 0;
        transform: translate3d(0, 18px, 0);
        transition: opacity 0.68s var(--fx-premium-ease), transform 0.68s var(--fx-premium-ease);
        will-change: opacity, transform;
    }

    [data-reveal="left"] {
        transform: translate3d(-22px, 0, 0);
    }

    [data-reveal="right"] {
        transform: translate3d(22px, 0, 0);
    }

    [data-reveal].is-visible {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }

    .contact-vibrant-shell {
        border-color: rgba(148, 163, 184, 0.28) !important;
        border-radius: 2rem !important;
        background:
            radial-gradient(circle at 8% 6%, rgba(245, 158, 11, 0.18), transparent 38%),
            radial-gradient(circle at 92% 92%, rgba(20, 184, 166, 0.16), transparent 34%),
            linear-gradient(145deg, rgba(255, 255, 255, 0.98), rgba(250, 252, 248, 0.92)) !important;
        box-shadow: 0 30px 70px -44px rgba(15, 23, 42, 0.5) !important;
    }

    .contact-vibrant-panel {
        border-color: rgba(148, 163, 184, 0.24) !important;
        border-radius: 1.25rem !important;
        box-shadow: 0 20px 40px -34px rgba(15, 23, 42, 0.32);
    }

    .contact-vibrant-card {
        border-color: rgba(148, 163, 184, 0.24) !important;
        transition: transform 0.26s var(--fx-premium-ease), box-shadow 0.26s var(--fx-premium-ease);
    }

    .contact-vibrant-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 22px 40px -32px rgba(15, 23, 42, 0.45) !important;
    }

    .contact-vibrant-map-frame {
        border-color: rgba(148, 163, 184, 0.26) !important;
        border-radius: 1rem !important;
    }

    .submenu-main-card {
        border: 1px solid rgba(148, 163, 184, 0.24);
        background: linear-gradient(160deg, rgba(255, 255, 255, 0.98), rgba(248, 252, 248, 0.92));
        box-shadow: 0 30px 62px -44px rgba(15, 23, 42, 0.46);
    }

    .submenu-aside {
        border-color: rgba(37, 51, 62, 0.24) !important;
        background: linear-gradient(165deg, rgba(255, 255, 255, 0.98), rgba(247, 251, 248, 0.92)) !important;
        box-shadow: 0 24px 50px -40px rgba(15, 23, 42, 0.48) !important;
    }

    .submenu-aside > div:first-child {
        background: linear-gradient(140deg, color-mix(in srgb, var(--submenu-secondary, #DFE753) 86%, #ffffff 14%), color-mix(in srgb, var(--submenu-secondary, #DFE753) 62%, #f3f4f6 38%)) !important;
        color: color-mix(in srgb, var(--submenu-hover-text, #25333E) 94%, #111827 6%) !important;
    }

    .submenu-aside .sidebar-program-link {
        border: 1px solid rgba(148, 163, 184, 0.34) !important;
        background: linear-gradient(155deg, #ffffff, #f8fbf8) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }

    .submenu-aside .sidebar-program-link-title {
        color: #0f172a !important;
    }

    .submenu-aside .sidebar-program-link-meta {
        color: #64748b !important;
    }

    .submenu-aside .sidebar-program-link:hover,
    .submenu-aside .sidebar-program-link:focus-visible,
    .submenu-aside .sidebar-program-link-active {
        border-color: color-mix(in srgb, var(--submenu-primary, #25333E) 74%, white 26%) !important;
        background: linear-gradient(145deg, color-mix(in srgb, var(--submenu-primary, #25333E) 90%, #172033 10%), color-mix(in srgb, var(--submenu-primary, #25333E) 74%, #22323C 26%)) !important;
        box-shadow: 0 14px 30px -22px rgba(15, 23, 42, 0.62) !important;
    }

    .submenu-aside .sidebar-program-link:hover .sidebar-program-link-title,
    .submenu-aside .sidebar-program-link:focus-visible .sidebar-program-link-title,
    .submenu-aside .sidebar-program-link-active .sidebar-program-link-title {
        color: #ffffff !important;
    }

    .submenu-aside .sidebar-program-link:hover .sidebar-program-link-meta,
    .submenu-aside .sidebar-program-link:focus-visible .sidebar-program-link-meta,
    .submenu-aside .sidebar-program-link-active .sidebar-program-link-meta {
        color: rgba(255, 255, 255, 0.8) !important;
    }

    .submenu-aside .mt-4.rounded-2xl.border {
        border-color: rgba(148, 163, 184, 0.34) !important;
        background: linear-gradient(155deg, rgba(240, 247, 245, 0.88), rgba(255, 255, 255, 0.95)) !important;
        box-shadow: 0 16px 28px -24px rgba(15, 23, 42, 0.36);
    }

    .submenu-aside .mt-4.rounded-2xl.border a {
        border: 1px solid rgba(148, 163, 184, 0.42) !important;
        background: rgba(255, 255, 255, 0.9) !important;
        color: #1f2937 !important;
    }

    .submenu-aside .mt-4.rounded-2xl.border a:hover,
    .submenu-aside .mt-4.rounded-2xl.border a:focus-visible {
        border-color: color-mix(in srgb, var(--submenu-primary, #25333E) 62%, white 38%) !important;
        background: color-mix(in srgb, var(--submenu-secondary, #DFE753) 38%, #ffffff 62%) !important;
        color: var(--submenu-hover-text, #25333E) !important;
    }

    .submenu-main-card .rich-text-content h1,
    .submenu-main-card .rich-text-content h2,
    .submenu-main-card .rich-text-content h3,
    .submenu-main-card .rich-text-content h4 {
        font-family: "Fraunces", "Outfit", serif;
        letter-spacing: -0.01em;
        line-height: 1.12;
    }

    .submenu-feature-image-card {
        border-color: rgba(148, 163, 184, 0.22) !important;
        box-shadow: 0 18px 34px -28px rgba(15, 23, 42, 0.45) !important;
    }

    .legal-hero-shell {
        border-bottom: 1px solid rgba(148, 163, 184, 0.2);
        background:
            radial-gradient(circle at 12% 0%, rgba(245, 158, 11, 0.2), transparent 36%),
            linear-gradient(160deg, #fdfefb 0%, #f2f8f4 100%) !important;
    }

    .legal-content-card {
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 1.5rem !important;
        box-shadow: 0 24px 56px -40px rgba(15, 23, 42, 0.42);
        background: linear-gradient(160deg, rgba(255, 255, 255, 0.98), rgba(248, 252, 248, 0.92)) !important;
    }

    @media (prefers-reduced-motion: reduce) {
        [data-reveal],
        [data-reveal="left"],
        [data-reveal="right"] {
            opacity: 1;
            transform: none;
            transition: none;
        }
    }
</style>
