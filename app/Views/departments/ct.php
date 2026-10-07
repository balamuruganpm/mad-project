<?php
// app/Views/departments/ct.php
$isReadOnly = in_array($_SESSION['role_id'] ?? 0, [1, 2]);
?>
<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-microchip me-2"></i><?php echo htmlspecialchars($title); ?></h2>
        <p class="text-muted">Manage CT Document and General Links for the CT department.</p>
    </div>
</div>

<div class="card-custom shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="fa-solid fa-list me-2 text-secondary"></i>Job Directory & Links</h5>
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
                    <th>CT Doc</th>
                    <th>General Link</th>
                    <?php if (!$isReadOnly): ?>
                    <th class="text-end pe-3">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($jobs)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fa-solid fa-folder-open fa-2x mb-2 text-light"></i><br>
                            No jobs found in the system.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($jobs as $j): 
                        $jobId = $j['job_id'];
                        $docsStmt = (new \App\Models\Document())->getDb()->prepare("SELECT file_name, file_path FROM documents WHERE job_id = ? AND department_id = ?");
                        $docsStmt->execute([$jobId, $dept['id']]);
                        $savedDocs = $docsStmt->fetchAll(\PDO::FETCH_KEY_PAIR);
                        
                        $urls = [
                            'ct_doc' => $savedDocs['ct_doc'] ?? '',
                            'link' => $savedDocs['link'] ?? ''
                        ];
                    ?>
                        <tr>
                            <td class="text-start ps-3 fw-bold text-dark"><?php echo htmlspecialchars($j['company_name'] ?? 'N/A'); ?></td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1 fs-6"><?php echo htmlspecialchars($j['job_id_code']); ?></span></td>
                            
                            <?php foreach (['ct_doc', 'link'] as $key): ?>
                                <td>
                                    <?php if (!empty($urls[$key])): ?>
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <a href="<?php echo htmlspecialchars($urls[$key]); ?>" target="_blank" class="text-success text-decoration-none" title="View Link" data-bs-toggle="tooltip">
                                                <i class="fa-solid fa-circle-check fs-5"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-light border p-1" onclick="copyToClipboard(this, '<?php echo htmlspecialchars($urls[$key]); ?>')" title="Copy Link" data-bs-toggle="tooltip">
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
                                        class="btn btn-sm btn-outline-primary fw-semibold rounded-pill px-3"
                                        onclick="openLinkModal(<?php echo $jobId; ?>, '<?php echo htmlspecialchars($j['job_id_code']); ?>', <?php echo htmlspecialchars(json_encode($urls)); ?>)">
                                    <i class="fa-solid fa-pen-to-square me-1"></i>Edit Links
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
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-link me-2 text-primary"></i>Manage CT Links</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <p class="text-muted small mb-4">Paste the document URLs for job <strong id="modalJobIdCode" class="text-primary"></strong> below.</p>
                <form action="<?php echo BASE_URL; ?>/departments/save-department-links" method="POST">
                    <input type="hidden" name="job_id" id="modalJobId" value="">
                    <input type="hidden" name="dept_code" value="CT">
                    <input type="hidden" name="redirect_url" value="/departments/ct">
                    
                    <?php 
                    $fields = [
                        'ct_doc' => 'CT Document Link',
                        'link' => 'General Link'
                    ];
                    foreach ($fields as $code => $label): 
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
        
        const codes = ['ct_doc', 'link'];
        codes.forEach(code => {
            const input = document.getElementById('link_' + code);
            if (input) {
                input.value = urls[code] || '';
            }
        });

        var modal = new bootstrap.Modal(document.getElementById('linksModal'));
        modal.show();
    }
    
    function copyToClipboard(btn, text) {
        navigator.clipboard.writeText(text).then(function() {
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
            let tdJobId = tr[i].getElementsByTagName("td")[1];
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

    document.addEventListener("DOMContentLoaded", function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
