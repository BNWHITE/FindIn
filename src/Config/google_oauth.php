<?php
// config/google_oauth.php
// Configuration pour l'authentification Google OAuth 2.0

return [
    // Remplacez par vos identifiants Google Cloud Console
    // https://console.cloud.google.com/apis/credentials
    'client_id' => getenv('GOOGLE_CLIENT_ID') ?: 'YOUR_GOOGLE_CLIENT_ID',
    'client_secret' => getenv('GOOGLE_CLIENT_SECRET') ?: 'YOUR_GOOGLE_CLIENT_SECRET',
    'redirect_uri' => getenv('GOOGLE_REDIRECT_URI') ?: 'http://localhost:8000/auth/google/callback',
    
    // Scopes demandés
    'scopes' => [                                                                                                       
        'email',
        'profile',
        'openid'
    ],
    
    // URLs Google OAuth
    'auth_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
    'token_url' => 'https://oauth2.googleapis.com/token',
    'userinfo_url' => 'https://www.googleapis.com/oauth2/v3/userinfo'
];
