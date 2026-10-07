<?php
namespace App\Controllers;

use App\Models\IncomingQc;
use App\Helpers\AuthHelper;

class IncomingQcController extends BaseController {
    private function checkAccess(): void {
        $rId = (int)($_SESSION['role_id'] ?? 0);
        // Admin (1) and FA (2) have global read access, IQC (8) has full access
        if (in_array($rId, [1, 2, 8])) return;
        die("Unauthorized access to Incoming QC.");
    }

    private function blockReadOnly(): void {
        $rId = (int)($_SESSION['role_id'] ?? 0);
        if (in_array($rId, [1, 2])) {
            die("Read-only role cannot perform this action.");
        }
    }

    public function index(): void {
        AuthHelper::checkSession();
        $this->checkAccess();
        
        $model = new IncomingQc();
        $reports = $model->getAllReports();

        // Also fetch dropdown options
        $db = $model->getDb();
        $stmt = $db->query("SELECT * FROM iqc_dropdown_options ORDER BY category, value");
        $optionsRaw = $stmt->fetchAll();
        $options = ['company' => [], 'product' => [], 'year' => [], 'month' => []];
        foreach($optionsRaw as $opt) {
            if(isset($options[$opt['category']])) {
                $options[$opt['category']][] = $opt['value'];
            }
        }

        $this->render('departments/incoming_qc', [
            'title' => 'Incoming QC Department',
            'reports' => $reports,
            'options' => $options
        ]);
    }

    public function store(): void {
        AuthHelper::checkSession();
        $this->blockReadOnly();
        
        $company = $_POST['company'] ?? '';
        $product = $_POST['product'] ?? '';
        $year = $_POST['year'] ?? '';
        $month = $_POST['month'] ?? '';
        $report_link = $_POST['report_link'] ?? '';

        if (!empty($company) && !empty($product) && !empty($year) && !empty($month)) {
            $model = new IncomingQc();
            $model->createReport($company, $product, $year, $month, $report_link);
        }

        header("Location: " . BASE_URL . "/departments/incoming-qc");
        exit;
    }

    public function delete(): void {
        AuthHelper::checkSession();
        $this->blockReadOnly();
        
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $model = new IncomingQc();
            $model->deleteReport($id);
        }

        header("Location: " . BASE_URL . "/departments/incoming-qc");
        exit;
    }

    public function addCategoryOption($params): void {
        AuthHelper::checkSession();
        $this->blockReadOnly();
        $category = $params['category'] ?? '';
        
        $validCats = ['company', 'product', 'year'];
        if (!in_array($category, $validCats)) {
            die("Invalid category");
        }
        
        $model = new IncomingQc();
        $db = $model->getDb();
        $stmt = $db->prepare("SELECT value FROM iqc_dropdown_options WHERE category = ? ORDER BY value");
        $stmt->execute([$category]);
        $existing = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        $this->render('departments/iqc_add_option', [
            'title' => 'Add ' . ucfirst($category),
            'category' => $category,
            'existing' => $existing
        ]);
    }

    public function addOption(): void {
        AuthHelper::checkSession();
        $this->blockReadOnly();

        $category = $_POST['category'] ?? '';
        $value = trim($_POST['value'] ?? '');

        $validCats = ['company', 'product', 'year', 'month'];
        if (in_array($category, $validCats) && !empty($value)) {
            $model = new IncomingQc();
            $db = $model->getDb();
            try {
                $stmt = $db->prepare("INSERT INTO iqc_dropdown_options (category, value) VALUES (?, ?)");
                $stmt->execute([$category, $value]);
            } catch (\Exception $e) {
                // Ignore duplicates
            }
        }
        $redirect = $_POST['redirect_url'] ?? BASE_URL . '/departments/incoming-qc';
        header("Location: " . $redirect);
        exit;
    }
}
