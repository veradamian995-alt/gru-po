<?php
require_once '../config/Database.php';

$action = $_GET['action'] ?? '';

if ($action == 'predict') {
    $id_partido = $_POST['id_partido'];
    $usuario_nombre = $_POST['usuario_nombre'];
    $prediccion_local = $_POST['prediccion_local'];
    $prediccion_visitante = $_POST['prediccion_visitante'];

    $db = Database::getConnection();
    $stmt = $db->prepare("INSERT INTO predicciones (id_partido, usuario_nombre, prediccion_local, prediccion_visitante) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id_partido, $usuario_nombre, $prediccion_local, $prediccion_visitante]);

    header("Location: ../../../index.php");
    exit();
}

if ($action == 'delete') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM predicciones WHERE id_prediccion = ?");
        $stmt->execute([$id]);
    }
    header("Location: ../../../index.php");
    exit();
}