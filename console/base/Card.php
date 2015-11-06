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

    private static $attributes = ['id', 'title', 'description', 'cost', 'img', 'type'];

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
}