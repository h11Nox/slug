<?php

namespace backend\components\forms\widgets;

use common\behaviors\models\AttachFileModel;
use yii\bootstrap\Widget;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * Виджет выбора изображения
 * Class ImageInput
 * @package backend\components\forms\widgets
 */
class ImageInput extends InputWidget{

    /**
     * Запустить
     * процесс
     * @return string
     */
    public function run(){
        $blockId = Html::getInputId($this->model, $this->attribute).'-block';
        $content = Html::beginTag('div', ['class'=>'c-file-block', 'id'=>$blockId]);
        $content .= Html::activeHiddenInput($this->model, $this->attribute);

        $fileInput = Html::activeInput('file', $this->model, $this->attribute, ['class'=>'styled']);
        $content .= '<div class="box box-success img-preview">';
        if($this->model->{$this->attribute} instanceof AttachFileModel && $this->model->{$this->attribute}->isExists()){
            $size = $this->model->{$this->attribute}->getSize();

            $buttonId = Html::getInputId($this->model, $this->attribute);

            $content .= '<div class="img">';
            $content .= '<img src="'.$this->model->{$this->attribute}->getThumb('220x160', 2).'" /></div>';
            $content .= '<div class="controls-blk">'.$fileInput;
            $content .= '<a href="'.$this->model->{$this->attribute}->getDownloadLink().'" class="btn btn-success btn-small control-btn">Скачать '.$size.'</a>';
            $content .= '<div class="delete-row"><a class="btn btn-danger btn-small delete-file control-btn" href="javascript:void(0);" id="'.$buttonId.'">Удалить</a></div></div>';

            $js = "
                $(document).ready(function(){
                    var \$block = $('#{$blockId}');
                    var \$img = \$block.find('img');
                    \$block.find('.delete-file').click(function(){
                        \$block.addClass('deleted').find('input[type=\"hidden\"]').val('remove');
                    });
                });
            ";
            $this->getView()->registerJs($js);
        }
        else{
            $content .= '<div class="controls-blk">'.$fileInput.'</div>';
        }
        $content .= '</div></div>';

        return $content;
    }

}