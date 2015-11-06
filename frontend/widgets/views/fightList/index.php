<?php
/**
 * Fights
 */
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;

?>
<div id="main-page">
    <h2 class="text-center">Начните играть!</h2>

    <div class="right-row">
        <a class="btn btn-info btn-small" id="create-game">Создать</a>
        <a class="btn btn-success btn-small" id="refresh-game">Обновить</a>
    </div>

    <div style="padding-top: 15px;" id="games-grid">
        <?php \yii\widgets\Pjax::begin(); ?>
        <?php echo GridView::widget([
            'id' => 'fightslist',
            'dataProvider' => $items,
            'emptyText' => 'Пока нет активных битв, но Вы можете создать, воспользовавшись кнопкой выше.',
            'layout' => '{items}{pager}',
            'columns' => [
                [
                    'header' => 'Противник',
                    'format' => 'raw',
                    'value' => function($data){
                        $html = '';
                        $user = $data->ownerUser;
                        if($user){
                            $html .= '<div class="img-holder">'.Html::img($user->img->getThumb('50x50'), [
                                'alt' => $user->username,
                                'title' => $user->username
                            ]).'</div>';
                            $html .= '<div class="info-holder"><div class="name">';
                            $html .= $user->username.'</div><div class="rate">';
                            $html .= 'Рейтинг: '.$user->rating;
                            $html .= '</div></div>';
                        }
                        return $html;
                    }
                ],
                [
                    'header' => 'Колода',
                    'contentOptions' => ['class' => 'deck-col'],
                    'headerOptions' => ['class' => 'deck-col'],
                    'value' => function($data){
                        $html = '';
                        $user = $data->owner;
                        if($user){
                            $html .= $user->deck->title;
                        }
                        return $html;
                    }
                ],
                [
                    'attribute' => 'created_at',
                    'contentOptions' => ['class' => 'date-col'],
                    'headerOptions' => ['class' => 'date-col'],
                    'value' => function($data){
                        return date('d.m.Y H:i:s', $data->created_at);
                    }
                ],
                [
                    'header' => '',
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'action-col'],
                    'headerOptions' => ['class' => 'action-col'],
                    'value' => function($data){
                        return '<a href="#" class="btn btn-info btn-play btn-small" data-id="'.$data->id.'">Играть</a>';
                    }
                ],
            ],
        ]); ?>
        <?php \yii\widgets\Pjax::end(); ?>
    </div>

    <div class="modal fade" id="create-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Создать игру</h4>
                </div>
                <div class="modal-body">
                    <?php $form = ActiveForm::begin(); ?>
                        <div class="errors-holder"></div>
                        <div class="success-holder">Игра успешно создана</div>

                        <div class="f-row">
                            <?= $form->field($model, 'deck_id')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Deck::find()->all(), 'id', 'title')) ?>
                        </div>
                        <div class="f-row submit-row">
                            <?php echo Html::a(Yii::t('app', 'Создать'), '#', ['class' => 'btn btn-primary c-submit']) ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>