<?php
// app/Views/layout/sidebar.php
$currentUri = $_SERVER['REQUEST_URI'];

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id']) && (!isset($_SESSION['role_id']) || !isset($_SESSION['role_name']))) {
    try {
        $userModel = new \App\Models\User();
        $details = $userModel->getUserDetails((int)$_SESSION['user_id']);
        if ($details) {
            $_SESSION['role_id'] = (int)$details['role_id'];
            $_SESSION['role_name'] = $details['role_name'];
            $_SESSION['username'] = $details['username'];
        }
    } catch (\Exception $e) {
        // Ignore DB issues
    }
}
?>
<!-- Mobile Top Bar Header -->
<div class="col-12 d-md-none bg-light p-3 border-bottom d-flex align-items-center justify-content-between">
    <h5 class="fw-bold text-primary mb-0"><i class="fa-solid fa-industry me-2"></i>Megawin QMS</h5>
    <button id="mobileSidebarToggle" class="btn btn-outline-primary border-0">
        <i class="fa-solid fa-bars fa-lg"></i>
    </button>
</div>

<!-- Drawer Overlay -->
<div id="sidebarOverlay" class="sidebar-overlay"></div>

<div id="appSidebar" class="col-md-3 col-lg-2 sidebar-custom">
    <div class="mb-4 text-center d-none d-md-block">
        <h4 class="fw-bold text-gradient"><i class="fa-solid fa-industry me-2"></i>Megawin QMS</h4>
        <small class="text-muted fw-medium">Quality Management</small>
    </div>

    <nav class="nav flex-column mb-auto">
        <?php 
        $rId = $_SESSION['role_id'] ?? 0;
        $isAdminOrFa = in_array($rId, [1, 2]);
        ?>

        <?php if ($isAdminOrFa): ?>
        <a class="nav-link-custom <?php echo (strpos($currentUri, 'dashboard') !== false || $currentUri == '/per/mad/public/' || $currentUri == '/per/mad/public') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/dashboard">
            <i class="fa-solid fa-chart-pie me-3"></i>Dashboard
        </a>
        <?php endif; ?>

        
        <!-- Accordion for Document Flow -->
        <div class="accordion mt-2" id="sidebarAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingDocs">
                    <button class="accordion-button <?php echo (strpos($currentUri, 'departments') !== false) ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDocs" aria-expanded="<?php echo (strpos($currentUri, 'departments') !== false) ? 'true' : 'false'; ?>" aria-controls="collapseDocs">
                        <i class="fa-solid fa-folder-tree me-3 text-center" style="width:24px;"></i> Departments
                    </button>
                </h2>
                <div id="collapseDocs" class="accordion-collapse collapse <?php echo (strpos($currentUri, 'departments') !== false) ? 'show' : ''; ?>" aria-labelledby="headingDocs" data-bs-parent="#sidebarAccordion">
                    <div class="accordion-body d-flex flex-column gap-1">
                        <?php 
                        if ($isAdminOrFa || $rId == 8): ?>
                        <a class="nav-link-custom <?php echo (strpos($currentUri, 'departments/incoming-qc') !== false && strpos($currentUri, 'add-') === false) ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/departments/incoming-qc">
                            Incoming QC
                        </a>
                        <?php if ($rId == 8): ?>
                        <div class="ps-3 mt-1 mb-2 border-start border-2 border-primary-subtle ms-3">
                            <a class="nav-link-custom small <?php echo (strpos($currentUri, 'add-company') !== false) ? 'active fw-bold text-primary' : ''; ?>" href="<?php echo BASE_URL; ?>/departments/incoming-qc/add-company" style="padding: 6px 12px;">
                                <i class="fa-solid fa-building me-2"></i>Company Add
                            </a>
                            <a class="nav-link-custom small <?php echo (strpos($currentUri, 'add-product') !== false) ? 'active fw-bold text-primary' : ''; ?>" href="<?php echo BASE_URL; ?>/departments/incoming-qc/add-product" style="padding: 6px 12px;">
                                <i class="fa-solid fa-box-open me-2"></i>Product Add
                            </a>
                            <a class="nav-link-custom small <?php echo (strpos($currentUri, 'add-year') !== false) ? 'active fw-bold text-primary' : ''; ?>" href="<?php echo BASE_URL; ?>/departments/incoming-qc/add-year" style="padding: 6px 12px;">
                                <i class="fa-solid fa-calendar me-2"></i>Year Add
                            </a>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($isAdminOrFa || $rId == 3): ?>
                        <a class="nav-link-custom <?php echo (strpos($currentUri, 'departments/design') !== false) ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/departments/design">
                            Design
                        </a>
                        <?php endif; ?>

                        <?php if ($isAdminOrFa || $rId == 4): ?>
                        <a class="nav-link-custom <?php echo (strpos($currentUri, 'departments/busbar') !== false) ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/departments/busbar">
                            Busbar QC
                        </a>
                        <?php endif; ?>

                        <?php if ($isAdminOrFa || $rId == 5): ?>
                        <a class="nav-link-custom <?php echo (strpos($currentUri, 'departments/ct') !== false) ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/departments/ct">
                            CT Dept
                        </a>
                        <?php endif; ?>

                        <?php if ($isAdminOrFa || $rId == 6): ?>
                        <a class="nav-link-custom <?php echo (strpos($currentUri, 'departments/pt') !== false) ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/departments/pt">
                            PT Dept
                        </a>
                        <?php endif; ?>

                        <?php if ($isAdminOrFa || $rId == 7): ?>
                        <a class="nav-link-custom <?php echo (strpos($currentUri, 'departments/breaker') !== false) ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/departments/breaker">
                            Breaker Dept
                        </a>
                        <?php endif; ?>

                        <?php if ($isAdminOrFa): ?>
                        <a class="nav-link-custom <?php echo (strpos($currentUri, 'departments/final-assembly') !== false) ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/departments/final-assembly">
                            Final Assembly
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <hr class="my-3 text-muted" style="opacity: 0.15;">
        
        <a class="nav-link-custom <?php echo (strpos($currentUri, 'profile') !== false) ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/profile">
            <i class="fa-solid fa-user-circle me-3"></i>My Profile
        </a>
        <a class="nav-link-custom text-danger mt-1" href="<?php echo BASE_URL; ?>/logout">
            <i class="fa-solid fa-right-from-bracket me-3"></i>Logout
        </a>
    </nav>
</div>


<!-- Main Workspace Container -->
<div class="col-md-9 col-lg-10 p-4">

<script>
$(document).ready(function() {
    $('#mobileSidebarToggle, #sidebarOverlay').on('click', function() {
        $('#appSidebar').toggleClass('show');
        $('#sidebarOverlay').toggleClass('show');
    });
});
</script>

