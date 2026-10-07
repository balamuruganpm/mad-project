<?php
namespace App\Controllers;

use App\Services\Database;
use PDO;

class DashboardController extends BaseController {
    public function index(): void {
        // Enforce user authentication
        \App\Helpers\AuthHelper::checkSession();

        $rId = (int)($_SESSION['role_id'] ?? 0);
        $redirectMap = [
            3 => '/departments/design',
            4 => '/departments/busbar',
            5 => '/departments/ct',
            6 => '/departments/pt',
            7 => '/departments/breaker',
            8 => '/departments/incoming-qc'
        ];
        
        if (isset($redirectMap[$rId])) {
            $this->redirect($redirectMap[$rId]);
            return;
        }

        $roleName = $_SESSION['role_name'] ?? '';
        if (empty($roleName) && isset($_SESSION['user_id'])) {
            $userModel = new \App\Models\User();
            $details = $userModel->getUserDetails($_SESSION['user_id']);
            if ($details) {
                $_SESSION['role_name'] = $details['role_name'];
                $roleName = $details['role_name'];
            }
        }

        $db = Database::getConnection();

        // Query metrics
        try {
            $totalJobsVal = $db->query("SELECT COUNT(*) FROM jobs")->fetchColumn();
            $totalJobs = ($totalJobsVal !== false) ? (int)$totalJobsVal : 0;

            $pendingJobsVal = $db->query("SELECT COUNT(*) FROM jobs WHERE status != 'completed'")->fetchColumn();
            $pendingJobs = ($pendingJobsVal !== false) ? (int)$pendingJobsVal : 0;

            $completedJobsVal = $db->query("SELECT COUNT(*) FROM jobs WHERE status = 'completed'")->fetchColumn();
            $completedJobs = ($completedJobsVal !== false) ? (int)$completedJobsVal : 0;

            $rejectedJobsVal = $db->query("SELECT COUNT(*) FROM jobs WHERE approval_status = 'rejected'")->fetchColumn();
            $rejectedJobs = ($rejectedJobsVal !== false) ? (int)$rejectedJobsVal : 0;



            // Fetch jobs and department-wise documents for the dynamic status grid
            $stmt = $db->query("SELECT j.id as job_id, j.job_id_code, j.report_name, c.company_name, j.status, j.qr_token
                                FROM jobs j 
                                LEFT JOIN customers c ON j.customer_id = c.id 
                                ORDER BY j.id DESC");
            $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch dynamic departments
            $deptStmt = $db->query("SELECT id, name, code FROM departments WHERE code != 'FINAL' ORDER BY id ASC");
            $departments = $deptStmt->fetchAll(PDO::FETCH_ASSOC);

            $docsStmt = $db->query("SELECT d.job_id, d.file_name, d.file_path, dept.code as dept_code 
                                    FROM documents d
                                    JOIN departments dept ON d.department_id = dept.id");
            $documents = $docsStmt->fetchAll(PDO::FETCH_ASSOC);

            $jobDocs = [];
            foreach ($documents as $doc) {
                $jobDocs[$doc['job_id']][$doc['dept_code']] = $doc;
            }

            // Calculate exact pending tasks
            $pendingTasks = [];
            foreach ($jobs as $job) {
                if ($job['status'] !== 'completed') {
                    foreach ($departments as $dept) {
                        if (!isset($jobDocs[$job['job_id']][$dept['code']])) {
                            $pendingTasks[] = [
                                'job_id_code' => $job['job_id_code'],
                                'department_name' => $dept['name']
                            ];
                        }
                    }
                }
            }

            $settingModel = new \App\Models\Setting();
            $qrRequireLogin = $settingModel->getSetting('qr_require_login', '0');

        } catch (\Exception $e) {
            error_log("Dashboard query error: " . $e->getMessage());
            // Fallback metrics in case db schema is not running/connected
            $totalJobs = 0;
            $pendingJobs = 0;
            $completedJobs = 0;
            $rejectedJobs = 0;
            $jobs = [];
            $departments = [];
            $pendingTasks = [];
            $jobDocs = [];
            $qrRequireLogin = '0';
        }

        $this->render('dashboard/index', [
            'username' => $_SESSION['username'] ?? 'User',
            'roleName' => $roleName,
            'totalJobs' => $totalJobs,
            'pendingJobs' => $pendingJobs,
            'completedJobs' => $completedJobs,
            'rejectedJobs' => $rejectedJobs,
            'jobs' => $jobs,
            'departments' => $departments,
            'pendingTasks' => $pendingTasks,
            'jobDocs' => $jobDocs,
            'qrRequireLogin' => $qrRequireLogin
        ]);
    }

    public function toggleQrLogin() {
        AuthHelper::checkSession();
        
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $value = isset($data['require_login']) && $data['require_login'] ? '1' : '0';

        try {
            $settingModel = new \App\Models\Setting();
            $settingModel->updateSetting('qr_require_login', $value);
            $this->json(['success' => true]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to update setting.']);
        }
    }

    public function guide(): void {
        \App\Helpers\AuthHelper::checkSession();

        $this->render('dashboard/guide', [
            'username' => $_SESSION['username']
        ]);
    }
}
