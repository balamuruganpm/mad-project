<?php
// app/Views/dashboard/index.php
?>
<style>
/* Material 3 Advanced Minimal Styles */
.m3-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 4px 16px rgba(149, 157, 165, 0.08);
    border: 1px solid var(--border-color);
    transition: transform 0.25s cubic-bezier(0.2, 0, 0, 1), box-shadow 0.25s cubic-bezier(0.2, 0, 0, 1);
    height: 100%;
    position: relative;
    overflow: hidden;
}

.m3-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 36px rgba(149, 157, 165, 0.15);
}

.m3-metric-value {
    font-size: 2.25rem;
    font-weight: 800;
    line-height: 1.1;
    letter-spacing: -1px;
    color: #1e293b;
}

.m3-metric-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #64748b;
    margin-bottom: 4px;
}

.m3-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

/* Floating Matrix Table */
.m3-table {
    border-collapse: separate;
    border-spacing: 0 12px;
    width: 100%;
}

.m3-table th {
    border: none;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 0.8rem;
    padding: 0 20px;
}

.m3-table tbody tr {
    background: #ffffff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.m3-table tbody tr:hover {
    transform: scale(1.01);
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
}

.m3-table td {
    border: none;
    padding: 16px 20px;
    vertical-align: middle;
}

.m3-table td:first-child {
    border-top-left-radius: 8px;
    border-bottom-left-radius: 8px;
}

.m3-table td:last-child {
    border-top-right-radius: 8px;
    border-bottom-right-radius: 8px;
}

