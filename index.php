<?php
require_once 'PhpTip-off/php/models/MatchModel.php';
$matches = MatchModel::getUpcomingMatches();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TIP-OFF - Predicciones</title>
    <link rel="stylesheet" href="PhpTip-off/css/index.css">
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
                <h1>PREDICCIONES DE FUTBOL ARGENTINO EN VIVO</h1>
            </div>
        </section>

        <!-- PRÓXIMOS PARTIDOS -->
        <section class="section">
            <h2 class="section-title">PROXIMOS PARTIDOS DE FUTBOL</h2>
            <div class="matches-grid">
                <?php foreach ($matches as $match): ?>
                    <div class="match-card">
                        <span class="league-name"><?= htmlspecialchars($match['league']) ?></span>
                        <div class="teams">
                            <img src="<?= htmlspecialchars($match['home_badge']) ?>" alt="<?= htmlspecialchars($match['home_team']) ?>" class="team-logo">
                            <img src="<?= htmlspecialchars($match['away_badge']) ?>" alt="<?= htmlspecialchars($match['away_team']) ?>" class="team-logo">
                        </div>
                        <div class="prob-bar">
                            <div class="prob-fill" style="width: <?= $match['home_prob'] ?>%;"></div>
                        </div>
                        <div class="prob-labels">
                            <span><?= $match['home_prob'] ?>%</span>
                            <span><?= $match['away_prob'] ?>%</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- PREDICCIÓN DE TORNEO -->
        <section class="section text-center">
            <h2 class="section-title">Predicion de Torneo</h2>
            <div class="tournament-banner">
                <img src="PhpTip-off/img/libertadores.jpg" alt="CONMEBOL Libertadores">
            </div>
        </section>

        <!-- DETALLES EXTENDIDOS POR PARTIDO -->
        <section class="section">
            <div class="matches-list-extended">
                <?php foreach ($matches as $match): ?>
                    <div class="match-extended-row">
                        <div class="match-card">
                            <span class="league-name"><?= htmlspecialchars($match['league']) ?></span>
                            <div class="teams">
                                <img src="<?= htmlspecialchars($match['home_badge']) ?>" alt="<?= htmlspecialchars($match['home_team']) ?>" class="team-logo">
                                <img src="<?= htmlspecialchars($match['away_badge']) ?>" alt="<?= htmlspecialchars($match['away_team']) ?>" class="team-logo">
                            </div>
                            <div class="prob-bar">
                                <div class="prob-fill" style="width: <?= $match['home_prob'] ?>%;"></div>
                            </div>
                            <div class="prob-labels">
                                <span><?= $match['home_prob'] ?>%</span>
                                <span><?= $match['away_prob'] ?>%</span>
                            </div>
                        </div>

                        <div class="match-extended-panel">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- SECCIÓN DE FORMULARIOS AL FINAL -->
        <section class="section forms-section">
            <h2 class="section-title">ADMINISTRACIÓN Y PREDICCIONES</h2>
            
            <div class="forms-container">
                <!-- FORMULARIO 1: CREAR PARTIDO -->
                <div class="form-card">
                    <h3>Cargar Nuevo Partido (`partidos`)</h3>
                    <form action="PhpTip-off/php/controllers/MatchController.php?action=create" method="POST">
                        <div class="form-group">
                            <label>ID Competencia:</label>
                            <input type="number" name="id_competencia" required placeholder="Ej: 1 (Liga Profesional)">
                        </div>
                        <div class="form-group">
                            <label>Temporada:</label>
                            <input type="text" name="temporada" required placeholder="Ej: 2026">
                        </div>
                        <div class="form-group">
                            <label>ID Equipo Local:</label>
                            <input type="number" name="id_local" required placeholder="Ej: 1">
                        </div>
                        <div class="form-group">
                            <label>ID Equipo Visitante:</label>
                            <input type="number" name="id_visitante" required placeholder="Ej: 2">
                        </div>
                        <div class="form-group">
                            <label>Fecha y Hora:</label>
                            <input type="datetime-local" name="fecha_hora" required>
                        </div>
                        <div class="form-group">
                            <label>Estadio:</label>
                            <input type="text" name="estadio" required placeholder="Ej: Florencio Sola">
                        </div>
                        <button type="submit" class="btn-submit">Guardar Partido</button>
                    </form>
                </div>

                <!-- FORMULARIO 2: CREAR PREDICCIÓN DE USUARIO -->
                <div class="form-card">
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
                        <button type="submit" class="btn-submit">Enviar Predicción</button>
                    </form>
                </div>

                <!-- FORMULARIO 3: ACTUALIZAR RESULTADO FINAL -->
                <div class="form-card">
                    <h3>Cargar Resultado Final (`partidos`)</h3>
                    <form action="PhpTip-off/php/controllers/MatchController.php?action=update_score" method="POST">
                        <div class="form-group">
                            <label>ID Partido:</label>
                            <input type="number" name="id_partido" required placeholder="Ej: 1">
                        </div>
                        <div class="form-group">
                            <label>Goles Local Finales:</label>
                            <input type="number" name="goles_local" required placeholder="Ej: 2">
                        </div>
                        <div class="form-group">
                            <label>Goles Visitante Finales:</label>
                            <input type="number" name="goles_visitante" required placeholder="Ej: 1">
                        </div>
                        <div class="form-group">
                            <label>Estado:</label>
                            <select name="estado">
                                <option value="Finalizado">Finalizado</option>
                                <option value="En Vivo">En Vivo</option>
                                <option value="Suspendido">Suspendido</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-submit">Actualizar Resultado</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <h3>Tip-of oficial</h3>
    </footer>

</body>
</html>