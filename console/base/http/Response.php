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
	protected $players;
	protected $card;
	protected $text;
	protected $data;
	protected $game;
	protected $ids = false;
	protected $id;

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
	 * Set players
	 * @param $players
	 * @return $this
	 */
	public function setPlayers(array $players) {
		$this->players = $players;
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
	 * Set game
	 *
	 * @param $game
	 * @return $this
	 */
	public function setFight($game) {
		$this->game = $game;
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
	 * Get Object data
	 * @return array
	 */
	public function getResponseData() {
		$response = new GameResponse();
		$response->setAction($this->action);
		if (!empty($this->player)) {
			$response->setPlayer($this->player->getIndex())
				->setDataParam($this->player->getIndex(), $this->player->getResponse());
		}
		if (!empty($this->card)) {
			$response->setCard([
				'data' => array_merge($this->card->getAttributes(), [
					'id' => $this->card->getIndex(),
					'text' => $this->card->getHtml()
				]),
				'params' => $this->card->getParams()
			]);
		}
		if (!empty($this->players)) {
			foreach ($this->players as $p) {
				$response->setDataParam($p->getIndex(), $p->getResponse());
			}
		}
		if (!empty($this->game)) {
			$response->setActive($this->game->getPhase()->getPlayer())
				->setPhase($this->game->getPhase()->getPhase());
			if ($this->ids) {
				$ids = [];
				foreach ($this->game->getPlayers() as $p) {
					$ids[$p->getIndex()] = $p->getFightUser()->user_id;
				}
				$response->setIds($ids);
				unset($ids);
			}
		}
		$response->setText($this->text)
			->setId($this->id);

		return $response->getResponse();
	}
}