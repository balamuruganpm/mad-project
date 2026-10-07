<?php
namespace App\Models;

class IncomingQc extends BaseModel {
    
    public function __construct() {
        parent::__construct();
        $this->db->exec("CREATE TABLE IF NOT EXISTS incoming_qc_reports (
            id INT AUTO_INCREMENT PRIMARY KEY, 
            company VARCHAR(150), 
            product VARCHAR(150), 
            year VARCHAR(4), 
            month VARCHAR(20), 
            report_link VARCHAR(500), 
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }

    public function getAllReports() {
        $stmt = $this->db->query("SELECT * FROM incoming_qc_reports ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function createReport($company, $product, $year, $month, $reportLink) {
        $stmt = $this->db->prepare("INSERT INTO incoming_qc_reports (company, product, year, month, report_link) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$company, $product, $year, $month, $reportLink]);
    }

    public function updateReport($id, $reportLink) {
        $stmt = $this->db->prepare("UPDATE incoming_qc_reports SET report_link = ? WHERE id = ?");
        return $stmt->execute([$reportLink, $id]);
    }

    public function deleteReport($id) {
        $stmt = $this->db->prepare("DELETE FROM incoming_qc_reports WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
