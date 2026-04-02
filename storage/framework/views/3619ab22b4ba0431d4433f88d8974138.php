<?php $__env->startSection('title', 'Edit Hero Slide'); ?>
<?php $__env->startSection('header', 'Edit Hero Slide'); ?>

<?php $__env->startSection('content'); ?>
<form action="<?php echo e(route('system.hero-slides.update', $slide->id)); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
    <?php echo $__env->make('system.hero-slides._form', ['slide' => $slide, 'maxSlides' => $maxSlides], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views\system\hero-slides\edit.blade.php ENDPATH**/ ?>