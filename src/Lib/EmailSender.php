<?php
/**
 * EmailSender.php - Utilitaire pour envoyer des emails
 */
class EmailSender {
    private static $from_email = 'blacknwhitemanagement@findin.fr';
    private static $from_name = 'FindIN - Gestion des Compétences';
    
    /**
     * Envoyer une invitation par email
     */
    public static function sendInvitation($invitation_token, $email, $prenom, $nom, $lien_acceptation) {
        $subject = 'Invitation FindIN - ' . $nom . ' ' . $prenom;
        $message = self::getInvitationEmailTemplate($prenom, $nom, $lien_acceptation);
        
        return self::send($email, $subject, $message);
    }
    
    /**
     * Envoyer un email de contact
     */
    public static function sendContactEmail($name, $email, $subject, $message) {
        $subject_formatted = 'FindIN - Contact: ' . $subject;
        $html_message = self::getContactEmailTemplate($name, $email, $message);
        
        // Envoyer au support
        return self::send('contact@findin.fr', $subject_formatted, $html_message);
    }
    
    /**
     * Envoyer un email générique
     */
    public static function send($to, $subject, $html_message) {
        // Vérifier si la destination est valide
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            error_log("Email invalide: {$to}");
            return false;
        }
        
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . self::$from_name . " <" . self::$from_email . ">\r\n";
        $headers .= "Reply-To: " . self::$from_email . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        
        $result = mail($to, $subject, $html_message, $headers);
        
        if ($result) {
            error_log("✅ Email envoyé à {$to}: {$subject}");
        } else {
            error_log("❌ Erreur lors de l'envoi de l'email à {$to}: {$subject}");
        }
        
        return $result;
    }
    
    /**
     * Template d'invitation
     */
    private static function getInvitationEmailTemplate($prenom, $nom, $lien_acceptation) {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f4f4f4; }
        .header { background: linear-gradient(135deg, #9333ea, #3b82f6); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: white; padding: 30px; }
        .button { display: inline-block; background: linear-gradient(135deg, #9333ea, #3b82f6); color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .footer { background: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💼 Bienvenue chez FindIN</h1>
        </div>
        <div class="content">
            <p>Bonjour <strong>{$prenom} {$nom}</strong>,</p>
            
            <p>Vous avez été invité à rejoindre FindIN, la plateforme de gestion des compétences de votre entreprise.</p>
            
            <p>Pour créer votre compte et accepter l'invitation, veuillez cliquer sur le bouton ci-dessous. Ce lien est valide pendant 7 jours.</p>
            
            <center>
                <a href="{$lien_acceptation}" class="button">Accepter l'invitation</a>
            </center>
            
            <p>Si vous ne pouvez pas cliquer sur le bouton, copiez et collez le lien suivant dans votre navigateur:</p>
            <p style="background: #f4f4f4; padding: 10px; border-left: 4px solid #9333ea; word-break: break-all;">
                {$lien_acceptation}
            </p>
            
            <p>À bientôt sur FindIN!</p>
            
            <p style="color: #999; font-size: 12px; margin-top: 30px;">
                Cordialement,<br>
                <strong>L'équipe FindIN</strong>
            </p>
        </div>
        <div class="footer">
            <p>&copy; 2026 FindIN. Tous droits réservés.</p>
            <p>Cette invitation a été envoyée à votre adresse email</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
    
    /**
     * Template de contact
     */
    private static function getContactEmailTemplate($name, $email, $message) {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f4f4f4; }
        .header { background: linear-gradient(135deg, #9333ea, #3b82f6); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: white; padding: 30px; }
        .sender-info { background: #f9f9f9; padding: 15px; border-left: 4px solid #9333ea; margin-bottom: 20px; }
        .footer { background: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📬 Nouveau message de contact</h1>
        </div>
        <div class="content">
            <div class="sender-info">
                <p><strong>Nom:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
            </div>
            
            <div style="white-space: pre-wrap; background: #f9f9f9; padding: 15px; border-radius: 6px;">
{$message}
            </div>
            
            <p style="margin-top: 30px; color: #999; font-size: 12px;">
                Message reçu via le formulaire de contact FindIN
            </p>
        </div>
        <div class="footer">
            <p>&copy; 2026 FindIN. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
?>
