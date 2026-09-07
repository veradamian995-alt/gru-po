<?php
require_once __DIR__ . '/../config/Database.php';

$action = $_GET['action'] ?? '';

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getConnection();

    $id_competencia = $_POST['id_competencia'];
    $temporada      = $_POST['temporada'];
    $id_local       = $_POST['id_local'];
    $id_visitante   = $_POST['id_visitante'];
    $fecha_hora     = $_POST['fecha_hora'];
    $estadio        = $_POST['estadio'];
    $estado         = 'Programado';

    $sql = "INSERT INTO partidos (id_competencia, temporada, id_local, id_visitante, fecha_hora, estado, estadio) 
            VALUES (:id_comp, :temp, :local, :vis, :fecha, :estado, :estadio)";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':id_comp' => $id_competencia,
        ':temp'    => $temporada,
        ':local'   => $id_local,
        ':vis'     => $id_visitante,
        ':fecha'   => $fecha_hora,
        ':estado'  => $estado,
        ':estadio' => $estadio
    ]);

    header('Location: ../../../index.php');
    exit;
}

if ($action === 'update_score' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getConnection();

    $id_partido      = $_POST['id_partido'];
    $goles_local     = $_POST['goles_local'];
    $goles_visitante = $_POST['goles_visitante'];
    $estado          = $_POST['estado'];

    $sql = "UPDATE partidos 
            SET goles_local = :goles_l, goles_visitante = :goles_v, estado = :estado 
            WHERE id_partido = :id";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':goles_l' => $goles_local,
        ':goles_v' => $goles_visitante,
        ':estado'  => $estado,
        ':id'      => $id_partido
    ]);

    header('Location: ../../../index.php');
    exit;
}
?>