<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'PhpTip-off/php/models/MatchModel.php';
require_once 'PhpTip-off/php/config/Database.php'; 

$db = Database::getConnection();

// CONSULTA DIRECTA DE PARTIDOS DESDE LA BASE DE DATOS
$partidos_registrados = $db->query("SELECT p.id_partido, p.id_competencia, p.temporada, p.id_local, p.id_visitante, p.fecha_hora, p.estadio, p.goles_local, p.goles_visitante, 
                                           e1.Nombre as local_nombre, e2.Nombre as visitante_nombre
                                    FROM partidos p 
                                    LEFT JOIN equipos e1 ON p.id_local = e1.id_equipo 
                                    LEFT JOIN equipos e2 ON p.id_visitante = e2.id_equipo 
                                    ORDER BY p.id_partido DESC")->fetchAll(PDO::FETCH_ASSOC);

$predicciones_registradas = $db->query("SELECT id_prediccion, id_partido, usuario_nombre, prediccion_local, prediccion_visitante, 
                                               NOW() as fecha_registro 
                                        FROM predicciones 
                                        ORDER BY id_prediccion DESC")->fetchAll(PDO::FETCH_ASSOC);

// DATOS PREDETERMINADOS DE LOS PARTIDOS PARA AUTOCOMPLETAR
$datos_partidos_js = [
    "1" => ["temporada" => "2026", "id_local" => 1, "id_visitante" => 2, "fecha" => "2026-03-20T18:00", "estadio" => "Florencio Sola"],
    "2" => ["temporada" => "2026", "id_local" => 3, "id_visitante" => 4, "fecha" => "2026-03-21T20:00", "estadio" => "Néstor Díaz Pérez"],
    "3" => ["temporada" => "2026", "id_local" => 5, "id_visitante" => 1, "fecha" => "2026-03-22T19:00", "estadio" => "El Cilindro de Avellaneda"],
    "4" => ["temporada" => "2026", "id_local" => 6, "id_visitante" => 7, "fecha" => "2026-03-23T21:00", "estadio" => "15 de Abril"]
];

// FUNCIÓN PARA OBTENER EL ESCUDO DESDE LA CARPETA LOCAL PhpTip-off/img/
function obtenerEscudoEquipo($nombreEquipo) {
    if (!$nombreEquipo) {
        return 'PhpTip-off/img/logo.png';
    }

    $nombre = mb_strtolower(trim($nombreEquipo), 'UTF-8');

    $escudos = [
        'banfield'        => 'banfield.png',
        'belgrano'        => 'belgrano.png',
        'racing'          => 'racing.png',
        'lanus'           => 'lanus.png',
        'lanús'           => 'lanus.png',
        'talleres'        => 'talleres.png',
        'union'           => 'union.png',
        'unión'           => 'union.png',
        'central'         => 'central_cordoba.png',
        'cordoba'         => 'central_cordoba.png',
        'córdoba'         => 'central_cordoba.png',
        'river'           => 'river.png',
        'boca'            => 'boca.png',
        'independiente'   => 'independiente.png',
        'san lorenzo'     => 'san_lorenzo.png'
    ];

    foreach ($escudos as $clave => $archivo) {
        if (strpos($nombre, $clave) !== false) {
            $rutaImagen = 'PhpTip-off/img/' . $archivo;
            if (file_exists($rutaImagen)) {
                return $rutaImagen;
            }
        }
    }

    return 'PhpTip-off/img/logo.png';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TIP-OFF - Predicciones</title>
    <link rel="stylesheet" href="PhpTip-off/css/index.css">
    <style>
        .crud-table { width: 100%; border-collapse: collapse; margin-top: 15px; color: #fff; background: #1c262f; border-radius: 8px; overflow: hidden; }
        .crud-table th, .crud-table td { padding: 12px; text-align: left; border-bottom: 1px solid #2c3a47; }
        .crud-table th { background-color: #0f171e; color: #00ff87; }
        .btn-action { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; margin-right: 5px; }
        .btn-edit { background-color: #ffb703; color: #000; }
        .btn-delete { background-color: #e63946; color: #fff; }
        .crud-container { margin-bottom: 40px; }

        /* ESTILOS EXACTOS DE LAS TARJETAS Y FORMULARIOS */
        .forms-container { display: flex; gap: 20px; justify-content: center; }
        .form-card {
            background-color: #17212b;
            border: 1px solid #24313d;
            border-radius: 8px;
            padding: 20px;
            box-sizing: border-box;
        }
        .form-card h3 {
            color: #00ff87;
            font-size: 0.95rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .form-card label {
            display: block;
            color: #a0acba;
            font-size: 0.85rem;
            margin-bottom: 5px;
        }
        .form-card select, .form-card input {
            width: 100%;
            padding: 10px;
            background: #0f171e;
            border: 1px solid #2c3a47;
            color: #fff;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 0.9rem;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .btn-submit-green {
            width: 100%;
            padding: 12px;
            background-color: #00ff87;
            color: #000;
            border: none;
            border-radius: 4px;
            font-weight: 800;
            font-size: 0.9rem;
            text-transform: uppercase;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.2s;
        }
        .btn-submit-green:hover {
            background-color: #00e077;
        }

        /* Ajustes de escudos y equipos */
        .match-card .teams-names {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
            font-weight: bold;
            font-size: 0.9em;
            margin: 10px 0;
            text-align: center;
        }
        .match-card .team-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 45%;
        }
        .match-card .team-logo-custom {
            width: 45px;
            height: 45px;
            max-width: 45px;
            max-height: 45px;
            object-fit: contain;
            margin-bottom: 6px;
            display: block;
        }
        .match-card .vs-label {
            color: #00ff87;
            font-size: 0.9em;
            font-weight: bold;
        }

        /* ==========================================
           ANIMACIÓN DE BLOQUE (CORREGIDA SIN SALTO DE SCROLL)
           ========================================== */

        /* Previene que las animaciones afecten la barra de scroll de la página */
        body {
            overflow-x: hidden;
        }

        /* Movimiento de entrada nítido y acotado para evitar alterar el alto del documento */
        @keyframes blockSlideUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Aplicación de la animación en capas aisladas de renderizado */
        .header {
            will-change: transform, opacity;
            animation: blockSlideUp 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .hero {
            will-change: transform, opacity;
            animation: blockSlideUp 1.1s cubic-bezier(0.16, 1, 0.3, 1) 0.15s both;
        }

        .section {
            will-change: transform, opacity;
            animation: blockSlideUp 1.1s cubic-bezier(0.16, 1, 0.3, 1) 0.25s both;
        }

        /* Tarjetas y formularios */
        .match-card,
        .form-card {
            will-change: transform, opacity;
            animation: blockSlideUp 1s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Entrada escalonada progresiva */
        .matches-grid .match-card:nth-child(1) { animation-delay: 0.20s; }
        .matches-grid .match-card:nth-child(2) { animation-delay: 0.30s; }
        .matches-grid .match-card:nth-child(3) { animation-delay: 0.40s; }
        .matches-grid .match-card:nth-child(4) { animation-delay: 0.50s; }

        .forms-container .form-card:nth-child(1) { animation-delay: 0.30s; }
        .forms-container .form-card:nth-child(2) { animation-delay: 0.42s; }
        .forms-container .form-card:nth-child(3) { animation-delay: 0.54s; }

        /* Micro-interacción al pasar el cursor */
        .match-card, .form-card {
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease;
        }

        .match-card:hover, .form-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
        }

        /* ACCESIBILIDAD: Respeto a las preferencias del usuario */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <header class="header">
        <div class="logo">
            <img src="PhpTip-off/img/logo.png" alt="Tip-Off Logo">
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Buscar...">
        </div>
    </header>

    <main class="container">
        <!-- HERO BANNER -->
        <section class="hero" style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('PhpTip-off/img/stadium.jpg');">
            <div class="hero-content">
                <h1>PREDICCIONES DE FÚTBOL ARGENTINO EN VIVO</h1>
            </div>
        </section>

        <!-- PRÓXIMOS PARTIDOS DE FÚTBOL -->
        <section class="section">
            <h2 class="section-title">PRÓXIMOS PARTIDOS DE FÚTBOL</h2>
            <div class="matches-grid">
                <?php if (!empty($partidos_registrados)): ?>
                    <?php foreach ($partidos_registrados as $match): 
                        $idP = $match['id_partido'];
                        $logoLocal = obtenerEscudoEquipo($match['local_nombre'] ?? '');
                        $logoVisitante = obtenerEscudoEquipo($match['visitante_nombre'] ?? '');
                    ?>
                        <div class="match-card" id="card-match-<?= $idP ?>">
                            <span class="league-name">Liga Profesional</span>
                            
                            <div class="teams-names">
                                <div class="team-item">
                                    <img src="<?= $logoLocal ?>" alt="<?= htmlspecialchars($match['local_nombre'] ?? 'Local') ?>" class="team-logo-custom">
                                    <span><?= htmlspecialchars($match['local_nombre'] ?? 'Local') ?></span>
                                </div>
                                <span class="vs-label">VS</span>
                                <div class="team-item">
                                    <img src="<?= $logoVisitante ?>" alt="<?= htmlspecialchars($match['visitante_nombre'] ?? 'Visitante') ?>" class="team-logo-custom">
                                    <span><?= htmlspecialchars($match['visitante_nombre'] ?? 'Visitante') ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #fff; text-align: center;">No hay partidos cargados aún.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- PREDICCIÓN DE TORNEO PROXIMAMENTE -->
        <section class="section text-center">
            <h2 class="section-title">PREDICCIÓN DE TORNEO PROXIMAMENTE</h2>
            <div class="tournament-banner">
                <img src="PhpTip-off/img/libertadores.jpg" alt="CONMEBOL Libertadores">
            </div>
        </section>

        <hr style="border: 0; border-top: 1px solid #1a2a3a; margin: 30px 0;">

        <!-- SECCIÓN DE FORMULARIOS -->
        <section class="section forms-section">
            <h2 class="section-title">ADMINISTRACIÓN Y PREDICCIONES</h2>
            
            <div class="forms-container">
                
                <!-- FORMULARIO 1: CREAR PARTIDO -->
                <div class="form-card" style="flex: 1;">
                    <h3>CARGAR NUEVO PARTIDO (`PARTIDOS`)</h3>
                    <form action="PhpTip-off/php/controllers/MatchController.php?action=create" method="POST">
                        
                        <div class="form-group">
                            <label>ID Competencia:</label>
                            <select name="id_competencia" id="select_competencia" onchange="autocompletarPartido(this.value)" required>
                                <option value="">-- Seleccionar Competencia / Partido --</option>
                                <option value="1">1 - Liga Profesional (Banfield vs Belgrano)</option>
                                <option value="2">2 - Liga Profesional (Lanús vs Talleres)</option>
                                <option value="3">3 - Liga Profesional (Racing vs Banfield)</option>
                                <option value="4">4 - Liga Profesional (Unión vs C. Córdoba)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Temporada:</label>
                            <input type="text" name="temporada" id="input_temporada" required placeholder="Ej: 2026">
                        </div>
                        <div class="form-group">
                            <label>ID Equipo Local:</label>
                            <input type="number" name="id_local" id="input_local" required placeholder="Ej: 1">
                        </div>
                        <div class="form-group">
                            <label>ID Equipo Visitante:</label>
                            <input type="number" name="id_visitante" id="input_visitante" required placeholder="Ej: 2">
                        </div>
                        <div class="form-group">
                            <label>Fecha y Hora:</label>
                            <input type="datetime-local" name="fecha_hora" id="input_fecha" required>
                        </div>
                        <div class="form-group">
                            <label>Estadio:</label>
                            <input type="text" name="estadio" id="input_estadio" required placeholder="Ej: Florencio Sola">
                        </div>

                        <button type="submit" class="btn-submit-green">GUARDAR PARTIDO</button>
                    </form>
                </div>

                <!-- FORMULARIO 2: CREAR PREDICCIÓN DE USUARIO -->
                <div class="form-card" style="flex: 1;">
                    <h3>REGISTRAR PREDICCIÓN (`PREDICCIONES`)</h3>
                    <form action="PhpTip-off/php/controllers/PredictionController.php?action=predict" method="POST">
                        <div class="form-group">
                            <label>ID Partido:</label>
                            <input type="number" name="id_partido" id="input_pred_id_partido" required placeholder="Ej: 1">
                        </div>
                        <div class="form-group">
                            <label>Nombre de Usuario:</label>
                            <input type="text" name="usuario_nombre" required placeholder="Ej: Benjamin">
                        </div>
                        <div class="form-group">
                            <label>Goles Local Estimados:</label>
                            <input type="number" name="prediccion_local" required placeholder="0">
                        </div>
                        <div class="form-group">
                            <label>Goles Visitante Estimados:</label>
                            <input type="number" name="prediccion_visitante" required placeholder="0">
                        </div>
                        <button type="submit" class="btn-submit-green">ENVIAR PREDICCIÓN</button>
                    </form>
                </div>

            </div>
        </section>

        <!-- SECCIÓN DE REGISTROS GUARDADOS Y GESTIÓN (CRUD) -->
        <section class="section">
            <h2 class="section-title">REGISTROS GUARDADOS Y ADMINISTRACIÓN</h2>

            <!-- TABLA DE PARTIDOS -->
            <div class="crud-container">
                <h3>Partidos Cargados</h3>
                <table class="crud-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Local</th>
                            <th>Visitante</th>
                            <th>Fecha</th>
                            <th>Estadio</th>
                            <th>Resultado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($partidos_registrados)): ?>
                            <?php foreach ($partidos_registrados as $p): ?>
                                <tr>
                                    <td><?= $p['id_partido'] ?></td>
                                    <td><?= htmlspecialchars($p['local_nombre'] ?? $p['id_local']) ?></td>
                                    <td><?= htmlspecialchars($p['visitante_nombre'] ?? $p['id_visitante']) ?></td>
                                    <td><?= $p['fecha_hora'] ?></td>
                                    <td><?= htmlspecialchars($p['estadio']) ?></td>
                                    <td>
                                        <?= ($p['goles_local'] !== null && $p['goles_visitante'] !== null) 
                                            ? $p['goles_local'] . ' - ' . $p['goles_visitante'] 
                                            : 'Sin Resultado' ?>
                                    </td>
                                    <td>
                                        <button class="btn-action btn-edit" onclick="editarPartido(<?= htmlspecialchars(json_encode($p)) ?>)">Editar</button>
                                        <a href="PhpTip-off/php/controllers/MatchController.php?action=delete&id=<?= $p['id_partido'] ?>" class="btn-action btn-delete" onclick="return confirm('¿Seguro que deseas eliminar este partido?')">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align: center;">No hay partidos cargados aún.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- TABLA DE PREDICCIONES -->
            <div class="crud-container">
                <h3>Predicciones de Usuarios Registradas</h3>
                <table class="crud-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ID Partido</th>
                            <th>Usuario</th>
                            <th>Predicción</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($predicciones_registradas)): ?>
                            <?php foreach ($predicciones_registradas as $pred): ?>
                                <tr>
                                    <td><?= $pred['id_prediccion'] ?></td>
                                    <td><?= $pred['id_partido'] ?></td>
                                    <td><?= htmlspecialchars($pred['usuario_nombre']) ?></td>
                                    <td><?= $pred['prediccion_local'] ?> - <?= $pred['prediccion_visitante'] ?></td>
                                    <td><?= htmlspecialchars($pred['fecha_registro']) ?></td>
                                    <td>
                                        <button class="btn-action btn-edit" onclick="editarPrediccion(<?= htmlspecialchars(json_encode($pred)) ?>)">Editar</button>
                                        <a href="PhpTip-off/php/controllers/PredictionController.php?action=delete&id=<?= $pred['id_prediccion'] ?>" class="btn-action btn-delete" onclick="return confirm('¿Seguro que deseas eliminar esta predicción?')">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align: center;">No hay predicciones registradas aún.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <h3>Tip-off oficial</h3>
    </footer>

    <script>
        // Mapeo de datos para autocompletar automáticamente
        const datosPartidos = <?= json_encode($datos_partidos_js) ?>;

        function autocompletarPartido(id) {
            if (datosPartidos[id]) {
                const info = datosPartidos[id];
                document.getElementById('input_temporada').value = info.temporada;
                document.getElementById('input_local').value = info.id_local;
                document.getElementById('input_visitante').value = info.id_visitante;
                document.getElementById('input_fecha').value = info.fecha;
                document.getElementById('input_estadio').value = info.estadio;
                document.getElementById('input_pred_id_partido').value = id;
            }
        }

        function editarPartido(partido) {
            window.scrollTo({top: document.querySelector('.forms-section').offsetTop, behavior: 'smooth'});
            document.getElementById('select_competencia').value = partido.id_competencia || "";
            document.getElementById('input_temporada').value = partido.temporada;
            document.getElementById('input_local').value = partido.id_local;
            document.getElementById('input_visitante').value = partido.id_visitante;
            document.getElementById('input_fecha').value = partido.fecha_hora ? partido.fecha_hora.replace(" ", "T") : "";
            document.getElementById('input_estadio').value = partido.estadio;
        }

        function editarPrediccion(prediccion) {
            window.scrollTo({top: document.querySelector('.forms-section').offsetTop, behavior: 'smooth'});
            let form = document.querySelector("form[action*='PredictionController.php']");
            form.querySelector("input[name='id_partido']").value = prediccion.id_partido;
            form.querySelector("input[name='usuario_nombre']").value = prediccion.usuario_nombre;
            form.querySelector("input[name='prediccion_local']").value = prediccion.prediccion_local;
            form.querySelector("input[name='prediccion_visitante']").value = prediccion.prediccion_visitante;
        }
    </script>
</body>
</html>