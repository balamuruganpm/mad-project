<?php
// app/Views/departments/iqc_add_option.php
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<div class="row mb-4">
    <div class="col">
        <h3 class="fw-bold text-dark"><i class="fa-solid fa-plus-circle text-primary me-2"></i><?php echo htmlspecialchars($title); ?></h3>
        <p class="text-muted">Manage the options available in the Incoming QC forms.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-custom border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h5 class="fw-bold">Add New Option</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/departments/incoming-qc/add-option" method="POST">
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                    <input type="hidden" name="redirect_url" value="/departments/incoming-qc/add-<?php echo htmlspecialchars($category); ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">New <?php echo ucfirst(htmlspecialchars($category)); ?> Value</label>
                        <input type="text" name="value" class="form-control" required placeholder="Enter value...">
                    </div>
                    
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>Add Option</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card card-custom border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h5 class="fw-bold">Existing Options</h5>
            </div>
            <div class="card-body">
                <?php if (empty($existing)): ?>
                    <p class="text-muted">No options added yet.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush border rounded">
                        <?php foreach($existing as $opt): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($opt); ?>
                                <span class="badge bg-secondary rounded-pill"><i class="fa-solid fa-check"></i></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
