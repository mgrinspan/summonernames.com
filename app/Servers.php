<?php

namespace App;

class Servers {
    private static $servers = [
        'BR' => 'Brazil',
        'EUNE' => 'Europe Nordic & East',
        'EUW' => 'Europe West',
        'JP' => 'Japan',
        'KR' => 'Korea',
        'LAN' => 'Latin America North',
        'LAS' => 'Latin America South',
        'NA' => 'North America',
        'OCE' => 'Oceania',
        'PBE' => 'Public Beta Environment',
        'RU' => 'Russia',
        'TR' => 'Turkey',
    ];

    private static $regionMap = [
        'BR' => 'BR1',
        'EUNE' => 'EUN1',
        'EUW' => 'EUW1',
        'JP' => 'JP1',
        'KR' => 'KR',
        'LAN' => 'LA1',
        'LAS' => 'LA2',
        'NA' => 'NA1',
        'OCE' => 'OC1',
        'PBE' => 'PBE1',
        'RU' => 'RU',
        'TR' => 'TR1',
    ];

    private static $default = 'NA';

    public static function all() {
        return static::$servers;
    }

    public static function getDefault() {
        return static::$default;
    }

    public static function getRegion($server) {
        return static::$regionMap[strtoupper($server)];
    }
}
