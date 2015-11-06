<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cms_deck".
 *
 * @property integer $id
 * @property string $title
 *
 * @property FightUser[] $fightUsers
 */
class Deck extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cms_deck';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFightUsers()
    {
        return $this->hasMany(FightUser::className(), ['deck_id' => 'id']);
    }
}
