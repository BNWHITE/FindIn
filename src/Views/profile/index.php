<?php
// filepath: /Applications/XAMPP/htdocs/FindIn/src/Views/profile/index.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /FindIn/public/login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - FindIN</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 40px;
        }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .profile-header { display: flex; align-items: center; margin-bottom: 30px; }
        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 40px;
            margin-right: 20px;
        }
        .profile-info h1 { color: #667eea; margin-bottom: 5px; }
        .profile-info p { color: #666; font-size: 14px; }
        .info-group {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 6px;
        }
        .info-group label { display: block; font-weight: 600; color: #667eea; margin-bottom: 5px; font-size: 12px; text-transform: uppercase; }
        .info-group p { color: #333; font-size: 16px; }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="/FindIn/public/dashboard">← Dashboard</a>
        <a href="/FindIn/public/logout">Déconnexion</a>
    </div>

    <div class="container">
        <div class="card">
            <div class="profile-header">
                <div class="avatar">👤</div>
                <div class="profile-info">
                    <h1><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h1>
                    <p><?php echo htmlspecialchars($user['role']); ?></p>
                </div>
            </div>

            <div class="info-group">
                <label>Email</label>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
            </div>

            <div class="info-group">
                <label>Prénom</label>
                <p><?php echo htmlspecialchars($user['prenom']); ?></p>
            </div>

            <div class="info-group">
                <label>Nom</label>
                <p><?php echo htmlspecialchars($user['nom']); ?></p>
            </div>

            <div class="info-group">
                <label>Rôle</label>
                <p><?php echo htmlspecialchars(ucfirst($user['role'])); ?></p>
            </div>

            <div class="info-group">
                <label>Date d'inscription</label>
                <p><?php echo date('d/m/Y à H:i', strtotime($user['cree_le'])); ?></p>
            </div>
        </div>
    </div>
</body>
</html>