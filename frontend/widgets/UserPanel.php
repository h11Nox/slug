<?php
namespace frontend\widgets;

use Yii;
use yii\bootstrap\Widget;
use yii\helpers\Html;

/**
 * Панель пользователя
 */
class UserPanel extends Widget {

	/**
	 * @return string
	 */
	public function run() {
		$html = '';
		if (!Yii::$app->user->isGuest) {
			$identity = Yii::$app->user->getIdentity();
			$img = Html::img($identity->img->getThumb('30x30'), [
				'alt' => $identity->username,
				'title' => $identity->username
			]);

			$html = '<div class="user-panel"><div class="row">';
			$html .= '<div class="col-md-6"></div><div class="col-md-3">';
			$html .= '<span class="rating">Рейтинг: <span>'.$identity->rating.'</span></span></div>';
			$html .= '<div class="col-md-3">'.$img.'<b>'.$identity->username.'</b>'.Html::a('Выход', ['site/logout']);
			$html .= '</div></div></div>';
		}

		return $html;
	}

}