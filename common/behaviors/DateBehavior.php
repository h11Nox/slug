<?php

namespace common\behaviors;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Поведение для сохранения даты
 * Class FileBehavior
 * @author: Nox
 * @package common\behaviors
 */
class DateBehavior extends Behavior{

    /**
     * Поле
     * @var string
     */
    public $field;

    /**
     * События
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate'
        ];
    }

    /**
     * Перед валидацией
     */
    public function beforeValidate(){
        $val = $this->owner->getAttribute($this->field);
        if ((int)$val!=$val) {
            $this->owner->setAttribute($this->field, strtotime($val));
        }
    }

}