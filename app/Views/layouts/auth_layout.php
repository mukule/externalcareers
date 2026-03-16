<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  
  <title><?= esc($appName) ?> | <?= esc($title ?? '') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body style="height: 100vh; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">

  <div class="container d-flex justify-content-center align-items-center mb-4">
    <div class="shadow-lg rounded-3 bg-white p-5" style="width: 100%; max-width: 500px;">
      
      <div class="text-center mb-4">
        <a href="<?= site_url('/signin') ?>">
          <img src="<?= base_url('assets/images/logo.png'); ?>" alt="Logo" class="img-fluid">
        </a>
      </div>

      <div class="text-center mb-2">
        <h4 class="fw-bold"><?= $heading ?? 'Welcome' ?></h4>
        <p class="text-muted fs-6 mb-0"><?= $subheading ?? 'Online Application System' ?></p>
      </div>

      <hr class="colorgraph">

      <?= $this->include('partials/messages'); ?>

      <?= $this->renderSection('content') ?>

      <?php if (!empty($showLinks)): ?>
        <div class="d-flex justify-content-between mt-2">
          <a href="<?= site_url('/forgot-password') ?>" class="small text-decoration-none">Forgot Password?</a>
          <a href="<?= site_url('/register') ?>" class="small text-decoration-none">Create Account</a>
        </div>

        <div class="text-center mt-2">
          <a href="<?= site_url('/') ?>" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-book"></i> Explore Our Courses
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>


  <script>
  function togglePassword(id, btn) {
      const input = document.getElementById(id);
      input.type = input.type === "password" ? "text" : "password";
      btn.textContent = btn.textContent === "🙈" ? "👁" : "🙈";
  }
</script>




</body>
</html>
