<?php

use yii\helpers\Html;
use backend\widgets\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Deck Cards';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="deck-card-index">

    <div class="box c-box">
        <div class="box-header">
            <h3 class="box-title"><?php echo Html::encode($this->title); ?></h3>
            <div class="box-tools">
                <?= Html::a('+', ['create'], ['class' => 'btn btn-success']); ?>
            </div>
        </div>
        <div class="box-body no-padding">
            <hr />
        
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                // ['class' => 'yii\grid\SerialColumn'],
                  // 'id',
                  'title',
                  // 'description',
                  [
                      'attribute' => 'img',
                      'format' => 'html',
                      'value' => function($data){
                          return $data->img->isExists() ? Html::img($data->img->getThumb('80x120')) : '-';
                      }
                  ],
                  [
                      'attribute' => 'type',
                      'value' => function($data){
                          return $data->getTypeTitle();
                      }
                  ],
                  // 'data:ntext',
                  // 'deck_id',
                  // 'type',

                  ['class' => 'backend\widgets\grid\ActionColumn'],
            ],
        ]); ?>

        </div>
    </div>
</div>
