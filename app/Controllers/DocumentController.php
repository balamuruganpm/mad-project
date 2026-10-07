<?php
namespace App\Controllers;

use App\Models\Document;
use App\Helpers\AuthHelper;

class DocumentController extends BaseController {

    private function getDepartmentData(string $code): array {
        $docModel = new Document();
        return $docModel->getDepartmentJobsAndDocs($code);
    }

    private function checkAccess(int $allowedRoleId): void {
        $rId = (int)($_SESSION['role_id'] ?? 0);
        // Admin (1) and FA (2) have global read access
        if (in_array($rId, [1, 2])) return;
        if ($rId !== $allowedRoleId) {
            die("Unauthorized access to this department.");
        }
    }

    public function incomingQC(): void {
        AuthHelper::checkSession();
        $this->checkAccess(8);
        $data = $this->getDepartmentData('IQC');
        $this->render('departments/incoming_qc', [
            'title' => 'Incoming QC',
            'dept' => $data['dept'],
            'jobs' => $data['jobs']
        ]);
    }

    public function design(): void {
        AuthHelper::checkSession();
        $this->checkAccess(3);
        $data = $this->getDepartmentData('DESIGN');
        $this->render('departments/design', [
            'title' => 'Design Department',
            'dept' => $data['dept'],
            'jobs' => $data['jobs']
        ]);
    }

    public function busbar(): void {
        AuthHelper::checkSession();
        $this->checkAccess(4);
        $data = $this->getDepartmentData('BUSBAR');
        $this->render('departments/busbar', [
            'title' => 'Busbar (Incoming QC)',
            'dept' => $data['dept'],
            'jobs' => $data['jobs']
        ]);
    }

    public function ct(): void {
        AuthHelper::checkSession();
        $this->checkAccess(5);
        $data = $this->getDepartmentData('CT');
        $this->render('departments/ct', [
            'title' => 'CT Department',
            'dept' => $data['dept'],
            'jobs' => $data['jobs']
        ]);
    }

    public function pt(): void {
        AuthHelper::checkSession();
        $this->checkAccess(6);
        $data = $this->getDepartmentData('PT');
        $this->render('departments/pt', [
            'title' => 'PT Department',
            'dept' => $data['dept'],
            'jobs' => $data['jobs']
        ]);
    }

    public function breaker(): void {
        AuthHelper::checkSession();
        $this->checkAccess(7);
        $data = $this->getDepartmentData('BREAKER');
        $this->render('departments/breaker', [
            'title' => 'Breaker Department',
            'dept' => $data['dept'],
            'jobs' => $data['jobs']
        ]);
    }

    public function finalAssembly(): void {
        AuthHelper::checkSession();
        $docModel = new Document();
        $data = $docModel->getAllFinalAssemblyDocs();

        $this->render('departments/final_assembly', [
            'title' => 'Final Assembly (Consolidated Dashboard)',
            'jobs' => $data['jobs'],
            'jobDocs' => $data['jobDocs']
        ]);
    }

    private function blockReadOnlyGlobal(): void {
        $rId = (int)($_SESSION['role_id'] ?? 0);
        if ($rId == 1) { // Only Admin is globally read-only
            die("Read-only role cannot perform this action.");
        }
    }

    private function blockReadOnlyDepartments(): void {
        $rId = (int)($_SESSION['role_id'] ?? 0);
        if (in_array($rId, [1, 2])) {
            die("Role cannot perform this action in individual departments.");
        }
    }

    public function createJob(): void {
        AuthHelper::checkSession();
        $this->blockReadOnlyGlobal();
        $companyName = $_POST['company_name'] ?? '';
        $jobIdCode = $_POST['job_id_code'] ?? '';

        if (empty($companyName) || empty($jobIdCode)) {
            die("Customer Name and Job ID are required.");
        }

        $docModel = new Document();
        $db = $docModel->getDb(); // We need a getDb method or just use raw queries if possible

        // Let's use the DB instance
        $stmt = $db->prepare("SELECT id FROM customers WHERE company_name = ?");
        $stmt->execute([$companyName]);
        $customerId = $stmt->fetchColumn();

        if (!$customerId) {
            $stmt = $db->prepare("INSERT INTO customers (company_name) VALUES (?)");
            $stmt->execute([$companyName]);
            $customerId = $db->lastInsertId();
        }

        $qrToken = bin2hex(random_bytes(16));
        $stmt = $db->prepare("INSERT INTO jobs (job_id_code, customer_id, template_id, department_id, project_id, report_name, priority, inspection_date, qr_token) VALUES (?, ?, 1, 1, 1, 'Quick Job', 'medium', CURDATE(), ?)");
        $stmt->execute([$jobIdCode, $customerId, $qrToken]);

        header("Location: " . BASE_URL . "/departments/final-assembly");
        exit;
    }

    public function saveLinks(): void {
        AuthHelper::checkSession();
        $this->blockReadOnlyGlobal();
        $jobId = (int)($_POST['job_id'] ?? 0);
        $links = $_POST['links'] ?? [];

        if ($jobId <= 0) {
            die("Invalid job ID.");
        }

        $docModel = new Document();
        $db = $docModel->getDb();

        // Get department IDs
        $deptCodes = ['DESIGN', 'BUSBAR', 'CT', 'PT', 'BREAKER', 'IQC'];
        
        foreach ($deptCodes as $code) {
            if (!empty($links[$code])) {
                $url = trim($links[$code]);
                
                $stmt = $db->prepare("SELECT id FROM departments WHERE code = ?");
                $stmt->execute([$code]);
                $deptId = $stmt->fetchColumn();
                
                if ($deptId) {
                    $docModel->deleteOldDocument($jobId, $deptId);
                    // Save the URL as file_path, and file_type as url
                    $docModel->saveDocument(
                        $jobId,
                        $deptId,
                        'External Link',
                        $url,
                        'url',
                        0,
                        $_SESSION['user_id']
                    );
                }
            }
        }

        header("Location: " . BASE_URL . "/departments/final-assembly");
        exit;
    }

