<?php if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../views/layouts/header.php';
?>
<main class="container">
    <section class="section">
        <h1>FAQ — Questions fréquentes</h1>
        <p>Bienvenue sur la page FAQ. Vous trouverez ici les réponses aux questions les plus courantes concernant FindIN, l'utilisation des fonctionnalités, la confidentialité et la gestion des compétences.</p>

        <div class="grid" style="margin-top:2rem;">
            <div class="card">
                <h3>Comment fonctionne la validation des compétences ?</h3>
                <p>Les compétences peuvent être ajoutées par chaque utilisateur. Les validateurs (RH / Managers) peuvent confirmer une compétence après vérification. Une fois validée, la compétence apparaît comme "Validée" sur le profil.</p>
            </div>

            <div class="card">
                <h3>Qui peut valider une compétence ?</h3>
                <p>Les rôles "manager" et "rh" peuvent effectuer des validations. L'administrateur a des droits étendus pour gérer les validateurs et les règles de validation.</p>
            </div>

            <div class="card">
                <h3>Comment protéger mes données ?</h3>
                <p>FindIN prend la confidentialité au sérieux : seules les personnes autorisées peuvent voir certaines informations. Consultez notre <a href="/privacy">politique de confidentialité</a> pour les détails.</p>
            </div>

            <div class="card">
                <h3>Puis-je exporter mes compétences ?</h3>
                <p>Oui — l'option d'export est disponible depuis votre tableau de bord. Vous pouvez exporter au format CSV.</p>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../views/layouts/footer.php';
