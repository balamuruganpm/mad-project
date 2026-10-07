<?php
// app/Views/jobs/list.php
?>
<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="fw-bold mb-0">Job Management</h2>
        <p class="text-muted">Track product categories, inspection status, and approval stages.</p>
    </div>
    <div class="col-auto">
        <a href="<?php echo BASE_URL; ?>/jobs/create" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i>Create New Job
        </a>
    </div>
</div>

<div class="card-custom">

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>Customer</th>
                    <th>Report Name</th>
                    <th>Template</th>
                    <th>Date</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Approval</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($jobs)): ?>
                    <!-- Mock data for view demonstration if DB is empty -->
                    <tr>
                        <td>MW-JOB-9A1C</td>
                        <td>BHEL Switchgears</td>
                        <td>Incoming QC Ammeter</td>
                        <td>Ammeter Inspection Form</td>
                        <td>2026-06-26</td>
                        <td><span class="badge bg-danger">High</span></td>
                        <td><span class="badge bg-warning">Assigned</span></td>
                        <td><span class="badge bg-secondary">Pending</span></td>
                        <td class="text-end">
                            <a href="<?php echo BASE_URL; ?>/jobs/inspect/1" class="btn btn-sm btn-primary rounded-pill"><i class="fa-solid fa-file-signature me-1"></i>Inspect</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill"><i class="fa-solid fa-qrcode"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>MW-JOB-3F8E</td>
                        <td>L&T Switchgear Division</td>
                        <td>Bus Bar Connection Inspection</td>
                        <td>Bus Bar Connection Inspection</td>
                        <td>2026-06-26</td>
                        <td><span class="badge bg-warning text-dark">Medium</span></td>
                        <td><span class="badge bg-success">Completed</span></td>
                        <td><span class="badge bg-success">Approved</span></td>
                        <td class="text-end">
                            <a href="<?php echo BASE_URL; ?>/public/view/mock_token_1" target="_blank" class="btn btn-sm btn-outline-success rounded-pill"><i class="fa-solid fa-eye me-1"></i>Cert</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($jobs as $j): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($j['job_id_code']); ?></strong></td>
                            <td><?php echo htmlspecialchars($j['company_name'] ?? 'BHEL'); ?></td>
                            <td><?php echo htmlspecialchars($j['report_name']); ?></td>
                            <td><?php echo htmlspecialchars($j['template_name'] ?? 'Ammeter Inspection Form'); ?></td>
                            <td><?php echo htmlspecialchars($j['inspection_date']); ?></td>
                            <td>
                                <span class="badge <?php echo $j['priority'] == 'high' || $j['priority'] == 'critical' ? 'bg-danger' : 'bg-secondary'; ?>">
                                    <?php echo ucfirst($j['priority']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark"><?php echo str_replace('_', ' ', ucfirst($j['status'])); ?></span>
                            </td>
                            <td>
                                <span class="badge bg-success"><?php echo str_replace('_', ' ', ucfirst($j['approval_status'])); ?></span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end align-items-center">
                                    <?php if ($j['status'] === 'created' || $j['status'] === 'assigned' || $j['status'] === 'inspection_in_progress' || $j['status'] === 'rework_requested'): ?>
                                        <a href="<?php echo BASE_URL; ?>/jobs/inspect/<?php echo $j['id']; ?>" class="btn btn-sm btn-primary rounded-4"><i class="fa-solid fa-file-signature me-1"></i>Inspect</a>
                                    <?php elseif ($j['status'] === 'pending_review'): ?>
                                        <a href="<?php echo BASE_URL; ?>/jobs/inspect/<?php echo $j['id']; ?>" class="btn btn-sm btn-warning rounded-4 text-dark"><i class="fa-solid fa-magnifying-glass me-1"></i>Review</a>
                                    <?php elseif ($j['status'] === 'completed'): ?>
                                        <a href="<?php echo BASE_URL; ?>/jobs/certificate/<?php echo $j['id']; ?>" target="_blank" class="btn btn-sm btn-outline-success rounded-4"><i class="fa-solid fa-award me-1"></i>Cert</a>
                                    <?php endif; ?>
                                    <button type="button" onclick="showQrModal('<?php echo htmlspecialchars($j['job_id_code'] ?? ''); ?>', '<?php echo htmlspecialchars($j['qr_token'] ?? ''); ?>')" class="btn btn-sm btn-outline-secondary rounded-4" title="Share & QR">
                                        <i class="fa-solid fa-share-nodes"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
