<?php
// app/Views/departments/incoming_qc.php
$isReadOnly = in_array($_SESSION['role_id'] ?? 0, [1, 2]);
$isIqcUser = (($_SESSION['role_id'] ?? 0) == 8);
?>
<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-boxes-packing me-2"></i><?php echo htmlspecialchars($title); ?></h2>
        <p class="text-muted">Manage hierarchical Incoming QC reports (Company &rarr; Product &rarr; Year &rarr; Month &rarr; Report).</p>
    </div>
</div>

<?php if (!$isReadOnly): ?>

<!-- Options management moved to sidebar -->

<!-- Create New Report Form -->
<div class="card-custom mb-4 border-primary border-start border-4 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="fa-solid fa-plus-circle me-2 text-primary"></i>Add New QC Report</h5>
    </div>
    <form action="<?php echo BASE_URL; ?>/departments/incoming-qc/store" method="POST" class="row g-3">
        <div class="col-md-2">
            <label class="form-label fw-semibold text-muted small text-uppercase">Company</label>
            <select name="company" class="form-select bg-light" required>
                <option value="" disabled selected>Select...</option>
                <?php foreach($options['company'] ?? [] as $opt): ?>
                    <option value="<?php echo htmlspecialchars($opt); ?>"><?php echo htmlspecialchars($opt); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold text-muted small text-uppercase">Product</label>
            <select name="product" class="form-select bg-light" required>
                <option value="" disabled selected>Select...</option>
                <?php foreach($options['product'] ?? [] as $opt): ?>
                    <option value="<?php echo htmlspecialchars($opt); ?>"><?php echo htmlspecialchars($opt); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-1">
            <label class="form-label fw-semibold text-muted small text-uppercase">Year</label>
            <select name="year" class="form-select bg-light" required>
                <option value="" disabled selected>Select...</option>
                <?php foreach($options['year'] ?? [] as $opt): ?>
                    <option value="<?php echo htmlspecialchars($opt); ?>"><?php echo htmlspecialchars($opt); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold text-muted small text-uppercase">Month</label>
            <select name="month" class="form-select bg-light" required>
                <option value="" disabled selected>Select...</option>
                <option value="January">January</option>
                <option value="February">February</option>
                <option value="March">March</option>
                <option value="April">April</option>
                <option value="May">May</option>
                <option value="June">June</option>
                <option value="July">July</option>
                <option value="August">August</option>
                <option value="September">September</option>
                <option value="October">October</option>
                <option value="November">November</option>
                <option value="December">December</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold text-muted small text-uppercase">Report Link</label>
            <input type="url" name="report_link" class="form-control bg-light" placeholder="https://..." required>
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm" title="Add Report">
                <i class="fa-solid fa-plus"></i> Add
            </button>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="card-custom shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="fa-solid fa-folder-tree me-2 text-secondary"></i>QC Reports Directory</h5>
        <div class="input-group" style="max-width: 300px;">
            <span class="input-group-text bg-white"><i class="fa-solid fa-search text-muted"></i></span>
            <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Search by Company/Product..." onkeyup="filterTable()">
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle text-center mb-0">
            <thead class="table-light text-muted small text-uppercase fw-semibold">
                <tr>
                    <th class="text-start ps-3">Company</th>
                    <th>Product</th>
                    <th>Year</th>
                    <th>Month</th>
                    <th>Report Link</th>
                    <?php if (!$isReadOnly): ?>
                    <th class="text-end pe-3">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reports)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fa-solid fa-folder-open fa-2x mb-2 text-light"></i><br>
                            No reports found in the system.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reports as $r): ?>
                        <tr>
                            <td class="text-start ps-3 fw-bold text-dark"><?php echo htmlspecialchars($r['company']); ?></td>
                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($r['product']); ?></span></td>
                            <td><?php echo htmlspecialchars($r['year']); ?></td>
                            <td><?php echo htmlspecialchars($r['month']); ?></td>
                            <td>
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <a href="<?php echo htmlspecialchars($r['report_link']); ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Open Report
                                    </a>
                                    <button type="button" class="btn btn-sm btn-light border p-1" onclick="copyToClipboard(this, '<?php echo htmlspecialchars($r['report_link']); ?>')" title="Copy Link" data-bs-toggle="tooltip">
                                        <i class="fa-solid fa-copy text-muted"></i>
                                    </button>
                                </div>
                            </td>
                            <?php if (!$isReadOnly): ?>
                            <td class="text-end pe-3">
                                <form action="<?php echo BASE_URL; ?>/departments/incoming-qc/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this report?');">
                                    <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
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
            // Search across Company and Product columns
            let tdCompany = tr[i].getElementsByTagName("td")[0]; 
            let tdProduct = tr[i].getElementsByTagName("td")[1];
            if (tdCompany || tdProduct) {
                let txtCompany = tdCompany.textContent || tdCompany.innerText;
                let txtProduct = tdProduct.textContent || tdProduct.innerText;
                if (txtCompany.toUpperCase().indexOf(filter) > -1 || txtProduct.toUpperCase().indexOf(filter) > -1) {
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
