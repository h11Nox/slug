<?php
namespace console\base;
use console\base\interfaces\Damageable;
use console\base\interfaces\UnitInterface;

/**
 * Class DamageCard
 * @package console\base
 */
class DamageCard extends Card implements Damageable {

	/**
	 * Params
	 * @var array
	 */
	protected $params = [ 'damage' ];

	/**
	 * Do damage
	 *
	 * @param interfaces\UnitInterface $unit
	 * @param $damage
	 */
	public function damage(UnitInterface $unit, $damage) {
		$unit->receiveDamage($damage);
	}
}