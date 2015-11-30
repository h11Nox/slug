<?php

namespace common\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "cms_fight".
 *
 * @property integer $id
 * @property integer $owner_id
 * @property integer $user_id
 * @property integer $move
 * @property integer $status
 * @property integer $created_at
 */
class Fight extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 0;
    const STATUS_CONNECTED = 1;
    const STATUS_STARTED = 2;
    const STATUS_DONE = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cms_fight';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['move', 'status', 'created_at'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'owner_id' => 'Owner ID',
            'user_id' => 'Пользователь',
            'move' => 'Move',
            'status' => 'Статус',
            'created_at' => 'Дата',
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert){
        if($insert){
            $this->created_at = time();
            $this->status = self::STATUS_NEW;
            $this->move = 0;
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner(){
        $query = $this->hasOne(FightUser::className(), ['fight_id' => 'id']);
        $query->onCondition('is_owner = 1');
        return $query;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser(){
        $query = $this->hasOne(FightUser::className(), ['fight_id' => 'id']);
        $query->onCondition('is_owner = 0');
        return $query;
    }

    /**
     * @return $this
     */
    public function getOwnerUser(){
        return $this->hasOne(User::className(), ['id' => 'user_id'])
            ->viaTable(FightUser::tableName(), ['fight_id' => 'id']);
    }

    /**
     * Check user
     * @param null $user
     * @return bool
     */
    public function check($user = null){
        if($user === null){
            $user = (int)Yii::$app->user->getIdentity()->cid;
        }
        return $user == $this->owner->user_id || ($this->user && $user == $this->user->user_id);
    }

    /**
     * Connect
     * @return bool
     */
    public function connect(){
        $this->updateAttributes(['status' => self::STATUS_CONNECTED]);

        $fightUser = new FightUser([
            'fight_id' => $this->id,
            'deck_id' => $this->owner->deck_id,
            'is_owner' => 0,
            'user_id' => Yii::$app->user->getIdentity()->cid
        ]);

        return $fightUser->save();
    }

    /**
     * Get Url
     * @return string
     */
    public function getUrl(){
        return Url::toRoute(['site/fight', 'id' => $this->id]);
    }

    /**
     * Check if fight is new
     * @return bool
     */
    public function isNew(){
        return self::STATUS_CONNECTED == $this->status;
    }

    /**
     * Start fight
     */
    public function start(){
        $this->shuffle();
        $this->updateAttributes(['status' => self::STATUS_STARTED]);
    }

    /**
     * Shuffle all
     */
    public function shuffle(){
        $this->owner->shuffle();
        $this->user->shuffle();
    }
}
