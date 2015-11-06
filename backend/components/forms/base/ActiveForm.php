<?php

namespace backend\components\forms\base;

/**
 * Робота з формами
 * Class ActiveForm
 * @author: Nox
 * @package backend\components\forms\base
 */
class ActiveForm extends \yii\bootstrap\ActiveForm{

    /**
     * @var string the default field class name when calling [[field()]] to create a new field.
     * @see fieldConfig
     */
    public $fieldClass = 'backend\components\forms\base\ActiveField';

}