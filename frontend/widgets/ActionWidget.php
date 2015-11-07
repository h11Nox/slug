<?php

namespace frontend\widgets;
use yii\base\Model;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Виджет с екшинами
 * @author: Nox
 */
class ActionWidget extends Widget {

	/**
	 * Действие
	 * @var string
	 */
	public $action = 'index';

	/**
	 * Запуск виджета
	 */
	public function run() {
		$method = 'action'.$this->action;
		if (method_exists($this, $method)) {
			parent::run();
			return $this->{$method}();
		}
	}

	/**
	 * @inheritdoc
	 */
	public function render($view, $params = []) {
		return parent::render($this->getViewsPath().'/'.$view, $params);
	}

	/**
	 * @return string
	 */
	protected function getViewsPath() {
		$parts = explode('\\', $this->className());
		$widget = array_pop($parts);

		$widgetName = substr($widget, 0, strlen($widget) - 6);
		$widgetName = strtolower(substr($widgetName, 0, 1)).substr($widgetName, 1);
		return $widgetName;
	}

	/**
	 * Validate Form action
	 * @param Model $model
	 * @param string $action
	 * @return array
	 */
	public function setAjaxData(Model $model, $action = 'save', $json = false) {
		$response = [
			'status' => 0,
			'errors' => '',
			'text' => ''
		];
		if (\Yii::$app->request->isAjax && $model->load(\Yii::$app->request->post())) {
			if ($model->{$action}()) {
				$response['status'] = 1;
			}
			else{
				$response['errors'] = Html::errorSummary($model);
			}
		}

		return $json ? json_encode($response) : [$model, $response];
	}
}


