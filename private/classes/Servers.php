<?php

class Servers {
	private static $servers = [
		'BR' => 'Brazil',
		'EUNE' => 'Europe Nordic & East',
		'EUW' => 'Europe West',
		'KR' => 'Korea',
		'LAN' => 'Latin America North',
		'LAS' => 'Latin America South',
		'NA' => 'North America',
		'OCE' => 'Oceania',
		'RU' => 'Russia',
		'TR' => 'Turkey'
	];

	private static $DEFAULT = 'NA';
	
	public static function GetList() {
		return self::$servers;
	}

	public static function GetDefault() {
		return self::$DEFAULT;
	}
}
