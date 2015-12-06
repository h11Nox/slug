<?php
namespace console\base;

use console\base\interfaces\UnitInterface;
use yii\helpers\Html;

/**
 * Class UnitCard
 * @package console\base
 */
class UnitCard extends Card implements UnitInterface {

	/**
	 * Params
	 * @var array
	 */
	protected $params = [ 'damage', 'hp' ];

	/**
	 * Do initialization
	 */
	protected function init() {
		$this->ready = 0;
	}

	/**
	 * Get additional params list
	 *
	 * @return array
	 */
	protected function getAdditionalParams() {
		return [ 'ready' ];
	}

	/**
	 * Do after turn action
	 */
	public function afterTurn() {
		$this->ready = 1;
	}

	/**
	 * Damage player
	 * @param Player $player
	 */
	public function attack(Player $player) {

	}

	/**
	 * @inheritdoc
	 */
	public function getHtml() {
		$content = Html::img($this->img, [
			'alt' => $this->title,
			'title' => $this->title,
		]);
		$content .= Html::tag('span', '', [
			'class' => 'hp',
			'data-type' => 'hp'
		]);
		$content .= Html::tag('span', '', [
				'class' => 'mp',
				'data-type' => 'mp'
		]);

		return Html::tag('div', $content, [
			'data-type' => $this->type
		]);
	}

	/**
	 * Provides possibility to damaged
	 *
	 * @param $damage
	 * @return void
	 */
	public function receiveDamage($damage) {
		$this->hp -= $damage;
		if ($this->hp <= 0) {
			// kill unit
		}
	}
}