<?php

namespace frontend\models;

use common\models\Fight;
use common\models\FightUser;
use Ratchet\Wamp\Exception;
use Yii;
use yii\base\Model;
use yii\db\Transaction;

/**
 * FightForm
 */
class FightForm extends Model
{
    public $deck_id;

    protected $_fight;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['deck_id'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'deck_id' => 'Колода'
        ];
    }

    /**
     * Creating fight
     * @return bool
     */
    public function create(){
        if(!$this->validate()){
            return false;
        }

        $transaction = Yii::$app->getDb()->beginTransaction();
        $saved = false;
        try {
            $fight = new Fight();
            if($fight->save()){
                $fightOwner = new FightUser([
                    'user_id' => Yii::$app->getUser()->getIdentity()->cid,
                    'deck_id' => $this->deck_id,
                    'is_owner' => 1,
                    'fight_id' => $fight->id
                ]);
                if($fightOwner->save()){
                    $saved = true;
                    $this->_fight = $fight;
                }
            }
        } catch (Exception $e) {
            $transaction->rollback();
        }

        if($saved){
            $transaction->commit();
        }
        else{
            $transaction->rollback();
        }

        return $saved;
    }

    public function getFight(){
        return $this->_fight;
    }
}
