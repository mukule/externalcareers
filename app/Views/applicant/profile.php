<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-5">

<?= $this->include('partials/prof_nav', ['currentStep' => 1]) ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="card-title mb-4">Basic Details</h4>

            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?php 
                        $errors = session()->getFlashdata('error');
                        if (is_array($errors)) {
                            foreach ($errors as $error) {
                                echo "<div>{$error}</div>";
                            }
                        } else {
                            echo $errors;
                        }
                    ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('applicant/user-details/store') ?>" method="POST">
                <?= csrf_field() ?>

                <input type="hidden" name="id" value="<?= set_value('id', $details['id'] ?? '') ?>">

                <!-- First & Last Name Row -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text"
                               class="form-control"
                               id="first_name"
                               name="first_name"
                               placeholder="Enter your first name"
                               value="<?= set_value('first_name', $details['first_name'] ?? ($user['first_name'] ?? '')) ?>"
                               required>
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text"
                               class="form-control"
                               id="last_name"
                               name="last_name"
                               placeholder="Enter your last name"
                               value="<?= set_value('last_name', $details['last_name'] ?? ($user['last_name'] ?? '')) ?>"
                               required>
                    </div>
                </div>

                <!-- National ID & Phone Row -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="national_id" class="form-label">National ID</label>
                        <input type="text"
                               class="form-control"
                               id="national_id"
                               name="national_id"
                               placeholder="Enter your National ID"
                               value="<?= set_value('national_id', $details['national_id'] ?? '') ?>"
                               required>
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text"
                               class="form-control"
                               id="phone"
                               name="phone"
                               placeholder="e.g., +254712345678"
                               value="<?= set_value('phone', $details['phone'] ?? '') ?>"
                               required>
                    </div>
                </div>

                <!-- Gender & DOB Row -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Gender</label>
                        <select class="form-select" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male" <?= set_select('gender', 'male', isset($details['gender']) && $details['gender'] == 'male') ?>>Male</option>
                            <option value="female" <?= set_select('gender', 'female', isset($details['gender']) && $details['gender'] == 'female') ?>>Female</option>
                            <option value="other" <?= set_select('gender', 'other', isset($details['gender']) && $details['gender'] == 'other') ?>>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="dob" class="form-label">Date of Birth</label>
                        <input type="date"
                               class="form-control"
                               id="dob"
                               name="dob"
                               value="<?= set_value('dob', $details['dob'] ?? '') ?>"
                               required>
                    </div>
                </div>

                <!-- Ethnicity & Nationality Row -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="ethnicity_id" class="form-label">Ethnicity</label>
                        <select class="form-select" id="ethnicity_id" name="ethnicity_id" required>
                            <option value="">Select Ethnicity</option>
                            <?php foreach($ethnicities as $ethnicity): ?>
                                <option value="<?= $ethnicity['id'] ?>" 
                                    <?= set_select('ethnicity_id', $ethnicity['id'], isset($details['ethnicity_id']) && $details['ethnicity_id'] == $ethnicity['id']) ?>>
                                    <?= esc($ethnicity['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="nationality" class="form-label">Nationality</label>
                        <select class="form-select" id="nationality" name="nationality" required>
                            <option value="">Select Nationality</option>
                            <?php foreach($countries as $country): ?>
                                <option value="<?= esc($country) ?>" 
                                    <?= set_select('nationality', $country, isset($details['nationality']) && $details['nationality'] == $country) ?>>
                                    <?= esc($country) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- County of Origin & Residence Row -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="county_of_origin_id" class="form-label">County of Origin</label>
                        <select class="form-select" id="county_of_origin_id" name="county_of_origin_id" required>
                            <option value="">Select County of Origin</option>
                            <?php foreach($counties as $county): ?>
                                <option value="<?= $county['id'] ?>" 
                                    <?= set_select('county_of_origin_id', $county['id'], isset($details['county_of_origin_id']) && $details['county_of_origin_id'] == $county['id']) ?>>
                                    <?= esc($county['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="county_of_residence_id" class="form-label">County of Residence</label>
                        <select class="form-select" id="county_of_residence_id" name="county_of_residence_id" required>
                            <option value="">Select County of Residence</option>
                            <?php foreach($counties as $county): ?>
                                <option value="<?= $county['id'] ?>" 
                                    <?= set_select('county_of_residence_id', $county['id'], isset($details['county_of_residence_id']) && $details['county_of_residence_id'] == $county['id']) ?>>
                                    <?= esc($county['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Disability Section -->
                <div class="mb-3 form-check">
                    <input type="checkbox"
                           class="form-check-input"
                           id="disability_status"
                           name="disability_status"
                           value="1"
                           <?= (isset($details['disability_status']) && $details['disability_status']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="disability_status">Do you have a disability?</label>
                </div>

              <div id="disability_section" style="display: none;">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="disability_type" class="form-label">Disability Type</label>
                        <input type="text"
                            class="form-control"
                            id="disability_type"
                            name="disability_type"
                            placeholder="Enter your disability type"
                            value="<?= set_value('disability_type', $details['disability_type'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="disability_number" class="form-label">Disability Number</label>
                        <input type="text"
                            class="form-control"
                            id="disability_number"
                            name="disability_number"
                            placeholder="Enter your disability card number"
                            value="<?= set_value('disability_number', $details['disability_number'] ?? '') ?>">
                    </div>
                </div>
            </div>


                <button type="submit" class="btn btn-primary">Save & Continue</button>
            </form>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkbox = document.getElementById('disability_status');
    const section = document.getElementById('disability_section');

    function toggleSection() {
        section.style.display = checkbox.checked ? 'block' : 'none';
    }

    toggleSection();
    checkbox.addEventListener('change', toggleSection);
});
</script>

<?= $this->endSection() ?>
