<?php
namespace console\base;

/**
 * Force card to be damageable
 * Interface Damageable
 * @package console\base
 */
interface Damageable {

	public function damage(Player $player);
}