    public function saveDepartmentLinks(): void {
        AuthHelper::checkSession();
        $this->blockReadOnlyDepartments();
        $jobId = (int)($_POST['job_id'] ?? 0);
        $deptCode = $_POST['dept_code'] ?? '';
        $links = $_POST['links'] ?? [];
        $redirectUrl = $_POST['redirect_url'] ?? '/departments/final-assembly';

        if ($jobId <= 0 || empty($deptCode)) {
            die("Invalid request parameters.");
        }

        $docModel = new Document();
        $db = $docModel->getDb();

        $stmt = $db->prepare("SELECT id FROM departments WHERE code = ?");
        $stmt->execute([$deptCode]);
        $deptId = $stmt->fetchColumn();
        
        if ($deptId) {
            // Only delete old links for the specific keys we are updating (optional, or just delete all for that dept and recreate)
            $docModel->deleteOldDocument($jobId, $deptId);

            foreach ($links as $fileName => $url) {
                if (!empty($url)) {
                    $docModel->saveDocument(
                        $jobId,
                        $deptId,
                        $fileName, // use the key as the label e.g., 'checklist_doc'
                        trim($url),
                        'url',
                        0,
                        $_SESSION['user_id']
                    );
                }
            }
        }

        header("Location: " . BASE_URL . $redirectUrl);
        exit;
    }

    public function upload(): void {
        AuthHelper::checkSession();
        $this->blockReadOnlyDepartments();
        
        $jobId = (int)($_POST['job_id'] ?? 0);
        $deptId = (int)($_POST['department_id'] ?? 0);
        $redirectUrl = $_POST['redirect_url'] ?? '/dashboard';

        if ($jobId <= 0 || $deptId <= 0 || empty($_FILES['doc_file']['name'])) {
            die("Invalid request parameters.");
        }

        $file = $_FILES['doc_file'];
        $fileName = basename($file['name']);
        
        // Security checks for file extension
        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx'];
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowedExtensions)) {
            die("Error: Invalid file type. Only PDF, Image, and Office documents are allowed.");
        }

        // Ensure upload directory exists
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0777, true);
        }

        $uniqueName = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $fileName);
        $targetPath = rtrim(UPLOAD_DIR, '/\\') . DIRECTORY_SEPARATOR . $uniqueName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $docModel = new Document();
            try {
                // Delete previous file if exists to prevent duplicates for a single department-job combination
                $oldFile = $docModel->getOldFilePath($jobId, $deptId);
                if ($oldFile && file_exists($oldFile)) {
                    unlink($oldFile); // removed @ error suppression
                }

                $docModel->deleteOldDocument($jobId, $deptId);

                // Insert new record
                $docModel->saveDocument(
                    $jobId,
                    $deptId,
                    $fileName,
                    $targetPath,
                    $file['type'],
                    $file['size'],
                    $_SESSION['user_id']
                );

                header("Location: " . BASE_URL . $redirectUrl);
                exit;
            } catch (\Exception $e) {
                // Should use flash messages instead of die, but for now we sanitize output
                die("An error occurred saving upload info. Please try again later.");
            }
        } else {
            die("Error uploading file to target server path.");
        }
    }

    public function deleteDoc(): void {
        AuthHelper::checkSession();
        $this->blockReadOnlyDepartments();

        $docId = (int)($_POST['doc_id'] ?? 0);
        $redirectUrl = $_POST['redirect_url'] ?? '/dashboard';

        if ($docId <= 0) {
            die("Invalid document ID.");
        }

        $docModel = new Document();
        try {
            $filePath = $docModel->getFilePathById($docId);

            if ($filePath && file_exists($filePath)) {
                if(!unlink($filePath)) {
                    // Could log error here
                }
            }

            $docModel->deleteDocumentById($docId);

            header("Location: " . BASE_URL . $redirectUrl);
            exit;
        } catch (\Exception $e) {
            die("An error occurred deleting the document. Please try again later.");
        }
    }

    public function publicFinalAssembly($params): void {
        $id = (int)$params['id'];
        $docModel = new Document();
        
        $data = $docModel->getPublicFinalAssemblyDocs($id);
        $job = $data['job'];
        $jobDocs = $data['jobDocs'];

        if (!$job) {
            http_response_code(404);
            die("<h3>Report Not Found</h3>");
        }

        // Layout-free public render or light layout
        require __DIR__ . '/../Views/departments/public_final_assembly.php';
    }

    public function publicView($params): void {
        $token = $params['token'];
        $docModel = new Document();
        
        $data = $docModel->getPublicFinalAssemblyDocsByToken($token);
        $job = $data['job'];
        $jobDocs = $data['jobDocs'];

        if (!$job) {
            http_response_code(404);
            die("<h3>Report Not Found</h3>");
        }

        require __DIR__ . '/../Views/departments/public_final_assembly.php';
    }
}
