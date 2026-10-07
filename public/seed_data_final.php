<?php
// seed_data_final.php
require_once __DIR__ . '/../app/Config/config.php';
// PSR-4 Autoloader implementation
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
    
    // Clear specific data (if needed) without dropping foreign keys
    $db->exec("DELETE FROM iqc_dropdown_options");
    $db->exec("DELETE FROM incoming_qc_reports");

    // Insert dummy Customers
    $stmtCust = $db->prepare("INSERT INTO customers (company_name, email, phone) VALUES (?, ?, ?)");
    $customers = [
        ['BHEL Switchgears', 'bhel@test.com', '123'],
        ['L&T Switchgear Division', 'lt@test.com', '123'],
        ['Siemens Energy', 'se@test.com', '123'],
        ['ABB Power Grids', 'abb@test.com', '123']
    ];
    $custIds = [];
    foreach ($customers as $c) {
        $stmtCust->execute($c);
        $custIds[] = $db->lastInsertId();
    }
    
    // Insert jobs with all required fields
    // fields: customer_id, job_id_code, qr_token, template_id, department_id, project_id, report_name
    $jobs = [
        [$custIds[0], 'MW-JOB-9A1C', bin2hex(random_bytes(16)), 1, 1, 1, 'BHEL Assembly Report'],
        [$custIds[1], 'MW-JOB-3F8E', bin2hex(random_bytes(16)), 1, 1, 1, 'L&T Panel Report'],
        [$custIds[2], 'MW-JOB-5B2X', bin2hex(random_bytes(16)), 1, 1, 1, 'Siemens Transformer'],
        [$custIds[3], 'MW-JOB-7C4D', bin2hex(random_bytes(16)), 1, 1, 1, 'ABB Core Check']
    ];

    // Assuming template_id=1, department_id=1, project_id=1 exist
    $stmtJob = $db->prepare("INSERT INTO jobs (customer_id, job_id_code, qr_token, template_id, department_id, project_id, report_name, priority, inspection_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'medium', CURDATE(), 'assigned')");
    
    foreach ($jobs as $j) {
        try {
            $stmtJob->execute($j);
        } catch (\Exception $e) {
            // ignore if foreign key for template/dept/project fails, meaning they don't exist
        }
    }

    // Insert IQC Options
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

    // Insert IQC Reports
    $reports = [
        ['BHEL', 'Switchgear 11kV', '2026', 'January', 'https://example.com/report1.pdf'],
        ['Siemens', 'VCB Panel', '2026', 'February', 'https://example.com/report2.pdf'],
        ['ABB', 'CT Transformer', '2026', 'March', 'https://example.com/report3.pdf']
    ];
    $stmtRep = $db->prepare("INSERT INTO incoming_qc_reports (company, product, year, month, report_link) VALUES (?, ?, ?, ?, ?)");
    foreach($reports as $r) {
        $stmtRep->execute($r);
    }

    echo "Successfully seeded database with corporate sample data!\n";
} catch (\Exception $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}
