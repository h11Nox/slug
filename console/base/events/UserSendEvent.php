<?php
namespace console\base\events;

use console\base\Card;

/**
 * Class UserSendEvent
 * Catch when the message will be send to user
 * @package console\base\events
 */
class UserSendEvent extends \yii\base\Event{

	/**
	 * Player index
	 * @var int
	 */
	public $player;
}