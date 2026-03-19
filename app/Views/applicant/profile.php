<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 1]) ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h4 class="card-title mb-4">Basic Details</h4>

            <form action="<?= base_url('applicant/user-details/store') ?>" method="POST">
                <?= csrf_field() ?>

                <input type="hidden" name="id" value="<?= set_value('id', $details['id'] ?? '') ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">First Name</label>
                        <input type="text"
                               class="form-control"
                               name="first_name"
                               value="<?= set_value('first_name', $details['first_name'] ?? ($user['first_name'] ?? '')) ?>"
                               required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Name</label>
                        <input type="text"
                               class="form-control"
                               name="last_name"
                               value="<?= set_value('last_name', $details['last_name'] ?? ($user['last_name'] ?? '')) ?>"
                               required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">National ID</label>
                        <input type="text"
                               class="form-control"
                               name="national_id"
                               value="<?= set_value('national_id', $details['national_id'] ?? '') ?>"
                               required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text"
                               class="form-control"
                               name="phone"
                               value="<?= set_value('phone', $details['phone'] ?? '') ?>"
                               required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Gender</label>
                        <select class="form-select" name="gender_id" required>
                            <option value="">Select Gender</option>
                            <?php foreach ($genders as $gender): ?>
                                <option value="<?= $gender['id'] ?>"
                                    <?= set_select('gender_id', $gender['id'], ($details['gender_id'] ?? '') == $gender['id']) ?>>
                                    <?= esc($gender['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date of Birth</label>
                        <input type="date"
                               class="form-control"
                               name="dob"
                               value="<?= set_value('dob', $details['dob'] ?? '') ?>"
                               required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Ethnicity</label>
                        <select class="form-select" name="ethnicity_id" required>
                            <option value="">Select Ethnicity</option>
                            <?php foreach ($ethnicities as $ethnicity): ?>
                                <option value="<?= $ethnicity['id'] ?>"
                                    <?= set_select('ethnicity_id', $ethnicity['id'], ($details['ethnicity_id'] ?? '') == $ethnicity['id']) ?>>
                                    <?= esc($ethnicity['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- ✅ FIXED MARITAL STATUS -->
                    <div class="col-md-6">
                        <label class="form-label">Marital Status</label>
                        <select class="form-select" name="marital_status_id">
                            <option value="">Select Marital Status</option>
                            <?php foreach ($maritalStatuses as $status): ?>
                                <option value="<?= $status['id'] ?>"
                                    <?= set_select('marital_status_id', $status['id'], ($details['marital_status_id'] ?? '') == $status['id']) ?>>
                                    <?= esc($status['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Country of Birth</label>
                        <select class="form-select" name="country_of_birth_id" required>
                            <option value="">Select Country</option>
                            <?php foreach ($countries as $country): ?>
                                <option value="<?= $country['id'] ?>"
                                    <?= set_select('country_of_birth_id', $country['id'], ($details['country_of_birth_id'] ?? '') == $country['id']) ?>>
                                    <?= esc($country['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Country of Residence</label>
                        <select class="form-select" name="country_of_residence_id" required>
                            <option value="">Select Country</option>
                            <?php foreach ($countries as $country): ?>
                                <option value="<?= $country['id'] ?>"
                                    <?= set_select('country_of_residence_id', $country['id'], ($details['country_of_residence_id'] ?? '') == $country['id']) ?>>
                                    <?= esc($country['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">County of Origin</label>
                        <select class="form-select" name="county_of_origin_id" required>
                            <option value="">Select County</option>
                            <?php foreach ($counties as $county): ?>
                                <option value="<?= $county['id'] ?>"
                                    <?= set_select('county_of_origin_id', $county['id'], ($details['county_of_origin_id'] ?? '') == $county['id']) ?>>
                                    <?= esc($county['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">County of Residence</label>
                        <select class="form-select" name="county_of_residence_id" required>
                            <option value="">Select County</option>
                            <?php foreach ($counties as $county): ?>
                                <option value="<?= $county['id'] ?>"
                                    <?= set_select('county_of_residence_id', $county['id'], ($details['county_of_residence_id'] ?? '') == $county['id']) ?>>
                                    <?= esc($county['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Field of Study</label>
                        <select class="form-select" name="field_of_study_id">
                            <option value="">Select Field</option>
                            <?php foreach ($fieldsOfStudy as $field): ?>
                                <option value="<?= $field['id'] ?>"
                                    <?= set_select('field_of_study_id', $field['id'], ($details['field_of_study_id'] ?? '') == $field['id']) ?>>
                                    <?= esc($field['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Highest Level of Study</label>
                        <select class="form-select" name="highest_level_of_study_id">
                            <option value="">Select Level</option>
                            <?php foreach ($levelsOfStudy as $level): ?>
                                <option value="<?= $level['id'] ?>"
                                    <?= set_select('highest_level_of_study_id', $level['id'], ($details['highest_level_of_study_id'] ?? '') == $level['id']) ?>>
                                    <?= esc($level['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox"
                           class="form-check-input"
                           id="disability_status"
                           name="disability_status"
                           value="1"
                           <?= ($details['disability_status'] ?? 0) ? 'checked' : '' ?>>
                    <label class="form-check-label">Do you have a disability?</label>
                </div>

                <div id="disability_section" style="display:none;">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Disability Type</label>
                            <input type="text"
                                   class="form-control"
                                   name="disability_type"
                                   value="<?= set_value('disability_type', $details['disability_type'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Disability Number</label>
                            <input type="text"
                                   class="form-control"
                                   name="disability_number"
                                   value="<?= set_value('disability_number', $details['disability_number'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4">Save & Continue</button>
                </div>

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