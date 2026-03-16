<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 7]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="card-title mb-4">
                <?= isset($referee) ? 'Edit Referee' : 'Add New Referee' ?>
            </h4>

            <!-- Flash Messages -->
            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= is_array(session()->getFlashdata('error')) 
                        ? implode('<br>', session()->getFlashdata('error')) 
                        : session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= $action ?>" method="POST">
                <?= csrf_field() ?>

                <?php if(isset($referee)): ?>
                    <input type="hidden" name="id" value="<?= $referee['id'] ?>">
                <?php endif; ?>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" 
                               class="form-control" 
                               name="name" 
                               id="name" 
                               placeholder="Referee full name" 
                               value="<?= $referee['name'] ?? '' ?>" 
                               required>
                    </div>

                    <div class="col-md-6">
                        <label for="position" class="form-label">Position / Title</label>
                        <input type="text" 
                               class="form-control" 
                               name="position" 
                               id="position" 
                               placeholder="e.g. HR Manager" 
                               value="<?= $referee['position'] ?? '' ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="organization" class="form-label">Organization / Company</label>
                        <input type="text" 
                               class="form-control" 
                               name="organization" 
                               id="organization" 
                               placeholder="Company or organization name" 
                               value="<?= $referee['organization'] ?? '' ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="relationship" class="form-label">Relationship</label>
                        <input type="text" 
                               class="form-control" 
                               name="relationship" 
                               id="relationship" 
                               placeholder="e.g. Former Supervisor" 
                               value="<?= $referee['relationship'] ?? '' ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" 
                               class="form-control" 
                               name="email" 
                               id="email" 
                               placeholder="example@email.com" 
                               value="<?= $referee['email'] ?? '' ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" 
                               class="form-control" 
                               name="phone" 
                               id="phone" 
                               placeholder="+2547XXXXXXXX" 
                               value="<?= $referee['phone'] ?? '' ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <?= isset($referee) ? 'Update Referee' : 'Add Referee' ?>
                </button>

                <a href="<?= base_url('applicant/referees') ?>" class="btn btn-secondary">Back to List</a>

            </form>

        </div>
    </div>

</div>

<?= $this->endSection() ?>
