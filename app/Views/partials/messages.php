<?php if (session()->getFlashdata()): ?>
    <?php foreach (['success' => 'success', 'error' => 'danger'] as $key => $type): ?>
        <?php if ($flashData = session()->getFlashdata($key)): ?>
            <?php
                
                $messages = is_array($flashData) ? $flashData : [$flashData];
            ?>
            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= $type ?> alert-dismissible fade show" role="alert">
                    <?= esc($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

<script>

    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
