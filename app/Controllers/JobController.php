<?php
namespace App\Controllers;

use App\Models\Job;
use App\Helpers\AuthHelper;

class JobController extends BaseController {
    public function index(): void {
        AuthHelper::checkSession();
        try {
            $jobModel = new Job();
            $jobs = $jobModel->getAllJobs();
        } catch (\Exception $e) {
            $jobs = [];
        }

        $this->render('jobs/list', [
            'jobs' => $jobs
        ]);
    }

    public function create(): void {
        AuthHelper::checkSession();
        try {
            $jobModel = new Job();
            $data = $jobModel->getCreateFormData();
        } catch (\Exception $e) {
            $data = [
                'customers' => [],
                'templates' => [],
                'departments' => [],
                'projects' => [],
                'engineers' => []
            ];
        }

        $this->render('jobs/create', $data);
    }

    public function store(): void {
        AuthHelper::checkSession();
        
        $data = [
            'customer_id' => (int)($_POST['customer_id'] ?? 1),
            'template_id' => (int)($_POST['template_id'] ?? 1),
            'department_id' => (int)($_POST['department_id'] ?? 1),
            'project_id' => (int)($_POST['project_id'] ?? 1),
            'assigned_engineer_id' => !empty($_POST['assigned_engineer_id']) ? (int)$_POST['assigned_engineer_id'] : null,
            'report_name' => trim($_POST['report_name'] ?? 'Inspection Report'),
            'priority' => $_POST['priority'] ?? 'medium',
            'inspection_date' => $_POST['inspection_date'] ?? date('Y-m-d')
        ];

        try {
            $jobModel = new Job();
            $jobModel->createJob($data);
            $this->redirect('/jobs');
        } catch (\Exception $e) {
            die("Error saving job record. Please try again.");
        }
    }

    public function inspect($params): void {
        AuthHelper::checkSession();
        $id = (int)$params['id'];
        
        try {
            $jobModel = new Job();
            $jobData = $jobModel->getJobForInspection($id);
            $job = $jobData['job'];
            if (!$job) {
                die("Job not found.");
            }
            $fields = json_decode($job['fields'] ?? '[]', true);
            $userId = $_SESSION['user_id'] ?? 1;
            $userModel = new \App\Models\User();
            $user = $userModel->getUserDetails($userId);

            $this->render('jobs/inspect', [
                'job' => $job,
                'fields' => $fields,
                'savedAttributes' => $jobData['savedAttributes'],
                'signature' => $jobData['signature'],
                'userSignature' => $user['signature'] ?? null
            ]);
        } catch (\Exception $e) {
            die("Job not found or error occurred.");
        }
    }

    public function saveInspection($params): void {
        AuthHelper::checkSession();
        $id = (int)$params['id'];
        
        $attributes = $_POST['attributes'] ?? [];
        $digitalSignature = $attributes['digital_signature'] ?? null;
        $userId = $_SESSION['user_id'] ?? 1;

        try {
            $jobModel = new Job();
            $jobModel->saveInspection($id, $attributes, $digitalSignature, $userId);
            $this->redirect('/jobs');
        } catch (\Exception $e) {
            die("Error saving inspection details.");
        }
    }

    public function approve($params): void {
        AuthHelper::checkSession();
        $id = (int)$params['id'];
        $remarks = trim($_POST['remarks'] ?? 'QC approved by Manager.');
        $digitalSignature = $_POST['manager_signature'] ?? null;
        $userId = $_SESSION['user_id'] ?? 1;
        
        try {
            $jobModel = new Job();
            $jobModel->approveJob($id, $userId, $remarks, $digitalSignature);
            $this->redirect('/jobs');
        } catch (\Exception $e) {
            die("Error approving job.");
        }
    }

    public function reject($params): void {
        AuthHelper::checkSession();
        $id = (int)$params['id'];
        $remarks = trim($_POST['remarks'] ?? 'Rework requested by Manager.');
        $userId = $_SESSION['user_id'] ?? 1;
        
        try {
            $jobModel = new Job();
            $jobModel->rejectJob($id, $userId, $remarks);
            $this->redirect('/jobs');
        } catch (\Exception $e) {
            die("Error rejecting job.");
        }
    }

    public function certificate($params): void {
        AuthHelper::checkSession();
        $id = (int)$params['id'];

        try {
            $jobModel = new Job();
            $data = $jobModel->getJobFullDetails($id);
            $job = $data['job'];
            $attributes = $data['attributes'];
            $signers = $data['signers'];
        } catch (\Exception $e) {
            $job = null;
            $attributes = [];
            $signers = [];
        }

        if (!$job) {
            http_response_code(404);
            die("<h3>Certificate Not Found</h3>");
        }

        require __DIR__ . '/../Views/jobs/certificate.php';
    }

    public function publicView($params): void {
        $token = $params['token'];

        try {
            $settingModel = new \App\Models\Setting();
            $qrRequireLogin = $settingModel->getSetting('qr_require_login', '0');
            
            if ($qrRequireLogin === '1' && !isset($_SESSION['user_id'])) {
                header("Location: " . BASE_URL . "/login");
                exit;
            }

            $jobModel = new Job();
            $data = $jobModel->getJobFullDetails(null, $token);
            $job = $data['job'];
            $attributes = $data['attributes'];
            $signers = $data['signers'];
        } catch (\Exception $e) {
            $job = null;
            $attributes = [];
            $signers = [];
        }

        if (!$job) {
            http_response_code(404);
            die("<h3>Certification Report Not Found</h3>");
        }

        require __DIR__ . '/../Views/jobs/public_view.php';
    }

    public function checkJob($params): void {
        $code = $params['code'];
        
        try {
            $jobModel = new Job();
            $job = $jobModel->checkJobCode($code);
            
            if ($job) {
                $this->json(['success' => true, 'job' => $job]);
            } else {
                $this->json(['success' => false, 'message' => 'Job ID code not found in database.']);
            }
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'An error occurred while checking job code.']);
        }
    }
}
