<?php

namespace App\Http\Controllers;

use App\Servers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Throwable;
use Touhonoob\RateLimit\RateLimit;

class ApiController extends Controller {
	protected function getSummonerByName($apiKey, $region, $summonerName) {
		$summonerName = rawurlencode($summonerName);

		$cacheKey = 'summoner-' . $region . '-' . sha1($summonerName);

		if(Cache::has($cacheKey)) {
			return Cache::get($cacheKey);
		}

		$url = "https://{$region}.api.riotgames.com/lol/summoner/v4/summoners/by-name/{$summonerName}?api_key={$apiKey}";

		try {
			$data = json_decode(file_get_contents($url), false, 512, JSON_THROW_ON_ERROR);

			if(isset($data->summonerLevel, $data->name, $data->accountId)) {
				Cache::put($cacheKey, $data, now()->addMinutes(10));

				return $data;
			} else {
				return null;
			}
		} catch(Throwable $exception) {
			return null;
		}
	}

	protected function getLastMatchByAccountId($apiKey, $region, $accountId) {
		$cacheKey = 'matches-' . $region . '-' . sha1($accountId);

		if(Cache::has($cacheKey)) {
			return Cache::get($cacheKey);
		}

		$url = "https://{$region}.api.riotgames.com/lol/match/v4/matchlists/by-account/{$accountId}?beginIndex=0&endIndex=1&api_key={$apiKey}";

		try {
			$data = json_decode(file_get_contents($url), false, 512, JSON_THROW_ON_ERROR);

			if(isset($data->matches[0])) {
				$lastMatch = $data->matches[0];

				Cache::put($cacheKey, $lastMatch, now()->addMinutes(10));

				return $lastMatch;
			} else {
				return null;
			}
		} catch(Throwable $exception) {
			return null;
		}
	}

	public function eta(Request $request, $server, $summonerName) {
		$apiKey = config('services.riot-api.key');
		$region = strtolower(Servers::getRegion($server));

		$response = [
			'name'   => $summonerName,
			'time'   => null,
			'server' => strtoupper($server),
			'error'  => false,
		];

		if($this->rateLimitExceeded($region)) {
			$response['error'] = true;

			return $response;
		}

		$summonerData = $this->getSummonerByName($apiKey, $region, $summonerName);

		if($summonerData === null || $this->rateLimitExceeded($region)) {
			$response['error'] = true;

			return $response;
		}

		$matchData = $this->getLastMatchByAccountId($apiKey, $region, $summonerData->accountId);

		if($matchData === null) {
			$response['error'] = true;

			return $response;
		}

		$months = min(max($summonerData->summonerLevel, 6), 30);

		$response['name'] = $summonerData->name;
		$response['time'] = strtotime("+{$months} months", $matchData->timestamp / 1000);

		DB::table('history')->insert([
			'name'   => $response['name'],
			'server' => strtoupper($response['server']),
			'ip'     => $request->ip(),
		]);

		return $response;
	}

	protected function rateLimitExceeded($region) {
		$adapter = new class {
			public function __call($method, $arguments) {
				return Redis::{$method}(...$arguments);
			}
		};

		$hitLimit = false;
		collect(explode(',', config('services.riot-api.rate-limits')))
			->each(function ($limit) use (&$hitLimit, &$adapter, $region) {
				$limit = explode(':', $limit);

				$limiter = new RateLimit('rate-limit', (int)$limit[0] + 1, (int)$limit[1], $adapter);

				$hitLocalLimit = !$limiter->check($region);

				$hitLimit = $hitLimit || $hitLocalLimit;
			});

		return $hitLimit;
	}

	public function recent() {
		return DB::table('history')->distinct()->latest()->limit(10)->select('name', 'server')->get();
	}

	public function feedback(Request $request) {
		$valid = $request->validate([
			'email'   => 'nullable|email',
			'message' => 'required|string|min:1|max:65535',
		]);

		DB::table('feedback')->insert($valid + ['ip' => $request->ip()]);

		return '';
	}
}
