<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DeckCard */

$this->title = 'Редактировать карту: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Карты', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="deck-card-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
