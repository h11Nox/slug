<?php
namespace console\base;

/**
 * Class Card
 * @package console\base
 */
class Card extends \yii\base\Component  {

	const USAGE_IMMEDIATELY = 1;
	const USAGE_UNIT_OR_HERO = 2;

	public $id;
	public $title;
	public $description;
	public $cost;
	public $img;
	public $type;

	protected $index;

	private static $attributes = ['id', 'title', 'description', 'cost', 'img', 'type'];

	/**
	 * Params
	 * @var array
	 */
	protected $params = [];

	/**
	 * Constructor
	 * @param array $config
	 */
	public function __construct($config = []) {
		$this->init();
	}

	/**
	 * Getting unknown card property
	 * @param $attribute
	 * @return bool
	 */
	public function __get($attribute) {
		if (in_array($attribute, $this->getParamsList())) {
			return $this->{$attribute};
		} else {
			return !trigger_error('Card property does not exist');
		}
	}

	/**
	 * Setting unknown card property
	 * @param $attribute
	 * @param $value
	 */
	public function __set($attribute, $value) {
		if (in_array($attribute, $this->getParamsList())) {
			$this->{$attribute} = $value;
		} else {
			trigger_error("Card property '{$attribute}' does not exist");
		}
	}

	/**
	 * Initialization function
	 */
	public function init() {
		// do init
	}

	/**
	 * Do something after turn
	 * @note Will be called externally
	 * Allows to do smth. after user ended his turn
	 */
	public function afterTurn() {}

	/**
	 * Return list of additional params
	 *
	 * @return array
	 */
	protected function getAdditionalParams() {
		return [];
	}

	/**
	 * Set card attributes
	 * @param array $data
	 */
	public function setAttributes(array $data) {
		foreach ($data as $k=>$v) {
			if (in_array($k, self::$attributes)) {
				$this->{$k} = $v;
			}
		}
	}

	/**
	 * Get Attributes
	 * @return array
	 */
	public function getAttributes(){
		$data = [];
		foreach (self::$attributes as $attribute) {
			$data[$attribute] = $this->{$attribute};
		}
		$data['usage'] = (int)$this->getUsage();

		return $data;
	}

	/**
	 * Get params list
	 *
	 * @return array
	 */
	protected function getParamsList() {
		return array_merge($this->params, $this->getAdditionalParams());
	}

	/**
	 * Get card params
	 * @return array
	 */
	public function getParams() {
		$data = [];
		foreach ($this->getParamsList() as $param) {
			$data[$param] = $this->{$param};
		}

		return $data;
	}

	/**
	 * Set card params
	 * @param $data
	 */
	public function setParams($data) {
		if (!empty($data) && is_array($data)) {
			foreach ($data as $item) {
				$this->{$item['key']} = $item['value'];
			}
		}
	}

	/**
	 * Set card index
	 * @param $index
	 * @return $this
	 */
	public function setIndex($index) {
		$this->index = $index;
		return $this;
	}

	/**
	 * Get index
	 * @return mixed
	 */
	public function getIndex() {
		return $this->index;
	}

	/**
	 * Get card response
	 *
	 * @return array
	 */
	public function getResponse() {
		return [
			'card' => array_merge($this->getAttributes(), [
				'id' => $this->getIndex(),
				'text' => $this->getHtml()
			]),
			'data' => $this->getParams(),
		];
	}

	/**
	 * Card usage
	 * @return int
	 */
	protected function getUsage() {
		return static::USAGE_IMMEDIATELY;
	}

	/**
	 * Get card html
	 * @return string
	 */
	public function getHtml() {}

	/**
	 * Use card
	 */
	public function useCard() {}
}