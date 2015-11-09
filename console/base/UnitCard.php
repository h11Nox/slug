<?php
namespace console\base;
use yii\helpers\Html;

/**
 * Class UnitCard
 * @package console\base
 */
class UnitCard extends Card implements Damageable {

	/**
	 * Damage player
	 * @param Player $player
	 */
	public function damage(Player $player) {

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
}