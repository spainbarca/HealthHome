<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Session::has('success')): ?>
<div class="toastr-message" data-type="success"><?php echo e(session('success')); ?></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Session::has('error')): ?>
<div class="toastr-message" data-type="error"><?php echo e(session('error')); ?></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Session::has('info')): ?>
<div class="toastr-message" data-type="info"><?php echo e(session('info')); ?></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Session::has('warning')): ?>
<div class="toastr-message" data-type="warning"><?php echo e(session('warning')); ?></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<script>
    $(document).ready(function() {
        $('.toastr-message').each(function() {
            var messageType = $(this).data('type');
            var messageText = $(this).text();
            toastr.options = {
                "closeButton": false,
                "progressBar": true,
                "extendedTimeOut": 0,
                "timeOut": 0,
            };

            switch (messageType) {
                case 'success':
                    toastr.success(messageText);
                    break;
                case 'error':
                    toastr.error(messageText);
                    break;
                case 'info':
                    toastr.info(messageText);
                    break;
                case 'warning':
                    toastr.warning(messageText);
                    break;
                default:
                    toastr.info(messageText);
            }
        });
    });
</script>
<?php /**PATH C:\laragon\www\cuba-laravel\resources\views/admin/inc/alerts.blade.php ENDPATH**/ ?>