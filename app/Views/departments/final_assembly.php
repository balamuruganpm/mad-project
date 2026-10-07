<?php
// app/Views/departments/final_assembly.php
$isReadOnly = ($_SESSION['role_id'] ?? 0) == 1; // Only Admin is read-only here
?>
<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="fw-bold mb-0"><?php echo htmlspecialchars($title); ?></h2>
        <p class="text-muted">Consolidated dashboard tracking status and inspection report verify status across all quality stages.</p>
    </div>
</div>

<?php if (!$isReadOnly): ?>
<!-- Create New Job Form -->
<div class="card-custom mb-4 border-primary border-start border-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="fa-solid fa-plus-circle me-2 text-primary"></i>Create New Job Record</h5>
    </div>
    <form action="<?php echo BASE_URL; ?>/departments/final-assembly/create-job" method="POST" class="row g-3">
        <div class="col-md-5">
            <label class="form-label fw-semibold text-muted small text-uppercase">Customer Name</label>
            <input type="text" name="company_name" class="form-control form-control-lg bg-light" placeholder="e.g., BHEL Switchgears" required>
        </div>
        <div class="col-md-5">
            <label class="form-label fw-semibold text-muted small text-uppercase">Job ID Code</label>
            <input type="text" name="job_id_code" class="form-control form-control-lg bg-light" placeholder="e.g., JOB-12345" required>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
                <i class="fa-solid fa-plus me-1"></i>Add Job
            </button>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="card-custom shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="fa-solid fa-list me-2 text-secondary"></i>Job Directory & Document Links</h5>
        <div class="input-group" style="max-width: 300px;">
            <span class="input-group-text bg-white"><i class="fa-solid fa-search text-muted"></i></span>
            <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Search by Job ID..." onkeyup="filterTable()">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle text-center mb-0">
            <thead class="table-light text-muted small text-uppercase fw-semibold">
                <tr>
                    <th class="text-start ps-3">Customer Name</th>
                    <th>Job ID</th>
                    <th>Design</th>
                    <th>Busbar</th>
                    <th>CT</th>
                    <th>PT</th>
                    <th>Breaker</th>
                    <th>Inc. QC</th>
                    <?php if (!$isReadOnly): ?>
                    <th class="text-end pe-3">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($jobs)): ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="fa-solid fa-folder-open fa-2x mb-2 text-light"></i><br>
                            No jobs found in the system.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($jobs as $j): 
                        $jobId = $j['job_id'];
                        $docs = $jobDocs[$jobId] ?? [];
                        
                        // Extract URLs to pass to the modal
                        $urls = [
                            'DESIGN' => $docs['DESIGN']['file_path'] ?? '',
                            'BUSBAR' => $docs['BUSBAR']['file_path'] ?? '',
                            'CT' => $docs['CT']['file_path'] ?? '',
                            'PT' => $docs['PT']['file_path'] ?? '',
                            'BREAKER' => $docs['BREAKER']['file_path'] ?? '',
                            'IQC' => $docs['IQC']['file_path'] ?? ''
                        ];
                    ?>
                        <tr>
                            <td class="text-start ps-3">
                                <strong class="text-dark"><?php echo htmlspecialchars($j['company_name'] ?? 'N/A'); ?></strong>
                            </td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1 fs-6">
                                    <?php echo htmlspecialchars($j['job_id_code']); ?>
                                </span>
                            </td>
                            
                            <?php foreach (['DESIGN', 'BUSBAR', 'CT', 'PT', 'BREAKER', 'IQC'] as $deptCode): ?>
                                <td>
                                    <?php if (!empty($urls[$deptCode])): ?>
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <a href="<?php echo htmlspecialchars($urls[$deptCode]); ?>" target="_blank" class="text-success text-decoration-none" title="View Link" data-bs-toggle="tooltip">
                                                <i class="fa-solid fa-circle-check fs-5"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-light border p-1" onclick="copyToClipboard('<?php echo htmlspecialchars($urls[$deptCode]); ?>')" title="Copy Link" data-bs-toggle="tooltip">
                                                <i class="fa-solid fa-copy text-muted"></i>
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted opacity-50" title="Pending" data-bs-toggle="tooltip"><i class="fa-solid fa-circle-xmark fs-5"></i></span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>

                            <?php if (!$isReadOnly): ?>
                            <td class="text-end pe-3">
                                <button type="button" 
                                        class="btn btn-sm btn-outline-primary fw-semibold rounded-pill px-3 me-1"
                                        onclick="openLinkModal(<?php echo $jobId; ?>, '<?php echo htmlspecialchars($j['job_id_code']); ?>', <?php echo htmlspecialchars(json_encode($urls)); ?>)">
                                    <i class="fa-solid fa-pen-to-square me-1"></i>Links
                                </button>
                                <button type="button" onclick="showQrModal('<?php echo htmlspecialchars($j['job_id_code'] ?? ''); ?>', '<?php echo htmlspecialchars($j['qr_token'] ?? ''); ?>')" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="Share & QR">
                                    <i class="fa-solid fa-qrcode"></i>
                                </button>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Links Modal -->
