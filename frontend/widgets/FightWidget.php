<?php
namespace frontend\widgets;
use common\base\Game;
use common\base\Player;
use common\models\Fight;
use HttpException;
use Yii;

/**
 * Fight Widget
 * Class FightWidget
 * @package frontend\widgets
 */
class FightWidget extends ActionWidget {

	public $fight;

	/**
	 * Fight Page
	 * @return string
	 */
	public function actionIndex() {
		$game = new Game();

		$player = new Player();
		$player->initialize($this->fight->owner);
		$game->setPlayer1($player);

		$owner = Yii::$app->user->getIdentity()->cid == $this->fight->owner->user_id;
		if (!$owner) {
			$player2 = new Player();
			$player2->initialize($this->fight->user);
			$game->setPlayer2($player2);
		}

		return $this->render('index', ['game' => $game, 'fight' => $this->fight, 'owner' => $owner]);
	}

	/**
	 * Load User
	 * @return string
	 * @throws HttpException
	 */
	public function actionGetUser() {
		$fight = Fight::findOne(Yii::$app->request->post('fight'));
		if (!$fight) {
			throw new HttpException(404);
		}
		/*if(!$fight->check(Yii::$app->request->post('user'))){
			throw new HttpException(403);
		}*/

		$game = new Game();
		$player = new Player();
		$player->initialize($fight->user);
		$game->setPlayer2($player);

		return $this->render('player', ['game' => $game, 'player' => $player]);
	}

	/**
	 * @return string
	 */
	public function actionCard() {
		return $this->response([]);
	}

	protected function response($response) {
		return json_encode($response);
	}
}