<?php
require_once __DIR__ . '/../config/Database.php';

class MatchModel {
    public static function getUpcomingMatches() {
        $db = Database::getConnection();
        if (!$db) return [];

        $query = "SELECT 
                    p.id_partido,
                    c.Nombre AS league,
                    e1.Nombre AS home_team,
                    e2.Nombre AS away_team,
                    p.fecha_hora,
                    p.estadio,
                    CONCAT('PhpTip-off/img/', LOWER(e1.Nombre), '.png') AS home_badge,
                    CONCAT('PhpTip-off/img/', LOWER(e2.Nombre), '.png') AS away_badge,
                    50 AS home_prob,
                    50 AS away_prob
                  FROM partidos p
                  JOIN competencias c ON p.id_competencia = c.id_competencia
                  JOIN equipos e1 ON p.id_local = e1.id_equipo
                  JOIN equipos e2 ON p.id_visitante = e2.id_equipo
                  ORDER BY p.fecha_hora ASC";

        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>