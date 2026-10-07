<?php
namespace App\Controllers;

use App\Models\User;
use App\Helpers\AuthHelper;

class ProfileController extends BaseController {

    public function index() {
        AuthHelper::checkSession();
        
        $userModel = new User();
        $userId = $_SESSION['user_id'] ?? 1;
        $user = $userModel->getUserDetails($userId);

        $this->render('profile/index', ['user' => $user]);
    }

    public function saveSignature() {
        AuthHelper::checkSession();
        
        $userId = $_SESSION['user_id'] ?? 1;
        $signatureData = $_POST['signature_data'] ?? null;
        
        if (empty($signatureData)) {
            $_SESSION['error'] = "No signature provided.";
            $this->redirect('/profile');
            return;
        }

        try {
            $userModel = new User();
            $userModel->updateSignature($userId, $signatureData);
            $_SESSION['success'] = "Signature saved successfully.";
        } catch (\Exception $e) {
            $_SESSION['error'] = "Failed to save signature.";
        }
        
        $this->redirect('/profile');
    }
}
