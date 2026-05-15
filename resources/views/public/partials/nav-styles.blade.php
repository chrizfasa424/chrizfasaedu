{{-- Shared public-site nav styles --}}
<style>
    :root {
        --theme-focus: rgba(13, 148, 136, 0.24);
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        text-align: justify;
        text-justify: inter-word;
    }

    .bg-pattern-grid {
        position: relative;
        isolation: isolate;
        background:
            radial-gradient(circle at 10% -8%, rgba(245, 188, 63, 0.22), transparent 34rem),
            radial-gradient(circle at 90% 4%, rgba(20, 184, 166, 0.2), transparent 34rem),
            radial-gradient(circle at 84% 100%, rgba(14, 116, 144, 0.14), transparent 38rem),
            linear-gradient(180deg, rgba(252, 253, 250, 0.96) 0%, rgba(244, 249, 245, 0.92) 56%, rgba(238, 245, 239, 0.95) 100%);
    }

    .bg-pattern-grid::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 0;
        background:
            radial-gradient(circle at 10% 24%, rgba(13, 148, 136, 0.18), transparent 24rem),
            radial-gradient(circle at 90% 76%, rgba(251, 191, 36, 0.14), transparent 24rem);
    }

    .bg-pattern-grid > * {
        position: relative;
        z-index: 1;
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

    .public-site-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.14);
        box-shadow: 0 18px 34px -30px rgba(15, 23, 42, 0.42);
        backdrop-filter: saturate(140%) blur(12px);
        -webkit-backdrop-filter: saturate(140%) blur(12px);
    }

    .public-header-shell {
        position: relative;
    }

    .public-header-shell::after {
        content: "";
        position: absolute;
        inset: auto 0 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        pointer-events: none;
    }

    .theme-nav-link {
        color: #1e293b !important;
        border-radius: 0.75rem;
    }

    .public-desktop-nav {
        display: none;
        background-color: rgba(255, 255, 255, 0.94) !important;
        color: #1e293b !important;
        border: 1px solid rgba(255, 255, 255, 0.62);
        box-shadow: 0 16px 30px -28px rgba(15, 23, 42, 0.46);
    }

    @media (min-width: 1024px) {
        .public-desktop-nav {
            display: flex;
        }
    }

    .theme-nav-link:hover,
    .theme-nav-link:focus-visible {
        background-color: color-mix(in srgb, var(--submenu-secondary, #f4c857) 80%, white) !important;
        color: var(--submenu-hover-text, #25333E) !important;
    }

    .theme-nav-link-active {
        background-color: color-mix(in srgb, var(--submenu-primary, #115e59) 92%, black 8%) !important;
        color: #ffffff !important;
        box-shadow: 0 10px 20px -16px rgba(15, 23, 42, 0.72);
    }

    .theme-header-action-outline {
        border-color: rgba(255, 255, 255, 0.55);
        color: #ffffff;
        backdrop-filter: blur(10px);
    }

    .theme-header-action-outline:hover,
    .theme-header-action-outline:focus-visible {
        border-color: var(--submenu-secondary, #DFE753);
        background-color: var(--submenu-secondary, #DFE753);
        color: var(--submenu-hover-text, #25333E);
    }

    .theme-header-action-solid {
        background-color: #ffffff;
        color: var(--submenu-primary, #25333E);
        box-shadow: 0 12px 26px -20px rgba(15, 23, 42, 0.42);
    }

    .theme-header-action-solid:hover,
    .theme-header-action-solid:focus-visible {
        background-color: var(--submenu-secondary, #DFE753);
        color: var(--submenu-hover-text, #25333E);
    }

    .theme-mobile-action-outline {
        border-color: var(--submenu-primary, #25333E);
        color: var(--submenu-primary, #25333E);
    }

    .theme-mobile-action-outline:hover,
    .theme-mobile-action-outline:focus-visible {
        border-color: var(--submenu-secondary, #DFE753);
        background-color: var(--submenu-secondary, #DFE753);
        color: var(--submenu-hover-text, #25333E);
    }

    .theme-mobile-action-solid {
        background-color: var(--submenu-primary, #25333E);
        color: #ffffff;
    }

    .theme-mobile-action-solid:hover,
    .theme-mobile-action-solid:focus-visible {
        background-color: var(--submenu-secondary, #DFE753);
        color: var(--submenu-hover-text, #25333E);
    }

    .theme-submenu-panel {
        background-color: color-mix(in srgb, var(--submenu-primary, #115e59) 88%, #0b1320 12%);
        border-color: var(--submenu-primary, #25333E);
        box-shadow: 0 24px 42px -30px rgba(15, 23, 42, 0.58);
    }

    .theme-submenu-heading {
        color: rgba(255, 255, 255, 0.72);
        opacity: 0.8;
    }

    .theme-submenu-link {
        color: #ffffff;
        border-radius: 0.6rem;
    }

    .theme-submenu-link:hover,
    .theme-submenu-link:focus-visible {
        background-color: color-mix(in srgb, var(--submenu-secondary, #f4c857) 84%, white) !important;
        color: var(--submenu-hover-text, #25333E) !important;
    }

    .theme-submenu-link-active {
        background-color: color-mix(in srgb, var(--submenu-secondary, #f4c857) 84%, white) !important;
        color: var(--submenu-hover-text, #25333E) !important;
    }

    .theme-mobile-submenu-link {
        color: var(--submenu-primary);
        border-radius: 0.6rem;
    }

    .theme-mobile-submenu-link:hover,
    .theme-mobile-submenu-link:focus-visible {
        background-color: color-mix(in srgb, var(--submenu-secondary, #f4c857) 84%, white) !important;
        color: var(--submenu-hover-text, #25333E) !important;
    }

    .theme-mobile-submenu-link-active {
        background-color: var(--submenu-primary, #25333E) !important;
        color: #ffffff !important;
    }

    /* Mobile drawer hardening: keep content visible even when utility classes are stale/colliding */
    #public-mobile-menu {
        display: flex;
        flex-direction: column;
        max-height: 100dvh;
        transform: translateX(100%);
        visibility: hidden;
    }

    #public-mobile-menu[data-open="true"] {
        transform: translateX(0);
        visibility: visible;
    }

    #public-mobile-menu [data-mobile-menu-body] {
        display: block;
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        background: #ffffff;
    }

    .theme-nav-link:hover,
    .theme-nav-link:focus-visible,
    .theme-submenu-link:hover,
    .theme-submenu-link:focus-visible,
    .theme-mobile-submenu-link:hover,
    .theme-mobile-submenu-link:focus-visible {
        animation: none !important;
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

    @media (prefers-reduced-motion: reduce) {
        html {
            scroll-behavior: auto;
        }

        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }
</style>
