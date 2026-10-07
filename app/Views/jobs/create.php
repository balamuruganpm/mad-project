<?php
// app/Views/jobs/create.php
?>
<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="fw-bold mb-0">Create Quality Job</h2>
        <p class="text-muted">Allocate jobs, assign inspectors, and map template forms.</p>
    </div>
</div>

<div class="card-custom" style="max-width: 800px;">
    <form action="<?php echo BASE_URL; ?>/jobs/store" method="POST">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="customer_id" class="form-label text-secondary fw-semibold">Customer</label>
                <select name="customer_id" id="customer_id" class="form-select form-control-custom">
                    <option value="1">BHEL Switchgears</option>
                    <option value="2">L&T Switchgear Division</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['company_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="template_id" class="form-label text-secondary fw-semibold">QC Form Template</label>
                <select name="template_id" id="template_id" class="form-select form-control-custom">
                    <option value="1">Ammeter Quality Test Template</option>
                    <option value="2">Bus Bar Connection Template</option>
                    <?php foreach ($templates as $t): ?>
                        <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="department_id" class="form-label text-secondary fw-semibold">Responsible Department</label>
                <select name="department_id" id="department_id" class="form-select form-control-custom">
                    <option value="1">IPQC Quality Department</option>
                    <option value="2">IQC Assembly Department</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="project_id" class="form-label text-secondary fw-semibold">Associated Project</label>
                <select name="project_id" id="project_id" class="form-select form-control-custom">
                    <option value="1">Megawin 33KV Substation Project</option>
                    <option value="2">11KV Panel Supply Contract</option>
                    <?php foreach ($projects as $p): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="report_name" class="form-label text-secondary fw-semibold">Report Display Name</label>
                <input type="text" name="report_name" id="report_name" class="form-control form-control-custom" placeholder="e.g. Busbar Calibration Report" required>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="priority" class="form-label text-secondary fw-semibold">Priority</label>
                <select name="priority" id="priority" class="form-select form-control-custom">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="critical">Critical</option>
                </select>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <label for="inspection_date" class="form-label text-secondary fw-semibold">Inspection Target Date</label>
                <input type="date" name="inspection_date" id="inspection_date" class="form-control form-control-custom" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Create & Assign Quality Job</button>
    </form>
</div>

