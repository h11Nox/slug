<?php
/**
 * Fight field
 */

$this->title = Yii::$app->name.'- Бой #'.$model->id;

echo \frontend\widgets\FightWidget::widget([
    'fight' => $model
]);

