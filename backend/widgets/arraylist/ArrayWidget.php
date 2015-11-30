<?php
namespace backend\widgets\arraylist;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * Array widget
 * Class ArrayWidget
 * @package backend\widgets
 */
class ArrayWidget extends InputWidget {

	public $clientOptions = [];

	/**
	 * Run widget
	 * @return string
	 */
	public function run() {
		$content = '<div>';
		$id = Html::getInputId($this->model, $this->attribute);
		$content .= Html::beginTag('div', [
			'id' => 'block-'.$id
		]);

		$content .= '</div>';
		$content .= '<a href="javascript:void(0);">Добавить</a>';
		$content .= '</div>';

		$this->registerClientScript();

		return $content;
	}

	/**
	 * Register assets
	 */
	protected function registerClientScript() {
		$id = Html::getInputId($this->model, $this->attribute);
		$view = $this->getView();
		ArrayWidgetAsset::register($view);

		$r = new \ReflectionClass($this->model);
		$params = Json::encode([
			'model' => $r->getShortName(),
			'attribute' => $this->attribute,
			'multiple' => true,
			'keys' => [ 'key', 'value' ],
			'data' => $this->model->{$this->attribute} ? $this->model->{$this->attribute} : []
		]);
		$js[] = "
		var params = {
			block : $('#block-{$id}')
		};
		$.extend(params, {$params});
		new ArrayEditor(params);";

		$view->registerJs(implode("\n", $js), View::POS_READY);

	}
}