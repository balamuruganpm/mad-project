<?php
namespace App\Models;

use PDO;

class Document extends BaseModel {
    
    public function getDepartmentJobsAndDocs(string $code): array {
        $stmt = $this->db->prepare("SELECT * FROM departments WHERE code = ?");
        $stmt->execute([$code]);
        $dept = $stmt->fetch();
        
        if (!$dept) {
            return ['dept' => null, 'jobs' => []];
        }

        $query = "SELECT j.id as job_id, j.job_id_code, j.report_name, c.company_name, j.qr_token 
                  FROM jobs j
                  LEFT JOIN customers c ON j.customer_id = c.id
                  ORDER BY j.id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $jobs = $stmt->fetchAll();

        $docsStmt = $this->db->prepare("SELECT d.job_id, d.file_name, d.file_path 
                                  FROM documents d
                                  WHERE d.department_id = ?");
        $docsStmt->execute([$dept['id']]);
        $documents = $docsStmt->fetchAll();

        $jobDocs = [];
        foreach ($documents as $doc) {
            // We use file_name as the key to store multiple types of links
            $jobDocs[$doc['job_id']][$doc['file_name']] = $doc;
        }

        return [
            'dept' => $dept,
            'jobs' => $jobs
        ];
    }

    public function getAllFinalAssemblyDocs(): array {
        $stmt = $this->db->query("SELECT j.id as job_id, j.job_id_code, j.report_name, c.company_name, j.qr_token 
                            FROM jobs j 
                            LEFT JOIN customers c ON j.customer_id = c.id 
                            ORDER BY j.id DESC");
        $jobs = $stmt->fetchAll();

        $docsStmt = $this->db->query("SELECT d.job_id, d.file_name, d.file_path, dept.code as dept_code 
                                FROM documents d
                                JOIN departments dept ON d.department_id = dept.id");
        $documents = $docsStmt->fetchAll();

        $jobDocs = [];
        foreach ($documents as $doc) {
            $jobDocs[$doc['job_id']][$doc['dept_code']][$doc['file_name']] = $doc;
        }

        return ['jobs' => $jobs, 'jobDocs' => $jobDocs];
    }
    
    public function getPublicFinalAssemblyDocs(int $jobId): array {
        $stmt = $this->db->prepare("SELECT j.id as job_id, j.job_id_code, j.report_name, c.company_name, j.qr_token 
                              FROM jobs j 
                              LEFT JOIN customers c ON j.customer_id = c.id 
                              WHERE j.id = ?");
        $stmt->execute([$jobId]);
        $job = $stmt->fetch();

        if (!$job) {
            return ['job' => null, 'jobDocs' => []];
        }

        $docsStmt = $this->db->prepare("SELECT d.file_name, d.file_path, dept.code as dept_code 
                                  FROM documents d
                                  JOIN departments dept ON d.department_id = dept.id
                                  WHERE d.job_id = ?");
        $docsStmt->execute([$jobId]);
        $documents = $docsStmt->fetchAll();

        $jobDocs = [];
        foreach ($documents as $doc) {
            $jobDocs[$doc['dept_code']][$doc['file_name']] = $doc;
        }

        return ['job' => $job, 'jobDocs' => $jobDocs];
    }

    public function getPublicFinalAssemblyDocsByToken(string $token): array {
        $stmt = $this->db->prepare("SELECT j.id as job_id, j.job_id_code, j.report_name, c.company_name, j.qr_token 
                              FROM jobs j 
                              LEFT JOIN customers c ON j.customer_id = c.id 
                              WHERE j.qr_token = ?");
        $stmt->execute([$token]);
        $job = $stmt->fetch();

        if (!$job) {
            return ['job' => null, 'jobDocs' => []];
        }

        $docsStmt = $this->db->prepare("SELECT d.file_name, d.file_path, dept.code as dept_code 
                                  FROM documents d
                                  JOIN departments dept ON d.department_id = dept.id
                                  WHERE d.job_id = ?");
        $docsStmt->execute([$job['job_id']]);
        $documents = $docsStmt->fetchAll();

        $jobDocs = [];
        foreach ($documents as $doc) {
            $jobDocs[$doc['dept_code']][$doc['file_name']] = $doc;
        }

        return ['job' => $job, 'jobDocs' => $jobDocs];
    }

    public function getOldFilePath(int $jobId, int $deptId) {
        $stmt = $this->db->prepare("SELECT file_path FROM documents WHERE job_id = ? AND department_id = ?");
        $stmt->execute([$jobId, $deptId]);
        return $stmt->fetchColumn();
    }

    public function deleteOldDocument(int $jobId, int $deptId) {
        $stmt = $this->db->prepare("DELETE FROM documents WHERE job_id = ? AND department_id = ?");
        return $stmt->execute([$jobId, $deptId]);
    }

    public function saveDocument(int $jobId, int $deptId, string $fileName, string $filePath, string $fileType, int $fileSize, int $userId) {
        $stmt = $this->db->prepare("INSERT INTO documents (job_id, department_id, file_name, file_path, file_type, file_size, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$jobId, $deptId, $fileName, $filePath, $fileType, $fileSize, $userId]);
    }
    
    public function getFilePathById(int $docId) {
        $stmt = $this->db->prepare("SELECT file_path FROM documents WHERE id = ?");
        $stmt->execute([$docId]);
        return $stmt->fetchColumn();
    }
    
    public function deleteDocumentById(int $docId) {
        $stmt = $this->db->prepare("DELETE FROM documents WHERE id = ?");
        return $stmt->execute([$docId]);
    }
}
