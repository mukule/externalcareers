<!DOCTYPE html>
<html>
<head>
    <title>Error <?= esc($statusCode) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="text-center p-4 border rounded bg-white shadow">
        <h1 class="display-4 text-danger">Oops!</h1>
        <h3>Error <?= esc($statusCode) ?></h3>
        <p class="lead">Something went wrong. Please try again later.</p>
        <!-- Optionally show exception message only in staging, not prod -->
        <?php if (ENVIRONMENT !== 'production'): ?>
            <pre><?= esc($message) ?></pre>
        <?php endif; ?>
        <a href="<?= base_url('/') ?>" class="btn btn-primary mt-3">Go Home</a>
    </div>
</body>
</html>