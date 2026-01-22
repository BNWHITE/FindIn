<?php
/**
 * ContactController.php - Gestion du formulaire de contact
 */
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Lib/EmailSender.php';

class ContactController extends BaseController {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $message = '';
        $error = '';
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $message_text = trim($_POST['message'] ?? '');
            
            // Validation
            if (empty($name) || empty($email) || empty($subject) || empty($message_text)) {
                $error = 'Tous les champs sont requis';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Email invalide';
            } elseif (strlen($message_text) < 10) {
                $error = 'Le message doit contenir au moins 10 caractères';
            } else {
                // Envoyer l'email à blacknwhitemanagement@findin.fr
                if (EmailSender::sendContactEmail($name, $email, $subject, $message_text)) {
                    $success = true;
                    $message = 'Votre message a été envoyé avec succès. Nous vous répondrons dès que possible!';
                    // Réinitialiser le formulaire
                    $_POST = [];
                } else {
                    $error = 'Erreur lors de l\'envoi du message. Veuillez réessayer.';
                }
            }
        }
        
        $data = [
            'message' => $message,
            'error' => $error,
            'success' => $success
        ];
        
        $this->view('contact', $data);
    }
}
?>
