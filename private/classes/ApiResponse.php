<?php

use LeagueWrap\Api;
use LeagueWrap\Response;

class ApiResponse {
	private $name;
	private $server;

	private $summoner;
	private $valid;

	private $expireTime;

	private $response;

	private static $API_KEY;

	public function __construct($name, $server) {
		self::$API_KEY = getenv('API_KEY');
		$this->name = $name;
		$this->server = $server;
		
		if($this->validate()) {
			try {
				$this->fetchSummoner();
			} catch(Response\Http404 $ex) {
				$this->response(true);
				return;
			} catch(Response\ResponseException $ex) {
				$this->response(false);
				return;
			}
			
			$this->response($this->getExpireTime());
		} else {
			$this->response(false);
		}
	}

	private function validate() {
		if(!is_string($this->name) || !is_string($this->server)) {
			return $this->valid = false;
		}
		
		$this->name = trim(rawurldecode($this->name));
		$this->server = strtoupper(trim($this->server));
		
		if(!isset(Servers::GetList()[$this->server])) {
			$this->server = Servers::GetDefault();
		}
		
		// TODO: Validate summoner name validity.
		
		return $this->valid = true;
	}

	private function fetchSummoner() {
		$api = new Api(self::$API_KEY);

		$api->setRegion($this->server); 

		$api->remember(600, new Cache); // cache for 10 minutes
		$api->limit(10, 10, $this->server, new Limit); // rate-limit 10 requests per 10 seconds
		$api->limit(500, 600, $this->server, new Limit); // rate-limit 500 requests per 600 seconds (10 minutes)

		return $this->summoner = $api->summoner()->info($this->name);
	}
	
	private function getExpireTime() {
		if(!$this->summoner->summonerLevel) {
			return true;
		}
		
		$months = $this->summoner->summonerLevel <= 6 ? 6 : $this->summoner->summonerLevel;
		
		return strtotime("+{$months} months", $this->summoner->revisionDate / 1000);
	}
	
	private function response($expireTime) {
		if(!$expireTime) {
			$response = [
				'error' => true
			];
		} else {
			$response = [
				'name' => $this->name,
				'server' => $this->server,
				'time' => $expireTime !== true ? $expireTime : 0
			];
		
			$this->logRequest();
		}
		
		$this->response = $response;
	}
	
	private function logRequest() {
		$db = new Database;

		$db
			->insert([
				'server',
				'name',
				'ip'
			])
			->into('history')
			->onDuplicateKeyUpdate('count', 'count + 1')
			->bindValues([
				'server' => $this->server,
				'name' => $this->name,
				'ip' => $_SERVER['REMOTE_ADDR']
			]);

		return $db->execute();
	}

	function getResponse() {
		return $this->response;
	}
}
