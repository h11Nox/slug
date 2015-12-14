<?php
namespace common\base;

/**
 * Player Data
 */
class PlayerData {

	protected $points = 10;
	protected $hp = 20;

	/**
	 * Get current user health
	 * @return int
	 */
	public function getHealth() {
		return $this->hp;
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

	/**
	 * Add points
	 * @param $points
	 */
	public function addPoints($points) {
		$this->points += $points;
	}
}
