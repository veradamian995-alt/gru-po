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

    <!-- 1. HEADER -->
    <header class="header">
        <div class="logo">
            <img src="PhpTip-off/img/logo.png" alt="Tip-Off Logo">
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Buscar...">
        </div>
    </header>

    <main class="container">
        <!-- 2. HERO BANNER (Sin botón) -->
        <section class="hero" style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('PhpTip-off/img/stadium.jpg');">
            <div class="hero-content">
                <h1>PREDICCIONES DE FUTBOL ARGENTINO EN VIVO</h1>
            </div>
        </section>

        <!-- 3. PRÓXIMOS PARTIDOS DE FÚTBOL (HORIZONTAL) -->
        <section class="section">
            <h2 class="section-title">PROXIMOS PARTIDOS DE FUTBOL</h2>
            <div class="carousel-container">
                <div class="matches-grid">
                    <?php foreach ($matches as $match): ?>
                        <div class="match-card">
                            <span class="league-name"><?= htmlspecialchars($match['league']) ?></span>
                            <div class="teams">
                                <img src="<?= htmlspecialchars($match['home_badge']) ?>" alt="<?= htmlspecialchars($match['home_team']) ?>" class="team-logo">
                                <img src="<?= htmlspecialchars($match['away_badge']) ?>" alt="<?= htmlspecialchars($match['away_team']) ?>" class="team-logo">
                            </div>
                            <div class="prob-labels">
                                <span><?= $match['home_prob'] ?>%</span>
                                <span><?= $match['away_prob'] ?>%</span>
                            </div>
                            <div class="prob-bar">
                                <div class="prob-fill" style="width: <?= $match['home_prob'] ?>%;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- 4. PREDICCIÓN DE TORNEO -->
        <section class="section text-center">
            <h2 class="section-title">Predicion de Torneo</h2>
            <div class="tournament-banner">
                <img src="PhpTip-off/img/libertadores.jpg" alt="CONMEBOL Libertadores">
            </div>
        </section>

        <!-- 5. DETALLES Y ESTADÍSTICAS EXTENDIDAS POR PARTIDO -->
        <section class="section">
            <div class="matches-list-extended">
                <?php foreach ($matches as $match): ?>
                    <div class="match-extended-row">
                        <!-- Tarjeta izquierda -->
                        <div class="match-card">
                            <span class="league-name"><?= htmlspecialchars($match['league']) ?></span>
                            <div class="teams">
                                <img src="<?= htmlspecialchars($match['home_badge']) ?>" alt="<?= htmlspecialchars($match['home_team']) ?>" class="team-logo">
                                <img src="<?= htmlspecialchars($match['away_badge']) ?>" alt="<?= htmlspecialchars($match['away_team']) ?>" class="team-logo">
                            </div>
                            <div class="prob-labels">
                                <span><?= $match['home_prob'] ?>%</span>
                                <span><?= $match['away_prob'] ?>%</span>
                            </div>
                            <div class="prob-bar">
                                <div class="prob-fill" style="width: <?= $match['home_prob'] ?>%;"></div>
                            </div>
                        </div>

                        <!-- Panel amplio a la derecha -->
                        <div class="match-extended-panel">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <!-- 6. FOOTER -->
    <footer class="footer">
        <h3>Tip-of oficial</h3>
    </footer>

</body>
</html>