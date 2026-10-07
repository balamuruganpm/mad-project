<?php
namespace App\Helpers;

class AuthHelper {
    public static function checkSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        if (!isset($_SESSION['role_id']) || !isset($_SESSION['role_name'])) {
            try {
                $userModel = new \App\Models\User();
                $details = $userModel->getUserDetails((int)$_SESSION['user_id']);
                if ($details) {
                    $_SESSION['role_id'] = (int)$details['role_id'];
                    $_SESSION['role_name'] = $details['role_name'];
                    $_SESSION['username'] = $details['username'];
                }
            } catch (\Exception $e) {
                // Ignore database errors here, fallback to session values
            }
        }
    }

    public static function isLoggedIn(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }
}
