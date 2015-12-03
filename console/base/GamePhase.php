<?php
namespace console\base;

/**
 * Class GamePhase
 * @package console\base
 */
class GamePhase {

	/** @var int $phase */
	protected $phase = 1;

	/** @var int game settings */
	const PLAYERS_COUNT = 2;
	const PHASES_COUNT = 2;

	/**
	 * End game phase
	 */
	public function end() {
		$this->phase++;
		if ($this->phase > static::PLAYERS_COUNT * static::PHASES_COUNT) {
			$this->phase = 1;
		}
	}

	/**
	 * Get active player index
	 * @return int
	 */
	public function getPlayer() {
		return ($this->phase + 1) % static::PLAYERS_COUNT + 1;
	}

	/**
	 * Get active fight phase
	 * @return int
	 */
	public function getPhase() {
		return ceil($this->phase / static::PHASES_COUNT);
	}
}