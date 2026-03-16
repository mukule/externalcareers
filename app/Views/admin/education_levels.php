<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="mb-0"><?= esc($title ?? 'Education Levels') ?></h1>
        <a href="<?= base_url('admin/education-levels/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Education Level
        </a>
    </div>
    <hr>

    <div class="card mb-4">
        <div class="card-body">
            <?php if (!empty($educationLevels) && count($educationLevels) > 0): ?>
                <div class="table-responsive">
                    <table id="datatablesSimple" class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Order</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($educationLevels as $i => $level): ?>
                                <tr data-id="<?= esc($level['id']) ?>">
                                    <td><?= $i + 1 ?></td>
                                    <td class="text-center" style="cursor: grab;">
                                       
                                        <i class="fas fa-arrow-up me-1"></i>
                                        <i class="fas fa-arrow-down"></i>
                                    </td>
                                    <td><?= esc($level['name']) ?></td>
                                    <td class="text-center">
                                        <?php if ($level['active']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/education-levels/edit/' . $level['uuid']) ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <a href="<?= base_url('admin/education-levels/delete/' . $level['uuid']) ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Are you sure you want to delete this education level?');">
                                           <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted text-center mb-0">No education levels found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.querySelector('#datatablesSimple tbody');

    const sortable = new Sortable(tbody, {
        animation: 150,
        handle: 'td:nth-child(2)', // drag only by the arrow column
        onEnd: function () {
            const order = [];
            tbody.querySelectorAll('tr').forEach((row, index) => {
                order.push({ id: row.dataset.id, index: index + 1 });
                row.querySelector('td:first-child').textContent = index + 1; // update row #
            });

            fetch('<?= base_url('admin/education-levels/reorder') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order: order })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status !== 'success') {
                    alert('Failed to update order');
                }
            });
        }
    });
});
</script>

<?= $this->endSection() ?>
