<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cms_user_profile".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $service
 * @property integer $service_id
 */
class UserProfile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cms_user_profile';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'service', 'service_id'], 'required'],
            [['id', 'user_id', 'service_id'], 'integer'],
            [['service'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'service' => 'Сервис',
            'service_id' => 'ID сервиса',
        ];
    }
}
