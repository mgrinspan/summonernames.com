<?php

use LeagueWrap\CacheInterface;

class Cache implements CacheInterface {

	/**
	 * Whether or not a Reds connection is available.
	 *
	 * @var bool
	 */
	private $valid;

	/**
	 * The Redis instance.
	 *
	 * @var Redis
	 */
	private $redis;

	/**
	 * Sets up Redis if it exists.
	 */
	public function __construct() {
		try {
			$this->redis = new Redis;
			$this->redis->connect('127.0.0.1', 6379);
			$this->valid = $this->redis->ping() === '+PONG';
		} catch(RedisException $e) {
			$this->valid = false;
		}
	}

	/**
	 * Adds the response string into the cache under the given key for
	 * $seconds.
	 *
	 * @param string $key
	 * @param string $response
	 * @param int $seconds
	 * @return bool
	 */
	public function set($key, $response, $seconds) {
		if($response->getCode() >= 400) {
			return false;
		}

		if($this->isValid()) {
			return $this->redis->setex($key, $seconds, $response->__toString());
		} else {
			return false;
		}
	}

	/**
	 * Determines if the cache has the given key.
	 *
	 * @param string $key
	 * @return bool
	 */
	public function has($key) {
		if($this->isValid()) {
			return $this->redis->exists($key);
		} else {
			return false;
		}
	}

	/**
	 * Gets the contents that are stored at the given key.
	 *
	 * @param string $key
	 * @return string
	 */
	public function get($key) {
		return $this->redis->get($key);
	}

	/**
	 * Is the current limit object valid on this machine (i.e. does
	 * the machine have Redis).
	 *
	 * @return bool
	 */
	public function isValid() {
		try {
			return $this->valid && $this->redis->ping() === '+PONG';
		} catch(RedisException $e) {
			return false;
		}
	}
}
