<?php

namespace App\Http\Controllers;

use App\Models\Servers;
use App\RedisRateLimitAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use JsonException;
use PalePurple\RateLimit\RateLimit;
use Throwable;

class ApiController extends Controller {
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

		$summonerData = $this->getSummonerByName($apiKey, $server, $summonerName);

		if($summonerData === null) {
			$response['error'] = true;

			return $response;
		}

		if($summonerData === true) {
			$response['time'] = 0;
		} else {
			$response['name'] = $summonerData->name;

			if($this->rateLimitExceeded($region)) {
				$response['error'] = true;

				return $response;
			}

			$matchEnd = $this->getLastMatchEndByPUUID($apiKey, $server, $summonerData->puuid);

			if($matchEnd === null && !isset($summonerData->summonerLevel)) {
				$response['error'] = true;

				return $response;
			}

			$months = min(max($summonerData->summonerLevel, 6), 30);
			$response['time'] = strtotime("+{$months} months", max($summonerData->revisionDate, (int)$matchEnd) / 1000);
		}

		DB::table('history')->insert([
			'name'   => $response['name'],
			'server' => strtoupper($response['server']),
			'ip'     => $request->ip(),
		]);

		return $response;
	}

	protected function rateLimitExceeded($region) {
		$hitLimit = false;
		collect(explode(',', config('services.riot-api.rate-limits')))
			->each(function ($limit) use (&$hitLimit, &$adapter, $region) {
				$limit = explode(':', $limit);

				$limiter = new RateLimit('rate-limit', (int)$limit[0] + 1, (int)$limit[1], new RedisRateLimitAdapter);

				$hitLocalLimit = !$limiter->check($region);

				$hitLimit = $hitLimit || $hitLocalLimit;
			});

		return $hitLimit;
	}

	protected function getSummonerByName($apiKey, $server, $summonerName) {
		$region = Servers::getPlatform($server);
		$summonerName = rawurlencode($summonerName);

		$cacheKey = 'summoner-' . $region . '-' . sha1($summonerName);

		if(Cache::has($cacheKey)) {
			return Cache::get($cacheKey);
		}

		$url = "https://{$region}.api.riotgames.com/lol/summoner/v4/summoners/by-name/{$summonerName}?api_key={$apiKey}";

		$response = null;
		try {
			$response = file_get_contents($url);
		} catch(Throwable $exception) {
			if(Str::contains($exception->getMessage(), 'Failed to open stream: HTTP request failed! HTTP/1.1 404 Not Found')) {
				return true;
			}

			return null;
		}

		$data = null;
		try {
			$data = json_decode($response, flags: JSON_THROW_ON_ERROR);
		} catch(JsonException $exception) {
			return null;
		}

		if(isset($data->summonerLevel, $data->name, $data->puuid)) {
			Cache::put($cacheKey, $data, now()->addMinutes(10));

			return $data;
		}

		return null;
	}

	protected function getLastMatchEndByPUUID($apiKey, $server, $puuid) {
		$region = Servers::getRegion($server);
		$cacheKey = 'matchIds-' . $region . '-' . sha1($puuid);

		if(Cache::has($cacheKey)) {
			return Cache::get($cacheKey);
		}

		$url = "https://{$region}.api.riotgames.com/lol/match/v5/matches/by-puuid/{$puuid}/ids?start=0&count=1&api_key={$apiKey}";

		$response = null;
		try {
			$response = file_get_contents($url);
		} catch(Throwable $exception) {
			if(Str::contains($exception->getMessage(), 'Failed to open stream: HTTP request failed! HTTP/1.1 404 Not Found')) {
				return true;
			}

			return null;
		}
		$data = null;
		try {
			$data = json_decode($response, flags: JSON_THROW_ON_ERROR);
		} catch(JsonException) {
			return null;
		}

		if(empty($data)) {
			return true;
		}

		$lastMatch = $this->getMatchEndById($apiKey, $server, $data[0]);

		if($lastMatch === true) {
			return true;
		}

		Cache::put($cacheKey, $lastMatch, now()->addMinutes(10));

		return $lastMatch;
	}

	protected function getMatchEndById($apiKey, $server, $matchId) {
		$region = Servers::getRegion($server);
		$cacheKey = 'match-' . $region . '-' . sha1($matchId);

		if(Cache::has($cacheKey)) {
			return Cache::get($cacheKey);
		}

		$url = "https://{$region}.api.riotgames.com/lol/match/v5/matches/{$matchId}?api_key={$apiKey}";

		$response = null;
		try {
			$response = file_get_contents($url);
		} catch(Throwable $exception) {
			if(Str::contains($exception->getMessage(), 'Failed to open stream: HTTP request failed! HTTP/1.1 404 Not Found')) {
				return true;
			}

			return null;
		}

		$data = null;
		try {
			$data = json_decode($response, flags: JSON_THROW_ON_ERROR);
		} catch(JsonException) {
			return null;
		}

		return $data->info->gameEndTimestamp ?? true;
	}

	public function recent() {
		return DB::table('history')
			->select('name', 'server')
			->groupBy('name', 'server')
			->orderByDesc(DB::raw('max(`created_at`)'))
			->limit(10)
			->get();
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
