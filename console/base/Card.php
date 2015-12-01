<?php
namespace console\base;

/**
 * Class Card
 * @package console\base
 */
class Card {

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
	 * Getting unknown card property
	 * @param $attribute
	 * @return bool
	 */
	public function __get($attribute) {
		if (in_array($attribute, $this->params)) {
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
		if (in_array($attribute, $this->params)) {
			$this->{$attribute} = $value;
		} else {
			trigger_error('Card property does not exist');
		}
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

		return $data;
	}

	/**
	 * Get card params
	 * @return array
	 */
	public function getParams() {
		$data = [];
		foreach ($this->params as $param) {
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
	 * Get card html
	 * @return string
	 */
	public function getHtml() {}
}