<?php
require_once __DIR__ . '/../config/Database.php';

$action = $_GET['action'] ?? '';

if ($action === 'predict' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getConnection();

    $id_partido           = $_POST['id_partido'];
    $usuario_nombre       = $_POST['usuario_nombre'];
    $prediccion_local     = $_POST['prediccion_local'];
    $prediccion_visitante = $_POST['prediccion_visitante'];

    $sql = "INSERT INTO predicciones (id_partido, usuario_nombre, prediccion_local, prediccion_visitante) 
            VALUES (:id_partido, :usuario, :p_local, :p_vis)";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':id_partido' => $id_partido,
        ':usuario'    => $usuario_nombre,
        ':p_local'    => $prediccion_local,
        ':p_vis'      => $prediccion_visitante
    ]);

    header('Location: ../../../index.php');
    exit;
}
?>