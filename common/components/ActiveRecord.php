<?php
/**
 * @author: Nox
 */

namespace common\components;

class ActiveRecord extends \yii\db\ActiveRecord{

    /**
     * Получить массив собранных данных
     * @param array $condition
     * @return array
     */
    public static function listData($condition = []){
        $query = self::find();
        if($condition){
            $query->where($condition[0], !empty($condition[1]) ? $condition[1] : []);
        }
        return ArrayHelper::map($query->all(), 'id', 'title');
    }

}