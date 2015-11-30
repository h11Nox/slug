<?php
namespace backend\widgets\tinymce;
use yii\web\AssetBundle;

/**
 * @author: Nox
 */
class TinyMCEAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/tinymce/assets';

    public $css = [

    ];

    public $js = [
        'tinymce.min.js'
        // 'jquery.tinymce.min.js',
        // 'plugins/responsivefilemanager/plugin.min.js',
    ];

    public $depends = [
        // 'yii\web\JqueryAsset',
    ];
}


