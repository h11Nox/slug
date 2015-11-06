<?php
/**
 * @author: Nox
 */

namespace backend\components;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;

class Controller extends \yii\web\Controller{

    /**
     * @param \yii\base\Action $action
     * @return bool|void
     */
    public function beforeAction($action){
        if(Yii::$app->getUser()->isGuest){ // !Yii::$app->request->pathInfo &&
            return $this->redirect(Url::toRoute('/default/login'));
        }

        return parent::beforeAction($action);
    }

    /**
     * Редирект после сохранения
     * @param ActiveRecord $model
     */
    protected function afterSaveRedirect(ActiveRecord $model){
        /** @var TYPE_NAME $this */
        if(isset($_POST['apply'])){
            return $this->redirect(['update', 'id' => $model->primaryKey]);
        }
        else{
            return $this->redirect(['index', 'id' => $model->primaryKey]);
        }
    }

}