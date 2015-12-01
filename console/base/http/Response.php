<?php
namespace console\base\http;

/**
 * Class Response
 * Fight response
 * @package console\base\http
 */
class Response {

	protected $action;
	protected $player;
	protected $active;
	protected $card;
	protected $data;

	/**
	 * Set action
	 * @param $action
	 * @return $this
	 */
	public function setAction($action) {
		$this->action = $action;
		return $this;
	}

	/**
	 * Set player
	 * @param $player
	 * @return $this
	 */
	public function setPlayer(\console\base\Player $player) {
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
	 * Set card
	 * @param \console\base\Card $card
	 * @return $this
	 */
	public function setCard(\console\base\Card $card) {
		$this->card = $card;
		return $this;
	}
	/**
	 * Get Object data
	 * @return array
	 */
	public function getResponseData() {
		$data = [
			'action' => $this->action,
			'data' => []
		];
		if (!empty($this->player)) {
			$data['player'] = $this->player->getIndex();
			$data['data'][$this->player->getIndex()] = $this->player->getResponse();
		}
		if ($this->active !== null) {
			$data['active'] = $this->active;
		}
		if (!empty($this->card)) {
			$data['card'] = [
				'data' => array_merge($this->card->getAttributes(), [
					'id' => $this->card->getIndex(),
					'text' => $this->card->getHtml()
				]),
				'params' => $this->card->getParams()
			];
		}
		return $data;
	}
}