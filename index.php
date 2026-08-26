<?php
require_once  'PhpTip-off/php/models/MatchModel.php';
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

    <!-- Header -->
    <header class="header">
        <div class="logo">
            <img src="PhpTip-off/img/img/logo.png" alt="Tip-Off Logo">
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Buscar...">
        </div>
    </header>

    <main class="container">
        <!-- Hero Banner -->
        <section class="hero" style="background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('img/stadium.jpg');">
            <button class="btn-primary">Ver Predicciones de Hoy</button>
        </section>

        <!-- Próximos Partidos -->
        <section class="section">
            <h2 class="section-title">PROXIMOS PARTIDOS DE FUTBOL</h2>
            <div class="carousel-container">
                <button class="nav-btn left" id="btn-prev">&#10094;</button>
                
                <div class="matches-grid" id="matches-grid">
                    <?php foreach ($matches as $match): ?>
                        <div class="match-card">
                            <span class="league-name"><?= $match['league'] ?></span>
                            <div class="teams">
                                <img src="<?= $match['home_badge'] ?>" alt="<?= $match['home_team'] ?>" class="team-logo">
                                <img src="<?= $match['away_badge'] ?>" alt="<?= $match['away_team'] ?>" class="team-logo">
                            </div>
                            <div class="prob-labels">
                                <span><?= $match['home_prob'] ?>%</span>
                                <span><?= $match['away_prob'] ?>%</span>
                            </div>
                            <div class="prob-bar">
                                <div class="prob-fill" style="width: <?= $match['home_prob'] ?>%;"></div>
                            </div>
                            <div class="card-buttons">
                                <button class="btn-card-left"></button>
                                <button class="btn-card-right"></button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button class="nav-btn right" id="btn-next">&#10095;</button>
            </div>
        </section>

        <!-- Predicción de Torneo -->
        <section class="section text-center">
            <h2 class="section-title">Predicion de Torneo</h2>
            <div class="tournament-banner">
                <img src="PhpTip-off/img/libertadores.jpg" alt="CONMEBOL Libertadores">
            </div>
        </section>
    </main>

    <script src="PhpTip-off/js/main.js"></script>
</body>
</html>