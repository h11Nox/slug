<?php

namespace common\models;

use common\behaviors\ImageBehavior;
use Yii;

/**
 * This is the model class for table "cms_deck_card".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $img
 * @property string $data
 * @property integer $deck_id
 * @property integer $type
 *
 * @property Deck $deck
 */
class DeckCard extends \yii\db\ActiveRecord
{
    const TYPE_WARRIOR = 1;
    const TYPE_DAMAGE = 2;
    const TYPE_BOOST = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cms_deck_card';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['data'], 'string'],
            [['deck_id', 'type'], 'integer'],
            [['title', 'description'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            ImageBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'description' => 'Описание',
            'img' => 'Изображение',
            'data' => 'Данные',
            'deck_id' => 'Колода',
            'type' => 'Тип',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeck()
    {
        return $this->hasOne(Deck::className(), ['id' => 'deck_id']);
    }

    public static function getTypes(){
        return [
            self::TYPE_WARRIOR => 'Воин',
            self::TYPE_DAMAGE => 'Урон',
            self::TYPE_BOOST => 'Усиление'
        ];
    }

    public function getTypeTitle(){
        $types = self::getTypes();

        return $types[$this->type];
    }
}
