<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'PhpTip-off/php/models/MatchModel.php';
require_once 'PhpTip-off/php/config/Database.php'; 

$db = Database::getConnection();

// CONSULTA DIRECTA DE PARTIDOS DESDE LA BASE DE DATOS
$partidos_registrados = $db->query("SELECT p.id_partido, p.id_competencia, p.temporada, p.fecha_hora, p.estadio, p.goles_local, p.goles_visitante, 
                                           e1.Nombre as local_nombre, e2.Nombre as visitante_nombre
                                    FROM partidos p 
                                    LEFT JOIN equipos e1 ON p.id_local = e1.id_equipo 
                                    LEFT JOIN equipos e2 ON p.id_visitante = e2.id_equipo 
                                    ORDER BY p.id_partido DESC")->fetchAll(PDO::FETCH_ASSOC);

$predicciones_registradas = $db->query("SELECT id_prediccion, id_partido, usuario_nombre, prediccion_local, prediccion_visitante, 
                                               NOW() as fecha_registro 
                                        FROM predicciones 
                                        ORDER BY id_prediccion DESC")->fetchAll(PDO::FETCH_ASSOC);

// FUNCIÓN PARA OBTENER EL ESCUDO SEGÚN EL NOMBRE DEL EQUIPO
function obtenerEscudoEquipo($nombreEquipo) {
    if (!$nombreEquipo) return 'https://upload.wikimedia.org/wikipedia/commons/d/d3/Soccerball_mark.svg';

    $nombreNormalizado = mb_strtolower(trim($nombreEquipo), 'UTF-8');

    $escudos = [
        'banfield' => 'https://upload.wikimedia.org/wikipedia/commons/1/1a/Escudo_Club_Atl%C3%A9tico_Banfield.svg',
        'belgrano' => 'https://upload.wikimedia.org/wikipedia/commons/e/ea/Escudo_Belgrano_CDB.svg',
        'racing' => 'https://upload.wikimedia.org/wikipedia/commons/2/27/Escudo_de_Racing_Club_%282014%29.svg',
        'racing club' => 'https://upload.wikimedia.org/wikipedia/commons/2/27/Escudo_de_Racing_Club_%282014%29.svg',
        'lanus' => 'https://upload.wikimedia.org/wikipedia/commons/e/eb/Escudo_del_Club_Atl%C3%A9tico_Lan%C3%BAs.svg',
        'lanús' => 'https://upload.wikimedia.org/wikipedia/commons/e/eb/Escudo_del_Club_Atl%C3%A9tico_Lan%C3%BAs.svg',
        'talleres' => 'https://upload.wikimedia.org/wikipedia/commons/0/07/Escudo_Talleres_2015.svg',
        'union' => 'https://upload.wikimedia.org/wikipedia/commons/8/87/Escudo_de_Uni%C3%B3n_de_Santa_Fe.svg',
        'unión' => 'https://upload.wikimedia.org/wikipedia/commons/8/87/Escudo_de_Uni%C3%B3n_de_Santa_Fe.svg',
        'central cordoba' => 'https://upload.wikimedia.org/wikipedia/commons/2/24/Escudo_Central_C%C3%B3rdoba_SDE.png',
        'central córdoba' => 'https://upload.wikimedia.org/wikipedia/commons/2/24/Escudo_Central_C%C3%B3rdoba_SDE.png',
        'c. cordoba' => 'https://upload.wikimedia.org/wikipedia/commons/2/24/Escudo_Central_C%C3%B3rdoba_SDE.png',
        'river' => 'https://upload.wikimedia.org/wikipedia/commons/a/ac/Escudo_del_C_A_River_Plate.svg',
        'boca' => 'https://upload.wikimedia.org/wikipedia/commons/a/ae/Escudo_de_Boca_Juniors_2022.svg',
        'independiente' => 'https://upload.wikimedia.org/wikipedia/commons/d/db/Escudo_de_Independiente.svg',
        'san lorenzo' => 'https://upload.wikimedia.org/wikipedia/commons/7/77/Escudo_del_Club_Atl%C3%A9tico_San_Lorenzo_de_Almagro.svg'
    ];

    foreach ($escudos as $clave => $url) {
        if (strpos($nombreNormalizado, $clave) !== false) {
            return $url;
        }
    }

    return 'https://upload.wikimedia.org/wikipedia/commons/d/d3/Soccerball_mark.svg';
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
        .form-card select, .form-card input { width: 100%; padding: 8px; background: #0f171e; border: 1px solid #2c3a47; color: #fff; border-radius: 4px; margin-top: 4px; }
        .prob-inputs { display: flex; gap: 10px; }
        
        /* Ajustes para escudos y nombres */
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
            object-fit: contain;
            margin-bottom: 6px;
        }
        .match-card .vs-label {
            color: #00ff87;
            font-size: 0.9em;
            font-weight: bold;
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
                        $logoLocal = obtenerEscudoEquipo($match['local_nombre'] ?? '');
                        $logoVisitante = obtenerEscudoEquipo($match['visitante_nombre'] ?? '');
                    ?>
                        <div class="match-card" id="card-match-<?= $match['id_partido'] ?>">
                            <span class="league-name">Liga Profesional</span>
                            
                            <!-- MOSTRAR EQUIPOS CON SU ESCUDO Y NOMBRE CORRESPONDIENTE -->
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

                            <!-- BARRA DE PROBABILIDAD -->
                            <div class="prob-bar">
                                <div class="prob-fill" id="fill-card-<?= $match['id_partido'] ?>" style="width: <?= $match['home_prob'] ?? 50 ?>%;"></div>
                            </div>
                            <div class="prob-labels">
                                <span class="lbl-home" id="lbl-home-<?= $match['id_partido'] ?>"><?= $match['home_prob'] ?? 50 ?>%</span>
                                <span class="lbl-away" id="lbl-away-<?= $match['id_partido'] ?>"><?= $match['away_prob'] ?? 50 ?>%</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #fff; text-align: center;">No hay partidos cargados aún.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- PREDICCIÓN DE TORNEO -->
        <section class="section text-center">
            <h2 class="section-title">Predicción de Torneo</h2>
            <div class="tournament-banner">
                <img src="PhpTip-off/img/libertadores.jpg" alt="CONMEBOL Libertadores">
            </div>
        </section>

        <!-- SECCIÓN DE FORMULARIOS -->
        <section class="section forms-section">
            <h2 class="section-title">ADMINISTRACIÓN Y PREDICCIONES</h2>
            
            <div class="forms-container" style="display: flex; gap: 20px; justify-content: center;">
                
                <!-- FORMULARIO 1: CREAR/EDITAR PARTIDO -->
                <div class="form-card" style="flex: 1; max-width: 500px;">
                    <h3>Cargar Nuevo Partido (`partidos`)</h3>
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

                        <div class="form-group">
                            <label>Porcentajes de Probabilidad (%):</label>
                            <div class="prob-inputs">
                                <input type="number" name="home_prob" id="input_prob_home" placeholder="Local %" min="0" max="100" value="50" oninput="actualizarPorcentajesBarra()">
                                <input type="number" name="away_prob" id="input_prob_away" placeholder="Visitante %" min="0" max="100" value="50" oninput="actualizarPorcentajesBarra()">
                            </div>
                        </div>

                        <button type="submit" class="btn-submit" style="margin-top: 15px;">Guardar Partido</button>
                    </form>
                </div>

                <!-- FORMULARIO 2: CREAR PREDICCIÓN DE USUARIO -->
                <div class="form-card" style="flex: 1; max-width: 500px;">
                    <h3>Registrar Predicción (`predicciones`)</h3>
                    <form action="PhpTip-off/php/controllers/PredictionController.php?action=predict" method="POST">
                        <div class="form-group">
                            <label>ID Partido:</label>
                            <input type="number" name="id_partido" required placeholder="Ej: 1">
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
                        <button type="submit" class="btn-submit" style="margin-top: 15px;">Enviar Predicción</button>
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

    <!-- SCRIPTS DE JS -->
    <script>
        const datosPredefinidos = {
            "1": { temporada: "2026", id_local: 1, id_visitante: 2, fecha: "2026-05-10T18:00", estadio: "Florencio Sola", prob_home: 50, prob_away: 50 },
            "2": { temporada: "2026", id_local: 3, id_visitante: 4, fecha: "2026-05-11T20:00", estadio: "La Fortaleza", prob_home: 50, prob_away: 50 },
            "3": { temporada: "2026", id_local: 5, id_visitante: 1, fecha: "2026-05-12T19:30", estadio: "El Cilindro", prob_home: 50, prob_away: 50 },
            "4": { temporada: "2026", id_local: 6, id_visitante: 7, fecha: "2026-05-13T21:15", estadio: "15 de Abril", prob_home: 50, prob_away: 50 }
        };

        function autocompletarPartido(id) {
            if (datosPredefinidos[id]) {
                const info = datosPredefinidos[id];
                document.getElementById('input_temporada').value = info.temporada;
                document.getElementById('input_local').value = info.id_local;
                document.getElementById('input_visitante').value = info.id_visitante;
                document.getElementById('input_fecha').value = info.fecha;
                document.getElementById('input_estadio').value = info.estadio;
                document.getElementById('input_prob_home').value = info.prob_home;
                document.getElementById('input_prob_away').value = info.prob_away;
                
                let inputIdPartido = document.querySelector("form[action*='PredictionController.php'] input[name='id_partido']");
                if (inputIdPartido) {
                    inputIdPartido.value = id;
                }

                actualizarPorcentajesBarra();
            }
        }

        function actualizarPorcentajesBarra() {
            let home = parseInt(document.getElementById('input_prob_home').value);
            if (isNaN(home)) home = 0;
            if (home > 100) home = 100;
            if (home < 0) home = 0;

            let away = 100 - home;
            document.getElementById('input_prob_away').value = away;

            let matchId = document.getElementById('select_competencia').value;
            if (!matchId) return;

            let fillBar = document.getElementById('fill-card-' + matchId);
            let lblHome = document.getElementById('lbl-home-' + matchId);
            let lblAway = document.getElementById('lbl-away-' + matchId);

            if (fillBar) fillBar.style.width = home + '%';
            if (lblHome) lblHome.textContent = home + '%';
            if (lblAway) lblAway.textContent = away + '%';
        }

        function editarPartido(partido) {
            window.scrollTo({top: document.querySelector('.forms-section').offsetTop, behavior: 'smooth'});
            document.getElementById('select_competencia').value = partido.id_competencia || "1";
            document.getElementById('input_temporada').value = partido.temporada;
            document.getElementById('input_local').value = partido.id_local;
            document.getElementById('input_visitante').value = partido.id_visitante;
            document.getElementById('input_fecha').value = partido.fecha_hora ? partido.fecha_hora.replace(" ", "T") : "";
            document.getElementById('input_estadio').value = partido.estadio;
            
            actualizarPorcentajesBarra();
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