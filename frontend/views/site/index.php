<?php

$this->title = Yii::$app->name.' - карточная онлайн игра';

if(Yii::$app->user->isGuest){
    if (Yii::$app->getSession()->hasFlash('error')) {
        echo '<div class="alert alert-danger">'.Yii::$app->getSession()->getFlash('error').'</div>';
    }
    ?>

    <div class="text-center">
        <p>
            Для начала авторизируйтесь через одну из соц. сетей
        </p>
        <?php echo \nodge\eauth\Widget::widget(['action' => 'site/login']); ?>
    </div>
<?php } else {
    echo \frontend\widgets\FightListWidget::widget([]);
} ?>
