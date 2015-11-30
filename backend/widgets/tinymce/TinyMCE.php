<?php

namespace backend\widgets\tinymce;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

/**
* Виджет ввода текста
* @author: Nox
*/
class TinyMCE extends InputWidget{

    public $language = 'ru';

    public $fileManager = true;

    public $clientOptions = [
        'theme' => "modern",
        // 'width' => 300,
        'height' => 300,
        'plugins' => [
            "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern"
        ],
        'toolbar1' => "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect table | hr removeformat | subscript superscript | charmap | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template restoredraft",
        'toolbar2' => "searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink image media code | forecolor backcolor pagebreak | responsivefilemanager",

        'menubar' => false,
        'toolbar_items_size' => 'small',
        'style_formats'=> [
            ['title' => 'Bold text', 'inline' => 'b'],
            ['title' => 'Red text', 'inline' => 'span', 'styles' => ['color' => '#ff0000']],
            ['title' => 'Red header', 'block' => 'h1', 'styles' => ['color' => '#ff0000']],
            ['title' => 'Example 1', 'inline' => 'span', 'classes' => 'example1'],
            ['title' => 'Example 2', 'inline' => 'span', 'classes' => 'example2'],
            ['title' => 'Table styles'],
            ['title' => 'Table row 1', 'selector' => 'tr', 'classes' => 'tablerow1']
        ],
        'templates' => [
                ['title' => 'Test template 1', 'content' => 'Test 1'],
                ['title' => 'Test template 2', 'content' => 'Test 2']
        ]
    ];

    /**
     * @inheritdoc
     */
    public function run()
    {
        $input = $this->hasModel()
            ? Html::activeTextInput($this->model, $this->attribute, $this->options)
            : Html::textInput($this->name, $this->value, $this->options);

        echo $input;

        $this->registerClientScript();
    }

    /**
     * Регистрировать скрипты
     */
    protected function registerClientScript(){
        $view = $this->getView();
        $bundle = TinyMCEAsset::register($view);
        if($this->language !== null){
            $langFile = "langs/{$this->language}.js";
            $bundle->js[] = $langFile;
            $this->clientOptions['language_url'] = $bundle->baseUrl . "/{$langFile}";
        }

        if($this->fileManager){
            /*$this->clientOptions['relative_urls'] = true;
            $this->clientOptions['remove_script_host'] = true;
            $this->clientOptions['document_base_url'] = 'http://www.tinymce.com/tryit/';*/
            $this->clientOptions['external_filemanager_path'] = '/plugins/fm/filemanager/';
            $this->clientOptions['filemanager_title'] = 'Файловий менеджер';
            $this->clientOptions['external_plugins'] = [
                "filemanager" => "/plugins/fm/filemanager/plugin.min.js"
            ];
        }


        $id = $this->options['id'];

        $this->clientOptions['selector'] = '#'.$id;
        $options = !empty($this->clientOptions) ? Json::encode($this->clientOptions) : '';
        $js[] = "tinymce.init({$options});";

        $view->registerJs(implode("\n", $js));
    }

}