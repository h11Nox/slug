<?php
namespace console\base;

/**
 * Class PlayerTimer
 * @package console\base
 */
class PlayerTimer {

	/** const int - seconds per move for user */
	const TIME_PER_MOVE = 60;

	/** @var int - move started time label */
	protected $time = -1;
	/** @var int - stopped time label */
	protected $stopTime = -1;

	/** @var bool - is active */
	protected $active = false;

	/**
	 * Start timer
	 */
	public function start() {
		if (!$this->active) {
			$this->time = microtime(true);
			$this->active = true;
		} else {
			$this->time = microtime(true) - ($this->stopTime - $this->time);
		}
		$this->stopTime = -1;
	}

	/**
	 * Stop timer
	 */
	public function stop() {
		if ($this->active) {
			$this->stopTime = microtime(true);
		}
	}

	/**
	 * Destroy timer
	 */
	public function destroy() {
		$this->time = $this->stopTime = -1;
		$this->active = false;
	}

	/**
	 * Restart timer
	 */
	public function reconnect() {
		if ($this->active) {
			$this->start();
		}
	}

	/**
	 * Get user timer
	 * @return float
	 * @throws TimerException
	 */
	public function getTime() {
		if ($this->time === -1) {
			throw new TimerException('Timer is not active');
		}
		return static::TIME_PER_MOVE - ceil(microtime(true) - $this->time);
	}
}

class TimerException extends \LogicException{}