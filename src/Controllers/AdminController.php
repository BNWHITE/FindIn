<?php
namespace App\Controllers;

use App\Models\Competence;

class AdminController extends BaseController {
    
    public function competences() {
        // 1. Sécurité : Vérifier si l'utilisateur est connecté et admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $_SESSION['error'] = "Accès refusé.";
            header('Location: /login');
            exit();
        }

        $model = new Competence();

        // 2. Traitement du formulaire d'ajout (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_save'])) {
            $nom = trim($_POST['nom'] ?? '');
            
            if (empty($nom)) {
                $_SESSION['error'] = "Le nom est obligatoire.";
            } else {
                $success = $model->create([
                    'nom' => $nom,
                    'type' => $_POST['type_competence'] ?? 'technique',
                    'description' => $_POST['description'] ?? ''
                ]);

                if ($success) {
                    $_SESSION['success'] = "Données enregistrées dans Supabase !";
                    header('Location: /admin/competences'); // Évite le double envoi
                    exit();
                } else {
                    $_SESSION['error'] = "Erreur technique lors de l'enregistrement.";
                }
            }
        }

        // 3. Récupération des données pour la vue
        $competences = $model->getAll();
        
        // 4. Chargement de la vue
        $this->render('admin_competences', ['competences' => $competences]);
    }
}
