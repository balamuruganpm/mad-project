<?php
// seed_data.php
require_once __DIR__ . '/../app/Config/config.php';
// PSR-4 Autoloader implementation (similar to index.php)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

try {
    $db = \App\Services\Database::getConnection();
    
    // 1. Clear existing generic jobs
    $db->exec("DELETE FROM jobs");
    $db->exec("DELETE FROM iqc_dropdown_options");
    $db->exec("DELETE FROM incoming_qc_reports");

    // 2. Insert dummy Jobs
    $jobs = [
        ['BHEL Switchgears', 'MW-JOB-9A1C', bin2hex(random_bytes(16))],
        ['L&T Switchgear Division', 'MW-JOB-3F8E', bin2hex(random_bytes(16))],
        ['Siemens Energy', 'MW-JOB-5B2X', bin2hex(random_bytes(16))],
        ['ABB Power Grids', 'MW-JOB-7C4D', bin2hex(random_bytes(16))]
    ];

    $stmtJob = $db->prepare("INSERT INTO jobs (company_name, job_id_code, qr_token) VALUES (?, ?, ?)");
    foreach ($jobs as $j) {
        $stmtJob->execute($j);
    }
    
    // 3. Insert IQC Options
    $iqcOpts = [
        ['company', 'BHEL'],
        ['company', 'Siemens'],
        ['company', 'ABB'],
        ['product', 'Switchgear 11kV'],
        ['product', 'VCB Panel'],
        ['product', 'CT Transformer'],
        ['year', '2025'],
        ['year', '2026'],
        ['year', '2027']
    ];
    $stmtOpt = $db->prepare("INSERT INTO iqc_dropdown_options (category, value) VALUES (?, ?)");
    foreach($iqcOpts as $opt) {
        $stmtOpt->execute($opt);
    }

    // 4. Insert IQC Reports
    $reports = [
        ['BHEL', 'Switchgear 11kV', '2026', 'January', 'https://example.com/report1.pdf'],
        ['Siemens', 'VCB Panel', '2026', 'February', 'https://example.com/report2.pdf'],
        ['ABB', 'CT Transformer', '2026', 'March', 'https://example.com/report3.pdf']
    ];
    $stmtRep = $db->prepare("INSERT INTO incoming_qc_reports (company, product, year, month, report_link) VALUES (?, ?, ?, ?, ?)");
    foreach($reports as $r) {
        $stmtRep->execute($r);
    }

    echo "Successfully seeded database with corporate sample data.\n";
} catch (\Exception $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}
