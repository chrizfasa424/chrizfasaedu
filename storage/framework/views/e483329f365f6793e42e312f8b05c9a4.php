
<?php
    $loaderPrimary = $primary ?? '#2D1D5C';
    $loaderLogo    = $school?->logo ? asset('storage/' . ltrim($school->logo, '/')) : null;
    $loaderInitials = \Illuminate\Support\Str::upper(
        collect(preg_split('/\s+/', trim($school?->name ?? 'S')))
            ->filter()->take(2)
            ->map(fn($w) => \Illuminate\Support\Str::substr($w, 0, 1))
            ->implode('')
    );
?>

<div id="page-loader" style="
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    background: <?php echo e($loaderPrimary); ?>;
    transition: opacity 0.5s ease, visibility 0.5s ease;
">
    <div style="position: relative; display: flex; align-items: center; justify-content: center;">

        
        <span style="
            position: absolute;
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            animation: loader-pulse 1.4s ease-in-out infinite;
        "></span>

        
        <span style="
            position: absolute;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.2);
            border-top-color: rgba(255,255,255,0.9);
            animation: loader-spin 0.9s linear infinite;
        "></span>

        
        <div style="
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            animation: loader-blink 1.4s ease-in-out infinite;
        ">
            <?php if($loaderLogo): ?>
                <img src="<?php echo e($loaderLogo); ?>" alt="Logo" style="width:100%;height:100%;object-fit:cover;">
            <?php else: ?>
                <span style="font-size:1.4rem;font-weight:900;color:<?php echo e($loaderPrimary); ?>;letter-spacing:-0.02em;font-family:'Manrope',sans-serif;">
                    <?php echo e($loaderInitials); ?>

                </span>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
@keyframes loader-spin {
    to { transform: rotate(360deg); }
}
@keyframes loader-pulse {
    0%, 100% { transform: scale(1);   opacity: 1; }
    50%       { transform: scale(1.2); opacity: 0; }
}
@keyframes loader-blink {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.55; }
}
</style>

<script>
(function () {
    const loader = document.getElementById('page-loader');
    if (!loader) return;
    const minDisplay = 2200; // minimum ms the loader stays visible
    const start = Date.now();

    function hide() {
        const elapsed = Date.now() - start;
        const delay = Math.max(0, minDisplay - elapsed);
        setTimeout(function () {
            loader.style.opacity = '0';
            loader.style.visibility = 'hidden';
            setTimeout(function () { loader.remove(); }, 550);
        }, delay);
    }

    window.addEventListener('load', hide);
})();
</script>
<?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views/public/partials/page-loader.blade.php ENDPATH**/ ?>