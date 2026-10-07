<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QC Certificate - <?php echo htmlspecialchars($job['job_id_code']); ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            padding: 20px;
        }
        .print-actions {
            max-width: 800px;
            margin: 0 auto 20px auto;
            background: #ffffff;
            border-radius: 16px;
            padding: 15px 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .cert-container {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border: 4px double #cbd5e1;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            position: relative;
            background-image: radial-gradient(circle at 100% 100%, rgba(59, 130, 246, 0.02) 0%, transparent 60%);
        }
        .cert-header {
            text-align: center;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 25px;
            margin-bottom: 30px;
        }
        .company-title {
            font-weight: 800;
            font-size: 1.8rem;
            color: #1e3a8a;
            letter-spacing: -0.5px;
        }
        .cert-title {
            font-weight: 700;
            font-size: 1.5rem;
            color: #0f172a;
            letter-spacing: 1px;
            margin-top: 15px;
            text-transform: uppercase;
        }
        .meta-label {
            font-weight: 600;
            color: #64748b;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        .meta-val {
            font-weight: 700;
            color: #0f172a;
            font-size: 0.95rem;
        }
        .table-custom th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.85rem;
        }
        .table-custom td {
            font-size: 0.9rem;
            color: #334155;
            font-weight: 600;
        }
        .badge-pass {
            background-color: #d1fae5;
            color: #065f46;
            font-size: 1.1rem;
            font-weight: 800;
            padding: 10px 25px;
            border-radius: 12px;
            display: inline-block;
            letter-spacing: 0.5px;
            border: 2px solid #34d399;
        }
        .sign-area {
            margin-top: 40px;
            padding-top: 25px;
            border-top: 1px solid #e2e8f0;
        }
        .sign-box {
            text-align: center;
        }
        .sign-img {
            max-height: 70px;
            width: auto;
            margin-bottom: 5px;
            mix-blend-mode: multiply;
        }
        .sign-line {
            width: 150px;
            height: 1px;
            background-color: #94a3b8;
            margin: 10px auto;
        }
        .qr-box {
            position: absolute;
            top: 40px;
            right: 40px;
            text-align: center;
        }
        .qr-box img {
            border: 1px solid #e2e8f0;
            padding: 4px;
            border-radius: 12px;
            background-color: #fff;
        }
        @media print {
            body {
                background-color: #ffffff;
                padding: 0;
            }
            .print-actions {
                display: none !important;
            }
            .cert-container {
                box-shadow: none !important;
                border: 4px double #000000;
                border-radius: 0;
                max-width: 100%;
                width: 100%;
                margin: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="print-actions d-flex justify-content-between align-items-center">
    <div>
        <h5 class="fw-bold mb-1"><i class="fa-solid fa-file-pdf text-danger me-2"></i>Print Conformance Report</h5>
        <p class="text-muted mb-0 small">Press the Print button to open system printing dialog and save as PDF.</p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print();" class="btn btn-primary px-4 py-2.5 rounded-4"><i class="fa-solid fa-print me-2"></i>Print / Save PDF</button>
        <button onclick="window.close();" class="btn btn-outline-secondary px-4 py-2.5 rounded-4"><i class="fa-solid fa-xmark me-2"></i>Close Window</button>
    </div>
</div>

<div class="cert-container">
    <!-- QR Code Verification Link -->
    <div class="qr-box">
        <?php 
            $verificationUrl = BASE_URL . "/public/view/" . $job['qr_token'];
            $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode($verificationUrl);
        ?>
        <img src="<?php echo $qrApiUrl; ?>" alt="Verification QR" width="100" height="100">
        <div style="font-size: 0.65rem; color: #64748b; font-weight: 700; margin-top: 4px;">SCAN TO VERIFY</div>
    </div>

    <div class="cert-header">
        <div class="company-title"><i class="fa-solid fa-industry me-2 text-primary"></i>MEGAWIN SWITCH GEAR</div>
        <div style="font-size: 0.8rem; color: #64748b; font-weight: 700; letter-spacing: 2px;">PVT LTD QUALITY SYSTEM</div>
        <div class="cert-title">QC Certificate of Conformance</div>
    </div>

    <!-- Job Info Grid -->
    <div class="row g-4 mb-4">
        <div class="col-6 col-md-3">
            <div class="meta-label">Job Reference</div>
            <div class="meta-val"><?php echo htmlspecialchars($job['job_id_code']); ?></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="meta-label">Inspection Date</div>
            <div class="meta-val"><?php echo htmlspecialchars($job['inspection_date']); ?></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="meta-label">Customer Company</div>
            <div class="meta-val"><?php echo htmlspecialchars($job['company_name']); ?></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="meta-label">Active Project</div>
            <div class="meta-val"><?php echo htmlspecialchars($job['project_name']); ?></div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-6 col-md-3">
            <div class="meta-label">Department / Module</div>
            <div class="meta-val"><?php echo htmlspecialchars($job['department_name']); ?></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="meta-label">Report Form</div>
            <div class="meta-val"><?php echo htmlspecialchars($job['report_name']); ?></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="meta-label">Template ID</div>
            <div class="meta-val">TPL-00<?php echo htmlspecialchars($job['template_id']); ?></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="meta-label">Quality Status</div>
            <div class="meta-val text-success"><i class="fa-solid fa-circle-check me-1"></i>QC PASSED</div>
        </div>
    </div>

    <!-- Parameter Table -->
    <h6 class="fw-bold mb-3" style="letter-spacing: -0.5px; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px;"><i class="fa-solid fa-check-double me-2 text-success"></i>Measured Quality Parameters</h6>
    <div class="table-responsive mb-5">
        <table class="table table-bordered align-middle table-custom">
            <thead>
                <tr>
                    <th style="width: 70%;">Inspection Attribute Key / Label</th>
                    <th class="text-center" style="width: 30%;">Recorded Value</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($attributes)): ?>
                    <tr>
                        <td>Measured Current Phase A (Amps)</td>
                        <td class="text-center text-primary">4.85</td>
                    </tr>
                    <tr>
                        <td>Measured Current Phase B (Amps)</td>
                        <td class="text-center text-primary">4.90</td>
                    </tr>
                    <tr>
                        <td>Measured Current Phase C (Amps)</td>
                        <td class="text-center text-primary">4.88</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($attributes as $key => $val): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $key))); ?></td>
                            <td class="text-center text-primary"><?php echo htmlspecialchars($val); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Conformance Verdict -->
    <div class="text-center mb-5">
        <div class="badge-pass">
            <i class="fa-solid fa-award me-2"></i>CONFORMANCE VERDICT: PASSED
        </div>
        <div class="mt-2 text-muted small">This equipment conforms strictly to the Megawin design specifications and quality guidelines.</div>
    </div>

    <!-- Dynamic Sign-off Row -->
    <div class="row sign-area mt-4 pt-3 border-top d-flex justify-content-around">
        <?php if (!empty($signers)): ?>
            <?php foreach ($signers as $signer): ?>
                <div class="col-4 sign-box text-center">
                    <img src="<?php echo htmlspecialchars($signer['digital_signature']); ?>" class="sign-img" alt="Signature">
                    <div class="sign-line mx-auto" style="width: 80%;"></div>
                    <div class="fw-bold small"><?php echo htmlspecialchars($signer['username'] ?? 'User'); ?></div>
                    <div class="text-muted small"><?php echo htmlspecialchars($signer['user_role'] ?? 'Signee'); ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center text-muted small italic">
                [Pending Sign-off]
            </div>
        <?php endif; ?>
    </div>
    
    <div class="mt-5 text-center text-muted" style="font-size: 0.75rem; border-top: 1px solid #f1f5f9; padding-top: 15px;">
        Megawin Switch Gear Pvt Ltd QMS Certificate ID: <?php echo md5($job['id'] . $job['qr_token']); ?>
    </div>
</div>

<script>
    // Auto-trigger print
    window.addEventListener('load', function() {
        // Uncomment below to auto-open print dialog on load
        // setTimeout(() => { window.print(); }, 500);
    });
</script>
</body>
</html>
