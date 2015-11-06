<?php

namespace backend\components\forms\widgets;

use common\behaviors\models\AttachFileModel;
use yii\bootstrap\Widget;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * Виджет выбора файла
 * Class FileInput
 * @package backend\components\forms\widgets
 */
class FileInput extends InputWidget{

    /**
     * Запустить
     * процесс
     * @return string
     */
    public function run(){
        $content = '<div class="c-file-block">';
        $content .= Html::activeFileInput($this->model, $this->attribute, ['class'=>'styled']);

        if($this->model->{$this->attribute} instanceof AttachFileModel && $this->model->{$this->attribute}->isExists()){
            $size = $this->model->{$this->attribute}->getSize();
            $buttonId = Html::getInputId($this->model, $this->attribute).'-button';

            $content .= '<div class="img-preview file-block"><div class="c-info"><span>';
            $content .= '<a href="'.$this->model->{$this->attribute}->getDownloadLink().'" class="buttonS bGreen bWhite">Завантажити '.$size.'</a></span>';
            $content .= '<div class="delete-row"><a class="buttonS bRed nsubmit delete-file" href="javascript:void(0);" id="'.$buttonId.'">Видалити</a></div></div></div>';

            $this->getView()->registerJs("$('#{$buttonId}').click(function(){\$(this).closest('.c-file-block').find('input[type=\"hidden\"]').val('remove');})");
        }
        $content .= '</div>';

        return $content;
    }

}