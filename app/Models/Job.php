<?php
namespace App\Models;

use PDO;

class Job extends BaseModel {

    public function getAllJobs() {
        $stmt = $this->db->query("SELECT j.*, c.company_name, t.name as template_name 
                            FROM jobs j 
                            LEFT JOIN customers c ON j.customer_id = c.id 
                            LEFT JOIN templates t ON j.template_id = t.id 
                            ORDER BY j.id DESC");
        return $stmt->fetchAll();
    }

    public function getCreateFormData() {
        $data = [];
        $data['customers'] = $this->db->query("SELECT * FROM customers")->fetchAll();
        $data['templates'] = $this->db->query("SELECT * FROM templates WHERE is_active = 1")->fetchAll();
        $data['departments'] = $this->db->query("SELECT * FROM departments")->fetchAll();
        $data['projects'] = $this->db->query("SELECT * FROM projects")->fetchAll();
        $data['engineers'] = $this->db->query("SELECT u.* FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'Quality Engineer'")->fetchAll();
        return $data;
    }

    public function generateJobIdCode() {
        $yearMonth = date('ym');
        $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM jobs WHERE DATE_FORMAT(created_at, '%y%m') = '$yearMonth'");
        $count = $stmt->fetchColumn() + 1;
        return 'JOB-' . $yearMonth . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function createJob($data) {
        $jobIdCode = $this->generateJobIdCode();
        $qrToken = bin2hex(random_bytes(16));
        $stmt = $this->db->prepare("INSERT INTO jobs (job_id_code, customer_id, template_id, department_id, project_id, assigned_engineer_id, report_name, priority, inspection_date, qr_token) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $jobIdCode, 
            $data['customer_id'], 
            $data['template_id'], 
            $data['department_id'], 
            $data['project_id'], 
            $data['assigned_engineer_id'], 
            $data['report_name'], 
            $data['priority'], 
            $data['inspection_date'], 
            $qrToken
        ]);
        return $this->db->lastInsertId();
    }

    public function getJobForInspection(int $id) {
        $stmt = $this->db->prepare("SELECT j.*, t.fields FROM jobs j LEFT JOIN templates t ON j.template_id = t.id WHERE j.id = ?");
        $stmt->execute([$id]);
        $job = $stmt->fetch();
        
        $savedAttributes = [];
        $signature = null;
        if ($job) {
            $stmt = $this->db->prepare("SELECT field_name, field_value FROM job_attributes WHERE job_id = ?");
            $stmt->execute([$id]);
            $savedAttributes = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

            $stmt = $this->db->prepare("SELECT digital_signature FROM approval_logs WHERE job_id = ? AND action = 'submit' AND digital_signature IS NOT NULL ORDER BY id DESC LIMIT 1");
            $stmt->execute([$id]);
            $signature = $stmt->fetchColumn() ?: null;
        }

        return ['job' => $job, 'savedAttributes' => $savedAttributes, 'signature' => $signature];
    }

    public function saveInspection(int $id, array $attributes, ?string $digitalSignature, int $userId) {
        foreach ($attributes as $key => $val) {
            if ($key === 'digital_signature') continue;
            $stmt = $this->db->prepare("INSERT INTO job_attributes (job_id, field_name, field_value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE field_value = ?");
            $stmt->execute([$id, $key, $val, $val]);
        }
        
        $stmt = $this->db->prepare("INSERT INTO approval_logs (job_id, user_id, action, comments, digital_signature) VALUES (?, ?, 'submit', 'QC inspection completed', ?)");
        $stmt->execute([$id, $userId, $digitalSignature]);
        
        $stmt = $this->db->prepare("UPDATE jobs SET status = 'pending_review', approval_status = 'reviewer_approved' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function approveJob(int $id, int $userId, string $remarks, ?string $digitalSignature = null) {
        $stmt = $this->db->prepare("UPDATE jobs SET status = 'completed', approval_status = 'manager_approved', remarks = ? WHERE id = ?");
        $stmt->execute([$remarks, $id]);

        $stmt = $this->db->prepare("INSERT INTO approval_logs (job_id, user_id, action, comments, digital_signature) VALUES (?, ?, 'manager_approve', ?, ?)");
        return $stmt->execute([$id, $userId, $remarks, $digitalSignature]);
    }

    public function rejectJob(int $id, int $userId, string $remarks) {
        $stmt = $this->db->prepare("UPDATE jobs SET status = 'assigned', approval_status = 'rework_requested', remarks = ? WHERE id = ?");
        $stmt->execute([$remarks, $id]);

        $stmt = $this->db->prepare("INSERT INTO approval_logs (job_id, user_id, action, comments) VALUES (?, ?, 'request_rework', ?)");
        return $stmt->execute([$id, $userId, $remarks]);
    }

    public function getJobFullDetails(int $id = null, string $token = null) {
        if ($token) {
            $stmt = $this->db->prepare("SELECT j.*, c.company_name, t.name as template_name, d.name as department_name, p.name as project_name, u.username as engineer_name, r.name as engineer_role 
                                  FROM jobs j 
                                  LEFT JOIN customers c ON j.customer_id = c.id 
                                  LEFT JOIN templates t ON j.template_id = t.id 
                                  LEFT JOIN departments d ON j.department_id = d.id
                                  LEFT JOIN projects p ON j.project_id = p.id
                                  LEFT JOIN users u ON j.assigned_engineer_id = u.id
                                  LEFT JOIN roles r ON u.role_id = r.id
                                  WHERE j.qr_token = ?");
            $stmt->execute([$token]);
        } else {
            $stmt = $this->db->prepare("SELECT j.*, c.company_name, t.name as template_name, d.name as department_name, p.name as project_name, u.username as engineer_name, r.name as engineer_role 
                                  FROM jobs j 
                                  LEFT JOIN customers c ON j.customer_id = c.id 
                                  LEFT JOIN templates t ON j.template_id = t.id 
                                  LEFT JOIN departments d ON j.department_id = d.id
                                  LEFT JOIN projects p ON j.project_id = p.id
                                  LEFT JOIN users u ON j.assigned_engineer_id = u.id
                                  LEFT JOIN roles r ON u.role_id = r.id
                                  WHERE j.id = ?");
            $stmt->execute([$id]);
        }
        
        $job = $stmt->fetch();
        
        $attributes = [];
        $signers = [];

        if ($job) {
            $stmt = $this->db->prepare("SELECT field_name, field_value FROM job_attributes WHERE job_id = ?");
            $stmt->execute([$job['id']]);
            $attributes = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

            $stmt = $this->db->prepare("SELECT a.digital_signature, u.username, r.name as user_role 
                                        FROM approval_logs a 
                                        LEFT JOIN users u ON a.user_id = u.id 
                                        LEFT JOIN roles r ON u.role_id = r.id 
                                        WHERE a.job_id = ? AND a.digital_signature IS NOT NULL 
                                        ORDER BY a.id ASC");
            $stmt->execute([$job['id']]);
            $signers = $stmt->fetchAll();
        }

        return ['job' => $job, 'attributes' => $attributes, 'signers' => $signers];
    }

    public function checkJobCode(string $code) {
        $stmt = $this->db->prepare("SELECT j.id, j.job_id_code, j.status, c.company_name 
                                  FROM jobs j 
                                  LEFT JOIN customers c ON j.customer_id = c.id 
                                  WHERE j.job_id_code = ?");
        $stmt->execute([$code]);
        return $stmt->fetch();
    }
}
