<?php

namespace backend\modules\deck\controllers;

use Yii;
use common\models\DeckCard;
use yii\data\ActiveDataProvider;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DeckCardController реализовывает базовые операции для DeckCard модели.
 */
class DeckCardController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Cписок всех записей DeckCard 
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => DeckCard::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Вывод модели DeckCard
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Создает новый DeckCard 
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DeckCard();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->afterSaveRedirect($model);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Редактирование  DeckCard модели.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->afterSaveRedirect($model);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Удаление записи DeckCard
     * Если успешно будет переадресация на 'index' страницу.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Поиск DeckCard модели по первичному ключу
     * Если модель не будет найдена - то 404 ошибка
     * @param integer $id
     * @return DeckCard выбранной модели
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = DeckCard::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
