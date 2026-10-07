<?php
namespace App\Controllers;

use App\Models\User;

class AuthController extends BaseController {
    public function showLogin(): void {
        // Simple layout-free rendering for login screen
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function login(): void {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $userModel = new User();
        $user = $userModel->findByUsername($username);

        if ($user && ($user['password_hash'] === $password || password_verify($password, $user['password_hash']))) {
            // Migrate plaintext password to hash if needed
            if ($user['password_hash'] === $password) {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $userModel->updatePasswordHash((int)$user['id'], $hash);
            }

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $details = $userModel->getUserDetails($user['id']);
            $_SESSION['user_id'] = $details['id'];
            $_SESSION['username'] = $details['username'];
            $rId = (int)$details['role_id'];
            $_SESSION['role_id'] = $rId;
            $_SESSION['role_name'] = $details['role_name'];
            
            $redirectMap = [
                3 => '/departments/design',
                4 => '/departments/busbar',
                5 => '/departments/ct',
                6 => '/departments/pt',
                7 => '/departments/breaker',
                8 => '/departments/incoming-qc'
            ];
            
            $redirectUrl = $redirectMap[$rId] ?? '/dashboard';
            $this->redirect($redirectUrl);
        } else {
            $error = "Invalid username or password.";
            require __DIR__ . '/../Views/auth/login.php';
        }
    }

    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        $this->redirect('/login');
    }
}
