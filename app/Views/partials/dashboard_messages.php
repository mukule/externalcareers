
<div style="position: fixed; top: 1rem; right: 1rem; z-index: 1050;">
    <?php if (session()->getFlashdata()): ?>
        <?php foreach (['success' => 'success', 'error' => 'danger', 'info' => 'info'] as $key => $type): ?>
            <?php if ($flashData = session()->getFlashdata($key)): ?>
                <?php
                    $message = is_array($flashData) ? implode(', ', $flashData) : $flashData;
                ?>
                <div class="alert alert-<?= $type ?> alert-dismissible fade show shadow" role="alert" style="min-width: 250px;">
                    <?= esc($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>


<script>
   
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach((el) => {
            const alert = new bootstrap.Alert(el);
            alert.close();
        });
    }, 5000);
</script>
