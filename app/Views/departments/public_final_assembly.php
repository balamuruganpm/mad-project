<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QC Conformance Portal - Megawin QMS</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS styling -->
    <link href="<?php echo BASE_URL; ?>/assets/css/custom.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="text-center mb-5">
                <h2 class="fw-bold text-gradient"><i class="fa-solid fa-industry me-2"></i>Megawin Switch Gear</h2>
                <p class="text-muted">Quality Assurance & Conformance Verification Portal</p>
            </div>

            <!-- Job details card -->
            <div class="card-custom mb-4">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <small class="text-muted text-uppercase fw-semibold">Customer</small>
                        <h4 class="fw-bold text-primary mb-0"><?php echo htmlspecialchars($job['company_name'] ?? 'N/A'); ?></h4>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted text-uppercase fw-semibold">Job ID Code</small>
                        <h4 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($job['job_id_code']); ?></h4>
                    </div>
                </div>
            </div>

            <!-- Matrix card -->
            <div class="card-custom">
                <h5 class="fw-bold mb-4"><i class="fa-solid fa-table-list text-primary me-2"></i>Stage-wise Inspection Reports</h5>
                
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Department Stage</th>
                                <th>Report Status</th>
                                <th class="text-end">Document Link</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $stages = [
                                'DESIGN' => 'Design Department',
                                'BUSBAR' => 'Busbar (Incoming QC)',
                                'CT' => 'CT Department',
                                'PT' => 'PT Department',
                                'BREAKER' => 'Breaker Department',
                                'IQC' => 'Incoming QC Department'
                            ];
                            
                            foreach ($stages as $code => $label): 
                                $isUploaded = isset($jobDocs[$code]);
                            ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo $label; ?></div>
                                    </td>
                                    <td>
                                        <?php if ($isUploaded): ?>
                                            <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i>Links Available</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i>In Progress / Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($isUploaded): ?>
                                            <div class="d-flex flex-column gap-2 align-items-end">
                                                <?php foreach ($jobDocs[$code] as $fileName => $doc): ?>
                                                <div class="d-flex justify-content-end align-items-center gap-2">
                                                    <span class="small text-muted fw-bold me-2"><?php echo htmlspecialchars($fileName); ?>:</span>
                                                    <a href="<?php echo htmlspecialchars($doc['file_path']); ?>" target="_blank" class="btn btn-sm btn-primary rounded-4">
                                                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>View Link
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-light border rounded-4 p-1 px-2" onclick="copyToClipboard(this, '<?php echo htmlspecialchars($doc['file_path']); ?>')" title="Copy Link" data-bs-toggle="tooltip">
                                                        <i class="fa-solid fa-copy text-muted"></i>
                                                    </button>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer info -->
            <div class="text-center mt-5 text-muted">
                <small>&copy; <?php echo date('Y'); ?> Megawin Switch Gear Pvt Ltd. All rights reserved.</small>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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

    document.addEventListener("DOMContentLoaded", function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
</body>
</html>
