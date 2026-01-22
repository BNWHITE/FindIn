<?php
// filepath: /Applications/XAMPP/htdocs/FindIn/src/Views/404.php
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>404 - FindIN</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            text-align: center;
            color: white;
            max-width: 600px;
        }

        h1 {
            font-size: 120px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        h2 {
            font-size: 28px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        p {
            font-size: 18px;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        code {
            background: rgba(0,0,0,0.2);
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }

        .button-group {
            margin-top: 40px;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        a {
            background: white;
            color: #667eea;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s ease;
        }

        a:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .secondary {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid white;
        }

        .secondary:hover {
            background: rgba(255,255,255,0.3);
        }

        .info {
            font-size: 12px;
            margin-top: 30px;
            opacity: 0.7;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.2);
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>404</h1>
        <h2>Page non trouvée</h2>
        <?php
        $request_uri = $_SERVER['REQUEST_URI'] ?? '/';
        ?>
        <p>
            La page <code><?php echo htmlspecialchars($request_uri); ?></code> n'existe pas.
        </p>
        <p>
            Vérifiez l'URL et réessayez, ou utilisez les boutons ci-dessous.
        </p>
        <div class="button-group">
            <a href="/FindIn/public/">🏠 Retour à l'accueil</a>
            <a href="/FindIn/public/login" class="secondary">🔐 Se connecter</a>
        </div>
        <div class="info">
            Si le problème persiste, contactez l'administrateur.
        </div>
    </div>
</body>

</html>