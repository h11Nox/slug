<?php
namespace common\base;
use common\models\FightUser;
use yii\base\Object;

/**
 * Player
 */
class Player extends Object {

	/** @var int $index */
	protected $index;
	/** @var FightUser $_user */
	protected $_user;
	protected $_owner;
	protected $data;

	/**
	 * @param FightUser $user
	 */
	public function initialize(FightUser $user) {
		$this->_user = $user;
		$this->_owner = (bool)$user->is_owner;
		$this->data = $this->getPlayerData();
	}

	/**
	 * Set index
	 * @param $index
	 */
	public function setIndex($index) {
		$this->index = $index;
	}

	/**
	 * Get user index
	 * @return mixed
	 */
	public function getIndex() {
		return $this->index;
	}

	/**
	 * @param $user
	 * @return mixed
	 */
	public function setGameUser($user) {
		return $this->_user = $user;
	}

	/**
	 * @return mixed
	 */
	public function getUser() {
		return $this->_user->user;
	}

	/**
	 * @return bool
	 */
	public function isOwner() {
		return $this->_owner;
	}

	/**
	 * Get Data
	 * @return mixed
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * @return PlayerData
	 */
	protected function getPlayerData() {
		return new PlayerData();
	}
}