<?php
$config = require __DIR__ . '/../app/Config/database.php';
try {
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
    
    // 1. Create Dropdown Options table
    $sql = "CREATE TABLE IF NOT EXISTS iqc_dropdown_options (
        id INT AUTO_INCREMENT PRIMARY KEY, 
        category VARCHAR(50) NOT NULL, 
        value VARCHAR(150) NOT NULL,
        UNIQUE KEY uq_category_val (category, value)
    )";
    $pdo->exec($sql);
    
    // Insert some defaults
    $pdo->exec("INSERT IGNORE INTO iqc_dropdown_options (category, value) VALUES 
        ('company', 'Seimon'), ('company', 'ABB'),
        ('product', 'Circuit Breaker'), ('product', 'Switchgear'),
        ('year', '2025'), ('year', '2026')
    ");

    // 2. Clear old roles and insert strict new ones
    // First, let's just insert the new roles (avoiding deleting old ones to prevent foreign key errors for existing users)
    $roles = [
        ['id' => 1, 'name' => 'Admin', 'permissions' => '{"read_only": true, "all_departments": true}'],
        ['id' => 2, 'name' => 'Final Assembly', 'permissions' => '{"read_only": true, "all_departments": true}'],
        ['id' => 3, 'name' => 'Design', 'permissions' => '{"department": "DESIGN"}'],
        ['id' => 4, 'name' => 'Busbar', 'permissions' => '{"department": "BUSBAR"}'],
        ['id' => 5, 'name' => 'CT', 'permissions' => '{"department": "CT"}'],
        ['id' => 6, 'name' => 'PT', 'permissions' => '{"department": "PT"}'],
        ['id' => 7, 'name' => 'Breaker', 'permissions' => '{"department": "BREAKER"}'],
        ['id' => 8, 'name' => 'Incoming QC', 'permissions' => '{"department": "IQC"}'],
    ];

    $stmt = $pdo->prepare("INSERT INTO roles (id, name, permissions) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE name=VALUES(name), permissions=VALUES(permissions)");
    foreach ($roles as $r) {
        $stmt->execute([$r['id'], $r['name'], $r['permissions']]);
    }

    // 3. Create Users for each role
    $users = [
        [1, 'admin', 'admin@megawin.com', 'admin123'],
        [2, 'final_assembly_user', 'fa@megawin.com', 'user123'],
        [3, 'design_user', 'design@megawin.com', 'user123'],
        [4, 'busbar_user', 'busbar@megawin.com', 'user123'],
        [5, 'ct_user', 'ct@megawin.com', 'user123'],
        [6, 'pt_user', 'pt@megawin.com', 'user123'],
        [7, 'breaker_user', 'breaker@megawin.com', 'user123'],
        [8, 'iqc_user', 'iqc@megawin.com', 'user123'],
    ];

    $stmt = $pdo->prepare("INSERT INTO users (role_id, username, email, password_hash) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE role_id=VALUES(role_id), password_hash=VALUES(password_hash)");
    foreach ($users as $u) {
        $hash = password_hash($u[3], PASSWORD_BCRYPT);
        $stmt->execute([$u[0], $u[1], $u[2], $hash]);
    }

    echo "RBAC Setup completed.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
// unlink(__FILE__); // keeping for debugging
?>
