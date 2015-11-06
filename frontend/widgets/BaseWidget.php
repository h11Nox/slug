<?php
namespace frontend\widgets;

use Yii;
use yii\bootstrap\Widget;

class BaseWidget extends Widget{

	/**
	 * @param string $view
	 * @param array $params
	 * @return string
	 */
	public function render($view, $params = []){
		$view = self::className().DIRECTORY_SEPARATOR.$view;
		return parent::render($view, $params = []);
	}
}