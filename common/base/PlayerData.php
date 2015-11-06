<?php
namespace common\base;

/**
 * Player Data
 */
class PlayerData{

	protected $points = 10;

	/**
	 * Get current user health
	 * @return int
	 */
	public function getHealth() {
		return 20;
	}

	/**
	 * Get Max Point
	 * @return int
	 */
	public function getMaxPoint() {
		return 10;
	}

	/**
	 * Get points
	 * @return int
	 */
	public function getPoints() {
		return $this->points;
	}

	/**
	 * Use points
	 * @param $points
	 */
	public function usePoints($points) {
		$this->points -= $points;
	}
}
