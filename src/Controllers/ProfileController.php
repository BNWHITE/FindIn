<?php
// controllers/ProfileController.php
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Competence.php';
require_once __DIR__ . '/../Models/Department.php';
require_once __DIR__ . '/../Controllers/BaseController.php';

class ProfileController extends BaseController {
    private $userModel;
    private $competenceModel;
    private $departmentModel;

    public function __construct() {
        parent::__construct();
        $this->checkAuth();
        $this->userModel = new User();
        $this->competenceModel = new Competence();
        $this->departmentModel = new Department();
    }

    public function view() {
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getUserById($userId);
        $competences = $this->competenceModel->getUserCompetences($userId);
        
        $data = [
            'user' => $user,
            'competences' => $competences
        ];
        
        $this->view('profile/view', $data);
    }

    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $data = [
                'prenom' => filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING),
                'nom' => filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING),
                'id_departement' => $_POST['id_departement']
            ];
            
            if ($this->userModel->updateProfile($userId, $data)) {
                $_SESSION['user_name'] = $data['prenom'] . ' ' . $data['nom'];
                $this->redirect('/profile');
            } else {
                $data['error'] = 'Erreur lors de la mise à jour';
                $this->viewProfileForm($data);
            }
        } else {
            $this->viewProfileForm();
        }
    }

    private function viewProfileForm($data = []) {
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getUserById($userId);
        $departements = $this->departmentModel->getAllDepartements();
        
        $data = array_merge([
            'user' => $user,
            'departements' => $departements
        ], $data);
        
        $this->view('profile/edit', $data);
    }

    public function competences() {
        $userId = $_SESSION['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['add_competence'])) {
                $competenceId = $_POST['competence_id'];
                $niveau = $_POST['niveau'];
                
                if ($this->competenceModel->addUserCompetence($userId, $competenceId, $niveau)) {
                    $data['success'] = 'Compétence ajoutée avec succès!';
                } else {
                    $data['error'] = 'Erreur lors de l\'ajout de la compétence';
                }
            }
        }
        
        $userCompetences = $this->competenceModel->getUserCompetences($userId);
        $allCompetences = $this->competenceModel->getAllCompetences();
        
        // Filtrer les compétences non déjà ajoutées
        $userCompetenceIds = array_map(function($comp) {
            return $comp['id_competence'];
        }, $userCompetences);
        
        $availableCompetences = array_filter($allCompetences, function($comp) use ($userCompetenceIds) {
            return !in_array($comp['id_competence'], $userCompetenceIds);
        });
        
        $data['user_competences'] = $userCompetences;
        $data['available_competences'] = $availableCompetences;
        
        $this->view('profile/competences', $data);
    }
}
?>
