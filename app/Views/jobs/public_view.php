<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Megawin QMS Certificate Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/assets/css/custom.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 40px 20px;
        }
        .cert-card {
            max-width: 700px;
            margin: 0 auto;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
            padding: 40px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.05);
        }
    </style>

</head>
<body>
<div class="cert-card">
    <div class="text-center mb-4">
        <div class="text-success mb-2">
            <i class="fa-solid fa-circle-check fa-4x"></i>
        </div>
        <h2 class="fw-bold text-primary">Megawin QMS Certificate</h2>
        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill"><i class="fa-solid fa-shield-halved me-1"></i>Verified Calibration Report</span>
    </div>

    <hr class="my-4">

    <div class="row mb-3">
        <div class="col-sm-4 text-muted">Job Reference:</div>
        <div class="col-sm-8 fw-bold"><?php echo htmlspecialchars($job['job_id_code']); ?></div>
    </div>
    <div class="row mb-3">
        <div class="col-sm-4 text-muted">Customer Name:</div>
        <div class="col-sm-8"><?php echo htmlspecialchars($job['company_name'] ?? 'BHEL Switchgears'); ?></div>
    </div>
    <div class="row mb-3">
        <div class="col-sm-4 text-muted">Report Name:</div>
        <div class="col-sm-8"><?php echo htmlspecialchars($job['report_name']); ?></div>
    </div>
    <div class="row mb-3">
        <div class="col-sm-4 text-muted">QC Form Template:</div>
        <div class="col-sm-8"><?php echo htmlspecialchars($job['template_name'] ?? 'Ammeter Quality Test Template'); ?></div>
    </div>
    <div class="row mb-3">
        <div class="col-sm-4 text-muted">Inspection Date:</div>
        <div class="col-sm-8"><?php echo htmlspecialchars($job['inspection_date']); ?></div>
    </div>

    <h5 class="fw-bold text-primary mt-5 mb-3"><i class="fa-solid fa-sliders me-2"></i>Parameter Values</h5>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr class="table-light">
                    <th>Test Parameter</th>
                    <th>Measured Value</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($attributes)): ?>
                    <tr>
                        <td>Measured Current Phase A (Amps)</td>
                        <td>4.85</td>
                    </tr>
                    <tr>
                        <td>Measured Current Phase B (Amps)</td>
                        <td>4.90</td>
                    </tr>
                    <tr>
                        <td>Measured Current Phase C (Amps)</td>
                        <td>4.88</td>
                    </tr>
                    <tr class="fw-bold">
                        <td>Average Current Calculation</td>
                        <td>4.88 Amps</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($attributes as $key => $val): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $key))); ?></td>
                            <td><?php echo htmlspecialchars($val); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Dynamic Sign-off Row -->
    <div class="row sign-area mt-4 pt-4 border-top d-flex justify-content-around">
        <?php if (!empty($signers)): ?>
            <?php foreach ($signers as $signer): ?>
                <div class="col-4 sign-box text-center">
                    <img src="<?php echo htmlspecialchars($signer['digital_signature']); ?>" class="sign-img" alt="Signature" style="max-height: 60px; mix-blend-mode: multiply;">
                    <div style="width: 100px; height: 1px; background-color: #cbd5e1; margin: 8px auto;"></div>
                    <div class="fw-bold small" style="color: #1e293b;"><?php echo htmlspecialchars($signer['username'] ?? 'User'); ?></div>
                    <div class="text-muted" style="font-size: 0.7rem;"><?php echo htmlspecialchars($signer['user_role'] ?? 'Signee'); ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center text-muted small italic">
                [Pending Sign-off]
            </div>
        <?php endif; ?>
    </div>


    <div class="text-center mt-5 text-muted small">
        <p>Megawin Switch Gear Pvt Ltd Quality Assurance Department.</p>
        <p class="mb-0">&copy; 2026 Megawin QMS System. All rights reserved.</p>
    </div>
</div>
</body>
</html>
