<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 7]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <h4 class="card-title mb-4">
                <?= isset($referee) ? 'Edit Referee' : 'Add New Referee' ?>
            </h4>

            <form action="<?= $action ?>" method="POST">
                <?= csrf_field() ?>

                <?php if(isset($referee)): ?>
                    <input type="hidden" name="id" value="<?= $referee['id'] ?>">
                <?php endif; ?>

                <!-- NAME + POSITION -->
                <div class="row mb-3">

                    <div class="col-md-6">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               name="name"
                               id="name"
                               value="<?= $referee['name'] ?? '' ?>"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label for="position" class="form-label">Position / Title <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               name="position"
                               id="position"
                               value="<?= $referee['position'] ?? '' ?>"
                               required>
                    </div>

                </div>

                <!-- ORGANIZATION + RELATIONSHIP -->
                <div class="row mb-3">

                    <div class="col-md-6">
                        <label for="organization" class="form-label">Organization / Company <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               name="organization"
                               id="organization"
                               value="<?= $referee['organization'] ?? '' ?>"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label for="relationship" class="form-label">Relationship <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               name="relationship"
                               id="relationship"
                               value="<?= $referee['relationship'] ?? '' ?>"
                               required>
                    </div>

                </div>

                <!-- EMAIL + PHONE -->
                <div class="row mb-3">

                    <div class="col-md-6">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email"
                               class="form-control"
                               name="email"
                               id="email"
                               value="<?= $referee['email'] ?? '' ?>"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               name="phone"
                               id="phone"
                               value="<?= $referee['phone'] ?? '' ?>"
                               required>
                    </div>

                </div>

                <!-- BUTTONS -->
                <button type="submit" class="btn btn-primary">
                    <?= isset($referee) ? 'Update Referee' : 'Add Referee' ?>
                </button>

                <a href="<?= base_url('applicant/referees') ?>" class="btn btn-secondary">
                    Back to List
                </a>

            </form>

        </div>
    </div>

</div>

<?= $this->endSection() ?>