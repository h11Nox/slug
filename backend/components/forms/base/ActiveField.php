<?php

namespace backend\components\forms\base;

use backend\widgets\tinymce\TinyMCE;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;

/**
 * Поле форми
 * Class ActiveField
 * @author: Nox
 * @package backend\components\forms\base
 */
class ActiveField extends \yii\bootstrap\ActiveField{

    /**
     * @inheritdoc
     */
    public function textInput($options = [])
    {
        $options = array_merge($this->inputOptions, $options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::activeTextInput($this->model, $this->attribute, $options);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function fileInput($options = []){
        return $this->widget(\backend\components\forms\widgets\FileInput::className());
    }

    /**
     * @inheritdoc
     */
    public function imageInput($options = []){
        return $this->widget(\backend\components\forms\widgets\ImageInput::className());
    }

    /**
     * Поле выбора даты
     * @return string
     * @throws \Exception
     */
    public function date(){
        if($this->model->getIsNewRecord()){
            $this->model->{$this->attribute} = time();
        }
        $this->model->{$this->attribute} = date('d.m.Y', $this->model->{$this->attribute});
        return $this->widget(DatePicker::className(), [
            'model' => $this->model,
            'attribute' => $this->attribute,
            //'language' => 'ru',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'dd.mm.yyyy'
            ]
        ]);
    }

    /**
     * Поле выбора даты и времени
     */
    public function datetime(){
        return $this->date();
    }

    /**
     * Виджет ввода текста
     */
    public function wysiwyg(){
        return $this->widget(TinyMCE::className(), [
            'model' => $this->model,
            'attribute' => $this->attribute
        ]);
    }
}