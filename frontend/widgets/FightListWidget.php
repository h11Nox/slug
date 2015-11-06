<?php
namespace frontend\widgets;
use common\models\Fight;
use common\models\FightUser;
use frontend\models\FightForm;
use HttpException;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

/**
 * FightList
 */
class FightListWidget extends ActionWidget{

	public $action = 'index';

	/**
	 * @return string
	 */
	public function actionIndex(){
		$query = Fight::find();
		$fu = FightUser::tableName();
		$query->join('LEFT JOIN', $fu, $fu.'.fight_id = '.Fight::tableName().'.id AND '.$fu.'.user_id = '.\Yii::$app->user->getIdentity()->cid);
		$query->where($fu.'.id IS NULL');
		$query->orderBy('created_at DESC');
		$items = new ActiveDataProvider([
			'query' => $query,
			'sort' => false
		]);
		return $this->render('index', ['items' => $items, 'model' => new FightForm()]);
	}

	/**
	 * @return string
	 */
	public function actionCreate(){
		$data = $this->setAjaxData(new FightForm(), 'create');
		$response = $data[1];

		$response['redirect'] = $data[1]['status'] == 1 ? $data[0]->getFight()->getUrl() : '';

		return json_encode($response);
	}

	public function actionStart(){
		$response = [
			'status' => 0,
			'message' => '',
			'redirect' => ''
		];
		$fight = Fight::findOne(Yii::$app->request->post('id'));
		if(!$fight){
			throw new HttpException(404);
		}
		if(Fight::STATUS_NEW != $fight->status){
			$response['message'] = 'Игра уже началась';
		}
		else{
			if($fight->connect()){
				$response['status'] = 1;
				$response['redirect'] = $fight->getUrl();
			}
		}

		return json_encode($response);
	}
}