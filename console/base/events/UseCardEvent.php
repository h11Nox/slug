<?php
namespace console\base\events;

use console\base\Card;

/**
 * Class UseCardEvent
 * @package console\base\events
 */
class UseCardEvent extends \yii\base\Event{

	/**
	 * Card
	 * @var Card
	 */
	public $card;

	/**
	 * Card index
	 * @var int
	 */
	public $index;

	/**
	 * Player index
	 * @var int
	 */
	public $player;
}