/* Minimalist Badges */
.m3-badge {
    padding: 6px 14px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.75rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
}
.m3-badge-soft-primary { background: #e0f2fe; color: #0284c7; }
.m3-badge-soft-success { background: #dcfce7; color: #16a34a; }
.m3-badge-soft-danger { background: #fee2e2; color: #dc2626; }
.m3-badge-soft-warning { background: #fef3c7; color: #d97706; }
.m3-badge-soft-secondary { background: #f1f5f9; color: #475569; }

/* Scrollbar hiding for clean lists */
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
/* Role Badge Styling */
.m3-role-badge {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    color: #f8fafc;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 6px 16px;
    border-radius: 30px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 4px 10px rgba(15, 23, 42, 0.15);
    vertical-align: middle;
}
</style>

<div class="row align-items-center mb-5 p-4" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 16px; border: 1px solid rgba(226, 232, 240, 0.8); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);">
    <div class="col-md-8">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <h2 class="fw-bold mb-0" style="font-size: 2.3rem; letter-spacing: -1px; color: #0f172a;">Welcome back, <?php echo htmlspecialchars($username); ?></h2>
            <span class="m3-role-badge">
                <i class="fa-solid fa-user-shield"></i> <?php echo htmlspecialchars($roleName ?? 'User'); ?>
            </span>
        </div>
        <p class="mb-0 mt-2" style="color: #64748b; font-size: 1.05rem;">Here is your unified quality control overview.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
        <button id="btnScanQr" class="btn btn-outline-primary btn-lg px-4 shadow-sm" style="font-weight: 600; border-radius: 8px; border-color: #cbd5e1; color: #334155;">
            <i class="fa-solid fa-qrcode me-2"></i>Scan QR
        </button>
        <a href="<?php echo BASE_URL; ?>/jobs/create" class="btn btn-primary btn-lg px-4 shadow-sm" style="font-weight: 600; border-radius: 8px; background: #0f172a; border-color: #0f172a;">
            <i class="fa-solid fa-plus me-2"></i>New Job
        </a>
    </div>
</div>

<!-- Dynamic Bento Grid -->
<div class="row g-4 mb-4">
    <!-- Today's Jobs -->
    <div class="col-xl-3 col-sm-6">
        <div class="m3-card d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="m3-metric-label">Total Jobs</div>
                    <div class="m3-metric-value text-primary"><?php echo $totalJobs; ?></div>
                </div>
                <div class="m3-icon-box" style="background: #f1f5f9; color: var(--primary-color);">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Review -->
    <div class="col-xl-3 col-sm-6">
        <div class="m3-card d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="m3-metric-label">Pending Review</div>
                    <div class="m3-metric-value text-warning"><?php echo $pendingJobs; ?></div>
                </div>
                <div class="m3-icon-box" style="background: #f1f5f9; color: #b45309;">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>
            
            <?php if (!empty($pendingTasks)): ?>
            <div class="mt-4 hide-scrollbar" style="max-height: 80px; overflow-y: auto;">
                <?php foreach(array_slice($pendingTasks, 0, 3) as $task): ?>
                   <div class="d-flex align-items-center mb-2" style="font-size: 0.8rem; color: #475569;">
                       <div style="width: 6px; height: 6px; border-radius: 50%; background: #f59e0b; margin-right: 8px;"></div>
                       <strong class="me-2 text-dark"><?php echo htmlspecialchars($task['job_id_code']); ?></strong>
                       <span class="text-truncate"><?php echo htmlspecialchars($task['department_name']); ?></span>
                   </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Completed -->
    <div class="col-xl-3 col-sm-6">
        <div class="m3-card d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="m3-metric-label">Completed</div>
                    <div class="m3-metric-value text-success"><?php echo $completedJobs; ?></div>
                </div>
                <div class="m3-icon-box" style="background: #f1f5f9; color: #047857;">
                    <i class="fa-solid fa-check-double"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejected -->
    <div class="col-xl-3 col-sm-6">
        <div class="m3-card d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="m3-metric-label">Rejected</div>
                    <div class="m3-metric-value text-danger"><?php echo $rejectedJobs; ?></div>
                </div>
                <div class="m3-icon-box" style="background: #f1f5f9; color: #b91c1c;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Consolidated Matrix -->
<div class="row mt-2">
    <div class="col-12 d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">Final Assembly Matrix</h4>
        
        <!-- Global Settings Toggle -->
        <div class="form-check form-switch d-flex align-items-center gap-2">
            <input class="form-check-input" type="checkbox" role="switch" id="qrLoginToggle" style="transform: scale(1.3); cursor: pointer;" <?php echo (isset($qrRequireLogin) && $qrRequireLogin == '1') ? 'checked' : ''; ?>>
            <label class="form-check-label fw-bold text-muted small mt-1" for="qrLoginToggle" style="cursor: pointer;">
                <i class="fa-solid fa-lock text-warning me-1"></i> Require Login for Public QR Links
            </label>
        </div>
    </div>
        <div class="table-responsive hide-scrollbar" style="padding: 10px 0;">
            <table class="table table-hover text-center align-middle">
                <thead>
                    <tr>
                        <th class="text-start">Customer</th>
                        <th>Job ID</th>
                        <?php foreach ($departments as $dept): ?>
                            <th><?php echo htmlspecialchars($dept['name']); ?></th>
                        <?php endforeach; ?>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($jobs)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5" style="background: #fff; border-radius: 8px;">
                                <i class="fa-regular fa-folder-open fa-2x mb-2"></i><br>
                                No jobs available
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($jobs as $j): 
                            $jobId = $j['job_id'];
                            $docs = $jobDocs[$jobId] ?? [];
                        ?>
                            <tr>
                                <td class="text-start fw-bold" style="color: #334155;">
                                    <?php echo htmlspecialchars($j['company_name'] ?? 'N/A'); ?>
                                </td>
                                <td>
                                    <span class="m3-badge m3-badge-soft-secondary"><?php echo htmlspecialchars($j['job_id_code']); ?></span>
                                </td>
                                
                                <?php foreach ($departments as $dept): ?>
                                    <td>
                                        <?php if (isset($docs[$dept['code']])): ?>
                                            <a href="<?php echo BASE_URL . '/../storage/uploads/' . basename($docs[$dept['code']]['file_path']); ?>" target="_blank" class="m3-badge m3-badge-soft-success">
                                                <i class="fa-solid fa-check"></i> OK
                                            </a>
                                        <?php else: ?>
                                            <span class="m3-badge m3-badge-soft-danger"><i class="fa-solid fa-xmark"></i> Pending</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>

                                <td class="text-end">
                                    <button type="button" onclick="showQrModal('<?php echo htmlspecialchars($j['job_id_code'] ?? ''); ?>', '<?php echo htmlspecialchars($j['qr_token'] ?? ''); ?>')" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;" title="Share & QR">
                                        <i class="fa-solid fa-share-nodes"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- QR Scanner Modal -->
<div class="modal fade" id="qrScannerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 12px;">
            <div class="modal-header border-0 bg-light">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-qrcode me-2 text-primary"></i>Scan Certificate QR</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center bg-dark">
                <div id="reader" style="width: 100%; min-height: 300px;"></div>
            </div>
            <div class="modal-footer border-0 justify-content-center bg-light">
                <p class="text-muted small mb-0">Point your camera at the QR code on the certificate.</p>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
$(document).ready(function() {
    // QR Login Toggle
    $('#qrLoginToggle').on('change', function() {
        const isChecked = $(this).is(':checked');
        $.ajax({
            url: '<?php echo BASE_URL; ?>/dashboard/toggle-qr-login',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ require_login: isChecked }),
            success: function(res) {
                if(res.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Setting saved.',
                        showConfirmButton: false,
                        timer: 2000
                    });
                } else {
                    Swal.fire('Error', res.message || 'Failed to update setting', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to communicate with server.', 'error');
                $('#qrLoginToggle').prop('checked', !isChecked); // revert
            }
        });
    });

    // QR Scanner
    let html5QrcodeScanner = null;
    
    $('#btnScanQr').on('click', function() {
        $('#qrScannerModal').modal('show');
    });
    
    $('#qrScannerModal').on('shown.bs.modal', function () {
        html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: {width: 250, height: 250} }, false);
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    });
    
    $('#qrScannerModal').on('hidden.bs.modal', function () {
        if(html5QrcodeScanner) {
            html5QrcodeScanner.clear().catch(error => {
                console.error("Failed to clear html5QrcodeScanner. ", error);
            });
        }
    });

    function onScanSuccess(decodedText, decodedResult) {
        if(html5QrcodeScanner) html5QrcodeScanner.clear();
        $('#qrScannerModal').modal('hide');
        
        if (decodedText.startsWith('http')) {
            window.location.href = decodedText;
        } else {
            Swal.fire('Invalid QR', 'This does not look like a valid Megawin Certificate QR code.', 'warning');
        }
    }

    function onScanFailure(error) {}
});

function showQrModal(jobId, token) {
    if(!token) return;
    const url = '<?php echo BASE_URL; ?>/public/view/' + token;
    Swal.fire({
        title: 'Share Job ' + jobId,
        html: '<p class="mb-3 text-muted">Scan to view Certificate</p>' +
              '<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(url) + '" class="rounded-3 shadow-sm mb-3">' +
              '<div class="input-group"><input type="text" class="form-control text-center" value="'+url+'" readonly id="shareUrl"><button class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText(document.getElementById(\'shareUrl\').value); Swal.showValidationMessage(\'Copied to clipboard!\');"><i class="fa-solid fa-copy"></i></button></div>',
        showConfirmButton: false,
        showCloseButton: true
    });
}
</script>
