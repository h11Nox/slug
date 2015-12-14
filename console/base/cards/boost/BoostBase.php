<?php
namespace console\base\cards\boost;

use console\base\BoostCard;
use console\base\interfaces\UnitInterface;

/**
 * Class BoostBase
 * @package console\base\cards\boost
 */
abstract class BoostBase extends BoostCard{

	/**
	 * @param UnitInterface $unit
	 * @return mixed
	 */
	abstract public function boostUnit(UnitInterface $unit);
}

class BoostException extends \Exception{}
class BoostUnitException extends BoostException{}