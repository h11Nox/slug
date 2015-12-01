<?php
namespace console\base\events;

/**
 * Class Player
 * @package console\base\events
 */
abstract class PlayerEvent extends \yii\base\Event{

	/**
	 * Player index
	 * @var int
	 */
	public $player;
}