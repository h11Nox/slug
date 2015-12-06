<?php
namespace console\base\interfaces;

/**
 * Force card to be damageable
 * Interface Damageable
 * @package console\base
 */
interface Damageable {

	/**
	 * Do some damage
	 *
	 * @param UnitInterface $unit
	 * @param $damage
	 */
	public function damage(UnitInterface $unit, $damage);
}