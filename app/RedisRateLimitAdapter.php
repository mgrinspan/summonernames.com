<?php

namespace App;

use Illuminate\Support\Facades\Redis;
use PalePurple\RateLimit\Adapter;

class RedisRateLimitAdapter extends Adapter {
	public function set($key, $value, $ttl) {
		return Redis::set($key, $value, $ttl);
	}

	public function get($key) {
		return Redis::get($key);
	}

	public function exists($key) {
		return Redis::exists($key);
	}

	public function del($key) {
		return Redis::delete($key);
	}
}
