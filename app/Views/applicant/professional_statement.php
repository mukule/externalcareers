<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 2]) ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="card-title mb-4">Professional Statement</h4>

            <form action="<?= base_url('applicant/professional-statement/store') ?>" method="POST">
                <?= csrf_field() ?>

                <!-- Hidden ID for updates -->
                <input type="hidden" name="id" value="<?= set_value('id', $statement['id'] ?? '') ?>">

                <!-- Professional Statement -->
                <div class="mb-3">
                    <label for="description" class="form-label">Your Professional Statement</label>
                    <textarea class="form-control"
                              id="description"
                              name="statement"
                              rows="8"
                              placeholder="Write your professional statement here..."
                              required><?= set_value('statement', $statement['statement'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Save & Continue</button>
            </form>
        </div>
    </div>

</div>


<?= $this->endSection() ?>
