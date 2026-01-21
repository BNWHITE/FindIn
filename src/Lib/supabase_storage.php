<?php
/**
 * Supabase Storage Helper
 * Upload, download et gestion des fichiers via l'API Supabase Storage
 */

class SupabaseStorage {
    private $projectUrl;
    private $apiKey;
    private $bucket;
    
    public function __construct($bucket = 'cvs') {
        // Supabase project credentials
        $this->projectUrl = getenv('SUPABASE_URL') ?: 'https://ugdkdrdgxtfwsehzpmvm.supabase.co';
        $this->apiKey = getenv('SUPABASE_ANON_KEY') ?: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InVnZGtkcmRneHRmd3NlaHpwbXZtIiwicm9sZSI6ImFub24iLCJpYXQiOjE3MzY3NjcyNTcsImV4cCI6MjA1MjM0MzI1N30.bBJH8ygJc4ViwmiRqX3X0bqX9-bHNu_EGvm_c8XZXCM';
        $this->bucket = $bucket;
    }
    
    /**
     * Upload un fichier vers Supabase Storage
     * @param string $filePath Chemin local du fichier
     * @param string $storagePath Chemin dans le bucket (ex: "user_123/cv.pdf")
     * @param string $contentType Type MIME du fichier
     * @return array|false Résultat de l'upload ou false en cas d'erreur
     */
    public function upload($filePath, $storagePath, $contentType = 'application/pdf') {
        if (!file_exists($filePath)) {
            return ['error' => 'Fichier introuvable'];
        }
        
        $fileContent = file_get_contents($filePath);
        $url = "{$this->projectUrl}/storage/v1/object/{$this->bucket}/{$storagePath}";
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $fileContent,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$this->apiKey}",
                "Content-Type: {$contentType}",
                "x-upsert: true"
            ]
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'success' => true,
                'path' => $storagePath,
                'public_url' => $this->getPublicUrl($storagePath)
            ];
        }
        
        return ['error' => $response, 'code' => $httpCode];
    }
    
    /**
     * Supprime un fichier de Supabase Storage
     * @param string $storagePath Chemin dans le bucket
     * @return bool
     */
    public function delete($storagePath) {
        $url = "{$this->projectUrl}/storage/v1/object/{$this->bucket}/{$storagePath}";
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$this->apiKey}"
            ]
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode >= 200 && $httpCode < 300;
    }
    
    /**
     * Obtient l'URL publique d'un fichier
     * @param string $storagePath Chemin dans le bucket
     * @return string URL publique
     */
    public function getPublicUrl($storagePath) {
        return "{$this->projectUrl}/storage/v1/object/public/{$this->bucket}/{$storagePath}";
    }
    
    /**
     * Obtient une URL signée temporaire (pour fichiers privés)
     * @param string $storagePath Chemin dans le bucket
     * @param int $expiresIn Durée de validité en secondes (défaut: 1 heure)
     * @return string|null URL signée ou null en cas d'erreur
     */
    public function getSignedUrl($storagePath, $expiresIn = 3600) {
        $url = "{$this->projectUrl}/storage/v1/object/sign/{$this->bucket}/{$storagePath}";
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(['expiresIn' => $expiresIn]),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$this->apiKey}",
                "Content-Type: application/json"
            ]
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            $data = json_decode($response, true);
            return $data['signedURL'] ?? null;
        }
        
        return null;
    }
    
    /**
     * Liste les fichiers d'un dossier
     * @param string $prefix Préfixe/dossier à lister
     * @return array Liste des fichiers
     */
    public function list($prefix = '') {
        $url = "{$this->projectUrl}/storage/v1/object/list/{$this->bucket}";
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(['prefix' => $prefix]),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$this->apiKey}",
                "Content-Type: application/json"
            ]
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true) ?: [];
        }
        
        return [];
    }
}

/**
 * Calcule un score ATS basique pour un CV
 * @param string $filePath Chemin du fichier CV
 * @return array Score et détails
 */
function calculateATSScore($filePath) {
    $score = 0;
    $details = [];
    
    // Extension du fichier
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    
    // PDF est le format préféré
    if ($ext === 'pdf') {
        $score += 20;
        $details[] = ['label' => 'Format PDF', 'points' => 20, 'status' => 'good'];
    } elseif ($ext === 'docx') {
        $score += 15;
        $details[] = ['label' => 'Format DOCX', 'points' => 15, 'status' => 'medium'];
    } else {
        $score += 5;
        $details[] = ['label' => 'Format DOC', 'points' => 5, 'status' => 'low'];
    }
    
    // Taille du fichier (entre 50Ko et 2Mo = idéal)
    if (file_exists($filePath)) {
        $size = filesize($filePath);
        if ($size >= 50000 && $size <= 2000000) {
            $score += 15;
            $details[] = ['label' => 'Taille optimale', 'points' => 15, 'status' => 'good'];
        } elseif ($size < 50000) {
            $score += 5;
            $details[] = ['label' => 'Fichier trop léger', 'points' => 5, 'status' => 'low'];
        } else {
            $score += 10;
            $details[] = ['label' => 'Fichier volumineux', 'points' => 10, 'status' => 'medium'];
        }
    }
    
    // Nom de fichier propre
    $filename = basename($filePath);
    if (preg_match('/^[a-zA-Z0-9_\-]+\.(pdf|docx?|txt)$/i', $filename)) {
        $score += 10;
        $details[] = ['label' => 'Nom de fichier propre', 'points' => 10, 'status' => 'good'];
    } else {
        $score += 5;
        $details[] = ['label' => 'Nom à améliorer', 'points' => 5, 'status' => 'medium'];
    }
    
    // Score de base pour contenu (simulé - à remplacer par vraie analyse)
    $contentScore = rand(35, 55);
    $score += $contentScore;
    $details[] = ['label' => 'Analyse du contenu', 'points' => $contentScore, 'status' => $contentScore >= 45 ? 'good' : 'medium'];
    
    return [
        'score' => min(100, $score),
        'details' => $details,
        'level' => $score >= 80 ? 'excellent' : ($score >= 60 ? 'good' : ($score >= 40 ? 'medium' : 'low'))
    ];
}
