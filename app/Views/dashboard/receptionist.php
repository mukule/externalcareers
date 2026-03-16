<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= esc($title ?? 'Receptionist Dashboard') ?></h1>
    <hr>

    <!-- Dashboard Cards -->
    <div class="row mt-4">
        <?php 
        $cards = [
            ['title' => 'Departments', 'count' => $counts['departments_count'] ?? 0, 'icon' => 'fas fa-building', 'bg' => 'rgba(13,110,253,0.1)', 'iconColor' => '#0d6efd'],
            ['title' => 'Staff', 'count' => $counts['staff_count'] ?? 0, 'icon' => 'fas fa-user-tie', 'bg' => 'rgba(25,135,84,0.1)', 'iconColor' => '#198754'],
            ['title' => 'Receptionists', 'count' => $counts['receptionists_count'] ?? 0, 'icon' => 'fas fa-user-friends', 'bg' => 'rgba(255,193,7,0.1)', 'iconColor' => '#ffc107'],
            ['title' => 'Security', 'count' => $counts['security_count'] ?? 0, 'icon' => 'fas fa-shield-alt', 'bg' => 'rgba(220,53,69,0.1)', 'iconColor' => '#dc3545'],
            ['title' => 'Total Visitors', 'count' => $counts['visitors_total'] ?? 0, 'icon' => 'fas fa-users', 'bg' => 'rgba(13,110,253,0.1)', 'iconColor' => '#0d6efd'],
            ['title' => 'Checked In', 'count' => $counts['visitors_checked_in'] ?? 0, 'icon' => 'fas fa-sign-in-alt', 'bg' => 'rgba(25,135,84,0.1)', 'iconColor' => '#198754'],
            ['title' => 'Drive-in', 'count' => $counts['visitors_drive_in'] ?? 0, 'icon' => 'fas fa-car', 'bg' => 'rgba(220,53,69,0.1)', 'iconColor' => '#dc3545'],
            ['title' => 'Walk-in', 'count' => $counts['visitors_walk_in'] ?? 0, 'icon' => 'fas fa-walking', 'bg' => 'rgba(255,193,7,0.1)', 'iconColor' => '#ffc107'],
        ];

        foreach ($cards as $card): ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow-sm bg-white py-3 px-3" style="border:0; max-width: 220px;">
                    <div class="card-body text-start d-flex flex-column p-2">
                        <div class="d-flex align-items-center mb-1">
                            <div class="position-relative me-2" style="width:40px; height:40px;">
                                <div style="width:40px; height:40px; background: <?= $card['bg'] ?>; display:flex; align-items:center; justify-content:center; border-radius:6px; position:relative; z-index:2;">
                                    <i class="<?= $card['icon'] ?>" style="color: <?= $card['iconColor'] ?>; font-size:18px;"></i>
                                </div>
                                <div style="position:absolute; top:3px; left:3px; width:40px; height:40px; border-radius:6px; background-color: <?= $card['iconColor'] ?>; opacity:0.15; z-index:1;"></div>
                            </div>
                            <h2 class="mb-0" style="font-size:1.4rem;"><?= esc($card['count']) ?></h2>
                        </div>
                        <small class="text-muted" style="font-size:0.85rem;"><?= esc($card['title']) ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Row with Top Departments and Doughnut -->
    <div class="row mt-4 align-items-stretch">
        <div class="col-xl-6 col-md-12 mb-4 d-flex">
            <div class="card shadow-sm bg-white py-3 w-100" style="border:0;">
                <div class="card-body d-flex flex-column">
                    <h5 class="mb-3">Top Departments</h5>
                    <?php if (!empty($departmentsWithCounts)): ?>
                        <ul class="list-group list-group-flush flex-grow-1">
                            <?php foreach ($departmentsWithCounts as $dept): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= esc($dept['name']) ?>
                                    <span class="badge bg-primary rounded-pill"><?= esc($dept['count']) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted mb-0">No departments found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-12 mb-4 d-flex">
            <div class="card shadow-sm bg-white py-3 w-100" style="border:0;">
                <div class="card-body d-flex flex-column">
                    <h5 class="mb-3">Visitors Type</h5>
                    <canvas id="visitorsTypeChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Visitors Table -->
    <div class="card mb-4">
        <div class="card-body">
            <?php if (!empty($allvisitors)): ?>
                <div class="table-responsive">
                    <table id="datatablesSimple" class="table table-striped align-middle">
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
                            <?php foreach ($allvisitors as $index => $visitor): ?>
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
                                        <button class="btn btn-sm btn-outline-primary viewVisitorBtn" 
                                                data-visitor-id="<?= esc($visitor['id']) ?>" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#visitorModal<?= esc($visitor['uuid']) ?>">
                                            <i class="fas fa-eye me-1"></i> View
                                        </button>
                                    </td>
                                </tr>

                                <!-- Visitor Modal -->
                                <div class="modal fade" id="visitorModal<?= esc($visitor['uuid']) ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content bg-white shadow-sm border-0 rounded-3">
                                            <div class="modal-header border-bottom">
                                                <h5 class="modal-title">Visitor Details</h5>
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
                                                    <li class="list-group-item"><strong>Status:</strong> <?= empty($visitor['check_out_time']) ? 'Checked In' : 'Checked Out' ?></li>
                                                </ul>
                                                <div id="belongingsContainer<?= esc($visitor['id']) ?>" class="mt-3 text-center text-muted small">
                                                    <em>Click “View” to load belongings...</em>
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

<!-- Doughnut Chart Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('visitorsTypeChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Drive-in', 'Walk-in'],
            datasets: [{
                data: [
                    <?= $counts['visitors_drive_in'] ?? 0 ?>,
                    <?= $counts['visitors_walk_in'] ?? 0 ?>
                ],
                backgroundColor: ['#203d8d', '#8ac83f'],
                borderColor: ['#203d8d', '#8ac83f'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
});
</script>

<!-- Belongings Load Script (same as admin) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('viewVisitorBtn') || e.target.closest('.viewVisitorBtn')) {
            const btn = e.target.classList.contains('viewVisitorBtn') ? e.target : e.target.closest('.viewVisitorBtn');
            const visitorId = btn.getAttribute('data-visitor-id');
            const container = document.getElementById(`belongingsContainer${visitorId}`);
            if (!container) return;

            container.innerHTML = '<div class="text-center text-muted">Loading belongings...</div>';

            fetch(`<?= base_url('admin/visitors/belongings/') ?>${visitorId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(res => res.ok ? res.json() : Promise.reject('HTTP error'))
            .then(data => {
                if (data.success && data.belongings.length > 0) {
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
                        <h6 class="fw-bold mb-2">Belongings</h6>
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
                    container.innerHTML = '';
                }
            })
            .catch(() => {
                container.innerHTML = '<div class="text-center text-muted">Unable to load belongings at this time.</div>';
            });
        }
    });
});
</script>

<?= $this->endSection() ?>
