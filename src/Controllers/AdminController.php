<?php
// controllers/AdminController.php
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Competence.php';
require_once __DIR__ . '/../Models/Department.php';
require_once __DIR__ . '/../Controllers/BaseController.php';

class AdminController extends BaseController {
    private $userModel;
    private $competenceModel;
    private $departmentModel;

    public function __construct() {
        parent::__construct();
        $this->checkRole(['admin']);
        $this->userModel = new User();
        $this->competenceModel = new Competence();
        $this->departmentModel = new Department();
    }

    public function dashboard() {
        $users = $this->userModel->getAllUsers();
        $competences = $this->competenceModel->getAllCompetences();
        
        $data = [
            'totalUsers' => count($users),
            'totalCompetences' => count($competences),
            'recentUsers' => array_slice($users, 0, 5),
            'stats' => $this->getStats()
        ];
        
        $this->view('admin/dashboard', $data);
    }

    public function users() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['update_role'])) {
                $userId = $_POST['user_id'];
                $role = $_POST['role'];
                
                if ($this->userModel->setUserRole($userId, $role)) {
                    $data['success'] = 'Rôle mis à jour avec succès';
                } else {
                    $data['error'] = 'Erreur lors de la mise à jour du rôle';
                }
            }
        }
        
        $users = $this->userModel->getAllUsers();
        $departements = $this->departmentModel->getAllDepartements();
        
        $data = [
            'users' => $users,
            'departements' => $departements
        ];
        
        $this->view('admin/users', $data);
    }

    public function competences() {
        $competences = $this->competenceModel->getAllCompetences();
        
        $data = [
            'competences' => $competences
        ];
        
        $this->view('admin/competences', $data);
    }

    private function getStats() {
        // Statistiques basiques
        return [
            'users_by_role' => [
                'employe' => 0,
                'manager' => 0,
                'rh' => 0,
                'admin' => 0
            ],
            'competences_by_type' => [
                'savoir_faire' => 0,
                'savoir_etre' => 0,
                'expertise' => 0
            ]
        ];
    }
}
?>
