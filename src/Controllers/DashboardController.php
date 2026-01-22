<?php
/**
 * DashboardController.php
 */
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/User.php';

class DashboardController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->checkAuth();
        $this->userModel = new User();
    }

    public function index() {
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        
        if (!$user) {
            session_destroy();
            $this->redirect('/FindIn/public/login');
        }

        $data = [
            'user' => $user,
            'title' => 'Dashboard'
        ];
        
        $this->view('dashboard/index', $data);
    }
}
?>
