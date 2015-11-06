<?php

namespace common\behaviors;
use yii\validators\Validator;

/**
 * Поведение для сохранения изображений
 * Class FileBehavior
 * @author: Nox
 * @package common\behaviors
 */
class ImageBehavior extends FileBehavior{

    /**
     * Поля
     * @var array
     */
    public $fields = array('img');

    /**
     * Типы файлов, которые можно загружать (нужно для валидации)
     * @var string
     */
    public $fileTypes='jpg,jpeg,gif,png';

    /**
     * @var string
     */
    protected $_modelNamespace = '\common\behaviors\models\AttachImageModel';

    /**
     * Добавить валидатор
     * @param $field
     */
    protected function addValidator($field){
        $this->owner->validators[] = Validator::createValidator('image', $this->owner, $field,
            [
                'skipOnEmpty'=>!in_array($field, $this->required) || $this->owner->{$field}->isExists(),
                'extensions'=>$this->fileTypes,
                'enableClientValidation'=>false,
                'maxSize' => 7200000
            ]);
    }
}