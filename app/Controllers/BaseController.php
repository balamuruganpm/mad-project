<?php
namespace App\Controllers;

class BaseController {
    protected function render(string $viewPath, array $data = []): void {
        extract($data);
        
        $viewFile = __DIR__ . '/../Views/' . $viewPath . '.php';
        if (file_exists($viewFile)) {
            // Render inside layouts
            require __DIR__ . '/../Views/layout/header.php';
            require __DIR__ . '/../Views/layout/sidebar.php';
            require $viewFile;
            require __DIR__ . '/../Views/layout/footer.php';
        } else {
            die("View file [{$viewPath}] not found.");
        }
    }

    protected function json(array $data, int $statusCode = 200): void {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): void {
        header("Location: " . BASE_URL . $url);
        exit;
    }
}
