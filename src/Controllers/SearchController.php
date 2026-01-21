<?php
// controllers/SearchController.php
require_once __DIR__ . '/../Models/Competence.php';
require_once __DIR__ . '/../Models/Department.php';
require_once __DIR__ . '/../Controllers/BaseController.php';

class SearchController extends BaseController {
    private $competenceModel;
    private $departmentModel;

    public function __construct() {
        parent::__construct();
        $this->checkAuth();
        $this->competenceModel = new Competence();
        $this->departmentModel = new Department();
    }

    public function index() {
        $searchResults = [];
        $searchTerm = '';
        $filters = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['q'])) {
            $searchTerm = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_STRING);
            $filters = [
                'departement' => $_GET['departement'] ?? '',
                'niveau_min' => $_GET['niveau_min'] ?? 1
            ];
            
            $searchResults = $this->competenceModel->searchByCompetence($searchTerm, $filters);
        }
        
        $departements = $this->departmentModel->getAllDepartements();
        
        $data = [
            'searchResults' => $searchResults,
            'searchTerm' => $searchTerm,
            'filters' => $filters,
            'departements' => $departements
        ];
        
        $this->view('search/index', $data);
    }
}
?>
