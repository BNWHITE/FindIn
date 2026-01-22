<?php
// filepath: /Applications/XAMPP/htdocs/FindIn/src/Controllers/CompetenceController.php
require_once CONTROLLERS_DIR . '/BaseController.php';

class CompetenceController extends BaseController {

    public function __construct() {
        $this->checkAuth();
    }

    public function index() {
        $this->view('competences/index', ['title' => 'Mes Compétences']);
    }
}