<?php

namespace App\Models;

class Servers {
	private static $servers = [
		'BR'   => 'Brazil',
		'EUNE' => 'Europe Nordic & East',
		'EUW'  => 'Europe West',
		'JP'   => 'Japan',
		'KR'   => 'Korea',
		'LAN'  => 'Latin America North',
		'LAS'  => 'Latin America South',
		'NA'   => 'North America',
		'OCE'  => 'Oceania',
		'PBE'  => 'Public Beta Environment',
		'RU'   => 'Russia',
		'TR'   => 'Turkey',
	];

	private static $platformMap = [
		'BR'   => 'BR1',
		'EUNE' => 'EUN1',
		'EUW'  => 'EUW1',
		'JP'   => 'JP1',
		'KR'   => 'KR',
		'LAN'  => 'LA1',
		'LAS'  => 'LA2',
		'NA'   => 'NA1',
		'OCE'  => 'OC1',
		'PBE'  => 'PBE1',
		'RU'   => 'RU',
		'TR'   => 'TR1',
	];

	private static $regionMap = [
		'BR'   => 'americas',
		'EUNE' => 'europe',
		'EUW'  => 'europe',
		'JP'   => 'asia',
		'KR'   => 'asia',
		'LAN'  => 'americas',
		'LAS'  => 'americas',
		'NA'   => 'americas',
		'OCE'  => 'americas',
		'PBE'  => 'americas',
		'RU'   => 'asia',
		'TR'   => 'europe',
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

	public static function getPlatform($server) {
		return static::$platformMap[strtoupper($server)];
	}
}
