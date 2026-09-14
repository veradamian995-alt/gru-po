<?php
require_once '../config/Database.php';

$action = $_GET['action'] ?? '';

if ($action == 'create') {
    $id_competencia = $_POST['id_competencia'];
    $temporada = $_POST['temporada'];
    $id_local = $_POST['id_local'];
    $id_visitante = $_POST['id_visitante'];
    $fecha_hora = $_POST['fecha_hora'];
    $estadio = $_POST['estadio'];

    $db = Database::getConnection();
    $stmt = $db->prepare("INSERT INTO partidos (id_competencia, temporada, id_local, id_visitante, fecha_hora, estadio, estado) VALUES (?, ?, ?, ?, ?, ?, 'Programado')");
    $stmt->execute([$id_competencia, $temporada, $id_local, $id_visitante, $fecha_hora, $estadio]);

    header("Location: ../../../index.php");
    exit();
}

if ($action == 'update_score') {
    $id_partido = $_POST['id_partido'];
    $goles_local = $_POST['goles_local'];
    $goles_visitante = $_POST['goles_visitante'];
    $estado = $_POST['estado'];

    $db = Database::getConnection();
    $stmt = $db->prepare("UPDATE partidos SET goles_local = ?, goles_visitante = ?, estado = ? WHERE id_partido = ?");
    $stmt->execute([$goles_local, $goles_visitante, $estado, $id_partido]);

    header("Location: ../../../index.php");
    exit();
}

if ($action == 'delete') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM partidos WHERE id_partido = ?");
        $stmt->execute([$id]);
    }
    header("Location: ../../../index.php");
    exit();
}