<?php

use LeagueWrap\LimitInterface;

class Limit implements LimitInterface {

	/**
	 * Whether or not a Reds connection is available.
	 *
	 * @var bool
	 */
	private $valid;

	/**
	 * The key that will be used for the Redis storage.
	 *
	 * @var string
	 */
	private $key;

	/**
	 * The max amount of hits the key can take in the given amount
	 * of seconds.
	 *
	 * @var int
	 */
	private $hits;

	/**
	 * The amount of seconds to let the hits accumulate for.
	 *
	 * @var int
	 */
	private $seconds;

	/**
	 * The region that is attached to this limit counter.
	 *
	 * @var string
	 */
	private $region;

	/**
	 * The Redis instance.
	 *
	 * @var Redis
	 */
	protected $redis;
	
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
	 * Returns a new instance of the current limit object.
	 */
	public function newInstance() {
		return new Static();
	}

	/**
	 * Sets the rate limit for the given region.
	 *
	 * @param int $hits
	 * @param int $seconds
	 * @param string $region
	 * @chainable
	 */
	public function setRate($hits, $seconds, $region) {
		$this->hits = (int) $hits;
		$this->seconds = (int) $seconds;
		$this->region = strtolower($region);
		$this->key = "leagueWrap.hits.{$this->region}.{$this->hits}.{$this->seconds}";

		return $this;
	}

	/**
	 * Returns the region that is tied to this limit counter.
	 *
	 * @return string
	 */
	public function getRegion() {
		return $this->region;
	}

	/**
	 * Applies a hit to the given regions rate limiting.
	 *
	 * @param int $count Default 1
	 * @return bool
	 */
	public function hit($count = 1) {
		$hitsLeft = $this->redis->get($this->key);

		if($hitsLeft === false) {
			$hitsLeft = $this->hits;
			$this->redis->setex($this->key, $this->seconds, $this->hits);
		}

		return !($hitsLeft < $count || $this->redis->decrBy($this->key, $count) === false);
	}

	/**
	 * Check how many hits the given region has remaining.
	 *
	 * @return int
	 */
	public function remaining() {
		$hitsLeft = $this->redis->get($this->key);
		
		return $hitsLeft === false ? $this->hits : $hitsLeft;
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
