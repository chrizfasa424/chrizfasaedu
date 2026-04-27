{{-- Public nav CSS - include in <head> before Tailwind --}}
<style>
    :root {
        --theme-focus: rgba(15, 118, 110, 0.25);
    }

    body {
        text-align: justify;
        text-justify: inter-word;
    }

    .bg-pattern-grid {
        background-color: #22323C;
        background-image:
            linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
        background-size: 40px 40px;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        text-align: left;
        text-justify: auto;
    }

    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
            scroll-behavior: auto !important;
        }
    }

    .theme-nav-link {
        color: #334155;
    }

    @keyframes fx-menu-hover-blink {
        0%, 49% {
            box-shadow: none;
        }
        50%, 100% {
            box-shadow: 0 0 0 1px color-mix(in srgb, var(--submenu-secondary, #DFE753) 78%, transparent),
                        0 0 15px color-mix(in srgb, var(--submenu-secondary, #DFE753) 40%, transparent);
        }
    }

    .theme-nav-link:hover,
    .theme-nav-link:focus-visible {
        background-color: var(--submenu-secondary, #DFE753) !important;
        color: var(--submenu-hover-text, #2D1D5C) !important;
    }

    .theme-nav-link-active {
        background-color: var(--submenu-primary, #2D1D5C) !important;
        color: #ffffff !important;
    }

    .theme-nav-link:hover,
    .theme-nav-link:focus-visible,
    .theme-submenu-link:hover,
    .theme-submenu-link:focus-visible,
    .theme-mobile-submenu-link:hover,
    .theme-mobile-submenu-link:focus-visible {
        animation: fx-menu-hover-blink 0.14s steps(2, end) infinite;
    }

    .theme-header-action-outline {
        border-color: rgba(255, 255, 255, 0.55);
        color: #ffffff;
        backdrop-filter: blur(8px);
    }

    .theme-header-action-outline:hover,
    .theme-header-action-outline:focus-visible {
        border-color: var(--submenu-secondary, #DFE753);
        background-color: var(--submenu-secondary, #DFE753);
        color: var(--submenu-hover-text, #2D1D5C);
    }

    .theme-header-action-solid {
        background-color: #ffffff;
        color: var(--submenu-primary, #2D1D5C);
        box-shadow: 0 14px 34px -20px rgba(15, 23, 42, 0.45);
    }

    .theme-header-action-solid:hover,
    .theme-header-action-solid:focus-visible {
        background-color: var(--submenu-secondary, #DFE753);
        color: var(--submenu-hover-text, #2D1D5C);
    }

    .theme-mobile-action-outline {
        border-color: var(--submenu-primary, #2D1D5C);
        color: var(--submenu-primary, #2D1D5C);
    }

    .theme-mobile-action-outline:hover,
    .theme-mobile-action-outline:focus-visible {
        border-color: var(--submenu-secondary, #DFE753);
        background-color: var(--submenu-secondary, #DFE753);
        color: var(--submenu-hover-text, #2D1D5C);
    }

    .theme-mobile-action-solid {
        background-color: var(--submenu-primary, #2D1D5C);
        color: #ffffff;
    }

    .theme-mobile-action-solid:hover,
    .theme-mobile-action-solid:focus-visible {
        background-color: var(--submenu-secondary, #DFE753);
        color: var(--submenu-hover-text, #2D1D5C);
    }

    .theme-submenu-panel {
        background-color: var(--submenu-primary, #2D1D5C);
        border-color: var(--submenu-primary, #2D1D5C);
        box-shadow: 0 20px 40px -28px rgba(15, 23, 42, 0.45);
    }

    .theme-submenu-heading {
        color: rgba(255, 255, 255, 0.72);
        opacity: 0.78;
    }

    .theme-submenu-link {
        color: #ffffff;
    }

    .theme-submenu-link:hover,
    .theme-submenu-link:focus-visible {
        background-color: var(--submenu-secondary, #DFE753) !important;
        color: var(--submenu-hover-text, #2D1D5C) !important;
    }

    .theme-submenu-link-active {
        background-color: var(--submenu-primary, #2D1D5C) !important;
        color: #ffffff !important;
    }

    .theme-mobile-submenu-link {
        color: var(--submenu-primary);
    }

    .theme-mobile-submenu-link:hover,
    .theme-mobile-submenu-link:focus-visible {
        background-color: var(--submenu-secondary, #DFE753) !important;
        color: var(--submenu-hover-text, #2D1D5C) !important;
    }

    .theme-mobile-submenu-link-active {
        background-color: var(--submenu-primary, #2D1D5C) !important;
        color: #ffffff !important;
    }

    a:focus-visible,
    button:focus-visible,
    [tabindex]:focus-visible {
        outline: none;
        box-shadow: 0 0 0 3px var(--theme-focus);
    }

    [x-cloak] {
        display: none !important;
    }
</style>
