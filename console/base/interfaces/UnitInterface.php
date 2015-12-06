<?php
namespace console\base\interfaces;

/**
 * Class UnitInterface
 * @package console\base\interfaces
 */
interface UnitInterface {

	/**
	 * Receive damage
	 *
	 * @param $damage
	 * @return mixed
	 */
	public function receiveDamage($damage);

}