<div class="modal fade" id="linksModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-link me-2 text-primary"></i>Manage Links</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <p class="text-muted small mb-4">Paste the document URLs for job <strong id="modalJobIdCode" class="text-primary"></strong> below.</p>
                <form action="<?php echo BASE_URL; ?>/departments/final-assembly/save-links" method="POST" id="linksForm">
                    <input type="hidden" name="job_id" id="modalJobId" value="">
                    
                    <?php 
                    $departments = [
                        'DESIGN' => 'Design Report',
                        'BUSBAR' => 'Busbar QC',
                        'CT' => 'CT Department',
                        'PT' => 'PT Department',
                        'BREAKER' => 'Breaker Department',
                        'IQC' => 'Incoming QC'
                    ];
                    foreach ($departments as $code => $label): 
                    ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase mb-1"><?php echo $label; ?></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-globe text-muted"></i></span>
                            <input type="url" name="links[<?php echo $code; ?>]" id="link_<?php echo $code; ?>" class="form-control border-start-0 ps-0" placeholder="https://...">
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-light me-2 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4"><i class="fa-solid fa-save me-1"></i>Save Links</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openLinkModal(jobId, jobCode, urls) {
        document.getElementById('modalJobId').value = jobId;
        document.getElementById('modalJobIdCode').textContent = jobCode;
        
        const deptCodes = ['DESIGN', 'BUSBAR', 'CT', 'PT', 'BREAKER', 'IQC'];
        deptCodes.forEach(code => {
            const input = document.getElementById('link_' + code);
            if (input) {
                input.value = urls[code] || '';
            }
        });

        var modal = new bootstrap.Modal(document.getElementById('linksModal'));
        modal.show();
    }
    
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Optional: Show a tiny toast or just rely on the tooltip
            let btn = event.currentTarget;
            let icon = btn.querySelector('i');
            icon.classList.remove('fa-copy');
            icon.classList.add('fa-check', 'text-success');
            setTimeout(() => {
                icon.classList.remove('fa-check', 'text-success');
                icon.classList.add('fa-copy');
            }, 2000);
        }, function(err) {
            console.error('Could not copy text: ', err);
        });
    }

    function filterTable() {
        let input = document.getElementById("searchInput");
        let filter = input.value.toUpperCase();
        let table = document.querySelector(".table tbody");
        let tr = table.getElementsByTagName("tr");

        for (let i = 0; i < tr.length; i++) {
            let tdJobId = tr[i].getElementsByTagName("td")[1]; // Job ID column
            if (tdJobId) {
                let txtValue = tdJobId.textContent || tdJobId.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }

    // Initialize tooltips if bootstrap is available
    document.addEventListener("DOMContentLoaded", function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
