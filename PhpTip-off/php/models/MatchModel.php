<?php
class MatchModel {
    public static function getUpcomingMatches() {
        return [
            [
                'league' => 'Liga Profesional',
                'home_team' => 'Banfield',
                'away_team' => 'Belgrano',
                'home_badge' => 'PhpTip-off/img/banfield.png',
                'away_badge' => 'PhpTip-off/img/belgrano.png',
                'home_prob' => 40,
                'away_prob' => 60
            ],
            [
                'league' => 'Liga Profesional',
                'home_team' => 'Lanús',
                'away_team' => 'Talleres',
                'home_badge' => 'PhpTip-off/img/lanus.png',
                'away_badge' => 'PhpTip-off/img/talleres.png',
                'home_prob' => 68,
                'away_prob' => 32
            ],
            [
                'league' => 'Liga Profesional',
                'home_team' => 'Racing',
                'away_team' => 'Banfield',
                'home_badge' => 'PhpTip-off/img/racing.png',
                'away_badge' => 'PhpTip-off/img/banfield.png',
                'home_prob' => 69,
                'away_prob' => 31
            ],
            [
                'league' => 'Liga Profesional',
                'home_team' => 'Unión',
                'away_team' => 'Central Córdoba',
                'home_badge' => 'PhpTip-off/img/union.png',
                'away_badge' => 'PhpTip-off/img/central.png',
                'home_prob' => 38,
                'away_prob' => 62
            ]
        ];
    }
}