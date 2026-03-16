<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
   <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
    <h1 class="mb-0"><?= esc($title ?? 'Visitors') ?></h1>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/visitors/create') ?>" class="btn btn-primary">
            <i class="fas fa-user-plus me-1"></i> Checkin
        </a>
        <a href="<?= base_url('admin/visitors/checkout-all') ?>" 
           class="btn btn-outline-primary"
           onclick="return confirm('Are you sure you want to check out all currently checked-in visitors?');">
            <i class="fas fa-sign-out-alt me-1"></i> Check Out All
        </a>
    </div>
</div>

    <hr>

    <div class="card mb-4">
        <div class="card-body">
            <?php if (!empty($visitors)): ?>
                <div class="table-responsive">
                    <table id="datatablesSimple" class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Host</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($visitors as $index => $visitor): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= esc($visitor['first_name'].' '.$visitor['last_name']) ?></td>
                                    <td><?= $visitor['visit_type'] === 'drivein' ? 'Drive-In' : 'Walk-In' ?></td>
                                    <td><?= esc($visitor['host_name'] ?? '-') ?></td>
                                    <td><?= !empty($visitor['check_in_time']) ? date('d M Y, H:i', strtotime($visitor['check_in_time'])) : '-' ?></td>
                                    <td><?= !empty($visitor['check_out_time']) ? date('d M Y, H:i', strtotime($visitor['check_out_time'])) : '-' ?></td>
                                    <td>
                                        <span class="badge p-2 <?= empty($visitor['check_out_time']) ? 'bg-primary' : 'bg-secondary' ?>">
                                            <?= empty($visitor['check_out_time']) ? 'Checked In' : 'Checked Out' ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-cogs me-1"></i> Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                <li>
                                                    <a href="#" 
                                                       class="dropdown-item viewVisitorBtn" 
                                                       data-visitor-id="<?= esc($visitor['id']) ?>" 
                                                       data-bs-toggle="modal" 
                                                       data-bs-target="#visitorModal<?= esc($visitor['uuid']) ?>">
                                                       <i class="fas fa-eye me-2 text-primary"></i> View Details
                                                    </a>
                                                </li>
                                                <?php if (empty($visitor['check_out_time'])): ?>
                                                    <li>
                                                        <form action="<?= base_url('admin/visitors/checkout/' . $visitor['uuid']) ?>" method="post" onsubmit="return confirm('Confirm checkout for this visitor?');">
                                                            <?= csrf_field() ?>
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="fas fa-sign-out-alt me-2"></i> Checkout
                                                            </button>
                                                        </form>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Visitor Modal -->
                                <div class="modal fade" id="visitorModal<?= esc($visitor['uuid']) ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content bg-white shadow-sm border-0 rounded-3">
                                            <div class="modal-header border-bottom">
                                                <h5 class="modal-title">Checkin Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <ul class="list-group list-group-flush mb-3">
                                                    <li class="list-group-item"><strong>Full Name:</strong> <?= esc($visitor['first_name'].' '.$visitor['last_name']) ?></li>
                                                    <li class="list-group-item"><strong>Phone:</strong> <?= esc($visitor['phone']) ?></li>
                                                    <li class="list-group-item"><strong>Email:</strong> <?= esc($visitor['email'] ?? '-') ?></li>
                                                    <li class="list-group-item"><strong>ID Number:</strong> <?= esc($visitor['id_number'] ?? '-') ?></li>
                                                    <li class="list-group-item"><strong>Visit Type:</strong> <?= $visitor['visit_type'] === 'drivein' ? 'Drive-In' : 'Walk-In' ?></li>
                                                    <?php if ($visitor['visit_type'] === 'drivein'): ?>
                                                        <li class="list-group-item"><strong>Vehicle:</strong> <?= esc($visitor['vehicle_reg_no'] ?? '-') ?></li>
                                                        <li class="list-group-item"><strong>No. of Passengers:</strong> <?= esc($visitor['no_of_passengers'] ?? '-') ?></li>
                                                    <?php endif; ?>
                                                    <li class="list-group-item"><strong>Purpose:</strong> <?= esc($visitor['purpose'] ?? '-') ?></li>
                                                    <li class="list-group-item"><strong>Host:</strong> <?= esc($visitor['host_name'] ?? '-') ?></li>
                                                    <li class="list-group-item"><strong>Checked In By:</strong> <?= esc($visitor['checked_in_by_name'] ?? '-') ?></li>
                                                    <li class="list-group-item"><strong>Checked Out By:</strong> <?= esc($visitor['checked_out_by_name'] ?? '-') ?></li>
                                                    <li class="list-group-item"><strong>Status:</strong> <?= empty($visitor['check_out_time']) ? 'Checked In' : 'Checked Out' ?></li>
                                                </ul>

                                                <div id="belongingsContainer<?= esc($visitor['id']) ?>" class="mt-3 text-center text-muted small">
                                                    <em>Click “View Details” to load belongings...</em>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="fas fa-times me-1"></i> Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-muted">No visitors found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Use event delegation to handle dynamically loaded content
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('viewVisitorBtn') || 
            e.target.closest('.viewVisitorBtn')) {
            
            const btn = e.target.classList.contains('viewVisitorBtn') ? 
                       e.target : e.target.closest('.viewVisitorBtn');
            const visitorId = btn.getAttribute('data-visitor-id');
            const container = document.getElementById(`belongingsContainer${visitorId}`);
            
            if (!container) {
                return;
            }
            
            container.innerHTML = '<div class="text-center text-muted">Loading belongings...</div>';
            
            fetch(`<?= base_url('admin/visitors/belongings/') ?>${visitorId}`, {
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                if (data.success && data.belongings && data.belongings.length > 0) {
                    let rows = data.belongings.map((item, i) => `
                        <tr>
                            <td>${i + 1}</td>
                            <td>${item.reference_no || 'N/A'}</td>
                            <td>${item.item_name || 'N/A'}</td>
                            <td>${item.description || '-'}</td>
                            <td>${item.created_at ? new Date(item.created_at).toLocaleString() : 'N/A'}</td>
                        </tr>
                    `).join('');

                    container.innerHTML = `
                        <h6 class="fw-bold mb-2">
                            Belongings
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Reference No.</th>
                                        <th>Item Name</th>
                                        <th>Description</th>
                                        <th>Added On</th>
                                    </tr>
                                </thead>
                                <tbody>${rows}</tbody>
                            </table>
                        </div>`;
                } else {
                    // Show nothing if no belongings
                    container.innerHTML = '';
                }
            })
            .catch(err => {
                // Generic error message for production
                container.innerHTML = '<div class="text-center text-muted">Unable to load belongings at this time.</div>';
            });
        }
    });
});
</script>



<?= $this->endSection() ?>
