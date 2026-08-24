<?php
class MatchModel {
    public static function getUpcomingMatches() {
        return [
            [
                'league' => 'Liga Profesional',
                'home_team' => 'Banfield',
                'away_team' => 'Belgrano',
                'home_badge' => 'img/banfield.png',
                'away_badge' => 'img/belgrano.png',
                'home_prob' => 40,
                'away_prob' => 60
            ],
            [
                'league' => 'Liga Profesional',
                'home_team' => 'Lanús',
                'away_team' => 'Talleres',
                'home_badge' => 'img/lanus.png',
                'away_badge' => 'img/talleres.png',
                'home_prob' => 68,
                'away_prob' => 32
            ],
            [
                'league' => 'Liga Profesional',
                'home_team' => 'Racing',
                'away_team' => 'Banfield',
                'home_badge' => 'img/racing.png',
                'away_badge' => 'img/banfield.png',
                'home_prob' => 69,
                'away_prob' => 31
            ],
            [
                'league' => 'Liga Profesional',
                'home_team' => 'Unión',
                'away_team' => 'Central Córdoba',
                'home_badge' => 'img/union.png',
                'away_badge' => 'img/central.png',
                'home_prob' => 38,
                'away_prob' => 62
            ]
        ];
    }
}