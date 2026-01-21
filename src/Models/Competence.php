<?php
namespace App\Models;

use PDO;
use Exception;

class Competence {
    private $db;

    public function __construct() {
        $this->db = \Database::getInstance()->getConnection();
    }

    /**
     * Récupère la liste complète des compétences
     */
    public function getAll() {
        try {
            $stmt = $this->db->query("SELECT * FROM public.competences ORDER BY nom ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur SQL getAll: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Crée une compétence avec gestion d'erreur professionnelle
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO public.competences (nom, description, type_competence) 
                    VALUES (:nom, :description, :type)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nom'         => strip_tags($data['nom']),
                ':description' => strip_tags($data['description'] ?? ''),
                ':type'        => $data['type'] ?? 'technique'
            ]);
        } catch (Exception $e) {
            error_log("Erreur insertion Supabase: " . $e->getMessage());
            return false;
        }
    }
}
