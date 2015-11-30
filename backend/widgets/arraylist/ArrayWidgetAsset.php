<?php
namespace backend\widgets\arraylist;
use yii\web\AssetBundle;

/**
 * @author: Nox
 */
class ArrayWidgetAsset extends AssetBundle
{
	public $sourcePath = '@app/widgets/arraylist/assets';

	public $css = [

	];

	public $js = [
		'array.js'
	];

	public $depends = [
		// 'yii\web\JqueryAsset',
	];
}


