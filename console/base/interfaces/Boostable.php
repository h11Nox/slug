<?php
namespace console\base\interfaces;

/**
 * Force card to be boostable
 * Interface Boostable
 * @package console\base
 */
interface Boostable {

	/**
	 * Do some boost action
	 *
	 * @param UnitInterface $unit
	 */
	public function boost(UnitInterface $unit);
}