<?php

use yii\helpers\Html;
use backend\components\forms\base\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DeckCard */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="deck-card-form">

    <?php $form = ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'title')->textInput() ?>

            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'deck_id')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Deck::find()->all(), 'id', 'title')); ?>

            <?= $form->field($model, 'img')->imageInput(); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'type')->dropDownList(\common\models\DeckCard::getTypes()) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'data')->arrayInput() ?>
        </div>
    </div>

    <div class="form-group submit-row">
        <?= Html::submitButton('Применить', ['class' =>'btn btn-warning', 'name'=>'apply']) ?>
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'name'=>'save']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
