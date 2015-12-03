<?php
namespace console\base\http;

/**
 * Class GameResponse
 * @package console\base\http
 */
class GameResponse {

	protected $action;
	protected $data = [];
	protected $card;
	protected $player;
	protected $active;
	protected $phase;
	protected $ids;
	protected $text;
	protected $id;

	/**
	 * Set action
	 *
	 * @param $action
	 * @return $this
	 */
	public function setAction($action) {
		$this->action = $action;
		return $this;
	}

	/**
	 * Set data param
	 *
	 * @param $key
	 * @param $value
	 * @return $this
	 */
	public function setDataParam($key, $value) {
		$this->data[$key] = $value;
		return $this;
	}

	/**
	 * Set card
	 *
	 * @param $card
	 * @return $this
	 */
	public function setCard($card) {
		$this->card = $card;
		return $this;
	}

	/**
	 * Set player
	 * @param $player
	 * @return $this;
	 */
	public function setPlayer($player) {
		$this->player = $player;
		return $this;
	}

	/**
	 * Set active
	 * @param $active
	 * @return $this
	 */
	public function setActive($active) {
		$this->active = $active;
		return $this;
	}

	/**
	 * Set phase
	 * @param $phase
	 * @return $this
	 */
	public function setPhase($phase) {
		$this->phase = $phase;
		return $this;
	}

	/**
	 * Set ids
	 * @param $ids
	 * @return $this
	 */
	public function setIds($ids) {
		$this->ids = $ids;
		return $this;
	}

	/**
	 * Set text
	 * @param $text
	 * @return $this
	 */
	public function setText($text) {
		$this->text = $text;
		return $this;
	}

	/**
	 * Set id
	 * @param $id
	 * @return $this
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * Get response
	 * @return object
	 */
	public function getResponse() {
		$data = [];
		foreach (['action', 'data', 'card', 'player', 'active', 'phase', 'ids', 'text', 'id'] as $p) {
			$data[$p] = $this->{$p};
		}
		return (object)array_filter(
			$data,
			function($item) {
				return isset($item);
			}
		);
	}
}