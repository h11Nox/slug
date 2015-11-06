<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use backend\components\forms\base\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-md-6">
<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {
        echo "    <?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
    }
} ?>
        </div>
    </div>

    <div class="form-group">
        <?= "<?= " ?>Html::submitButton('Применить', ['class' =>'btn btn-warning', 'name'=>'apply']); ?>
        <?= "<?= " ?>Html::submitButton($model->isNewRecord ? <?= $generator->generateString('Создать') ?> : <?= $generator->generateString('Сохранить') ?>, ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'name'=>'save']) ?>
    </div>

    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
