<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cms_fight_user".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $deck_id
 * @property integer $is_owner
 * @property integer $fight_id
 * @property integer $cards_list
 * @property integer $cards
 *
 * @property Deck $deck
 * @property User $user
 */
class FightUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cms_fight_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'deck_id', 'is_owner', 'fight_id'], 'required'],
            [['user_id', 'deck_id', 'is_owner'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'deck_id' => 'Deck ID',
            'is_owner' => 'Создатель',
            'fight_id' => 'Номер боя'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeck()
    {
        return $this->hasOne(Deck::className(), ['id' => 'deck_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Get card list
     * Shuffle and get new if list is empty
     * @return int
     */
    public function getCardList() {
        if (empty($this->cards_list)) {
            $this->shuffle();
            $this->refresh();
        }

        return $this->cards_list;
    }

    /**
     * Shuffle cards
     */
    public function shuffle() {
        $items = DeckCard::find()->select('id')->where('deck_id = :id', [':id' => $this->deck->id])
            ->all();
        $ids = [];
        foreach($items as $item){
            $ids[] = $item->id;
        }
        shuffle($ids);

        $this->updateAttributes(['cards_list' => implode(',', $ids)]);
    }

    /**
     * Get Cards
     * @param int $number
     * @return array
     */
    public function getCards($number = 1, $info = true){
        $cards = explode(',', $this->cards_list);
        $offset = 0; // (int)$this->cards;
        // $this->updateAttributes(['cards' => $offset + $number]);
        $result = array_slice($cards, $offset, $number);
        if($info){
            $data = [];
            $items = DeckCard::find()->where(['in', 'id', $result])->all();

            foreach($items as $item){
                $i = $item->getAttributes([
                    'id',
                    'title',
                    'description',
                    'cost',
                    'type'
                ]);
                $i['img'] = $item->img->getThumb('60x80');

                $data[] = $i;
            }

            $result = $data;
            unset($data);
        }

        return $result;
    }
}
