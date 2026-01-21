<?php
// controllers/HomeController.php
class HomeController {
    public function index() {
        // Afficher la page d'accueil
        $this->view('home/index');
    }

    public function features() {
        $this->view('home/features');
    }

    public function pricing() {
        $this->view('home/pricing');
    }

    public function about() {
        $this->view('home/about');
    }

    private function view($view) {
        $viewFile = "views/$view.php";
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            // Rediriger vers la page d'accueil si la vue n'existe pas
            header('Location: /');
        }
    }
}
?>

