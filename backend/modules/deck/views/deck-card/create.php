<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\DeckCard */

$this->title = 'Создать карту';
$this->params['breadcrumbs'][] = ['label' => 'Карты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="deck-card-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
