<?php

namespace App\Http\Controllers;

use App\Servers;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class ApiController extends Controller {
    public function eta(Request $request, $server, $summoner) {
        $apiKey = config('services.riot-api.key');
        $region = strtolower(Servers::getRegion($server));
        $summoner = rawurlencode($summoner);
        $url = "https://{$region}.api.riotgames.com/lol/summoner/v3/summoners/by-name/{$summoner}?api_key={$apiKey}";
        $cacheKey = $region . '-' . strtolower($summoner);

        $cached = Cache::get($cacheKey);

        if ($cached) {
            DB::table('history')->insert([
                'name' => $cached['name'],
                'server' => strtoupper($cached['server']),
                'ip' => $request->ip(),
            ]);

            return $cached;
        }

        $response = [
            'name' => $summoner,
            'time' => null,
            'server' => strtoupper($server),
        ];
        try {
            $data = json_decode(file_get_contents($url));

            if (isset($data->status->status_code) && $data->status->status_code === 404) {
                $response['time'] = 0;
            } elseif (isset($data->summonerLevel, $data->name, $data->revisionDate)) {
                $months = max(min($data->summonerLevel, 6), 30);

                $response['name'] = $data->name;
                $response['time'] = strtotime("+{$months} months", $data->revisionDate / 1000);
            } else {
                throw new Exception;
            }
        } catch (Throwable $exception) {
            if (trim($exception->getMessage()) === "file_get_contents({$url}): failed to open stream: HTTP request failed! HTTP/1.1 404 Not Found") {
                $response['time'] = 0;
            } else {
                $response['error'] = true;
            }
        }

        DB::table('history')->insert([
            'name' => $response['name'],
            'server' => strtoupper($response['server']),
            'ip' => $request->ip(),
        ]);

        Cache::put($cacheKey, $response, now()->addMinutes(10));

        return $response;
    }

    public function recent() {
        return DB::table('history')->distinct()->latest()->limit(10)->select('name', 'server')->get();
    }

    public function feedback(Request $request) {
        $valid = $request->validate([
            'email' => 'nullable|email',
            'message' => 'required|string|min:1|max:65535',
        ]);

        DB::table('feedback')->insert($valid + ['ip' => $request->ip()]);

        return '';
    }
}
