<?php
namespace common\base;
use yii\base\Component;

/**
 * Game
 */
class Game extends Component{

	protected $_settings;
	protected $_player1;
	protected $_player2;

	public function __construct(){
		$this->initialize();
	}

	protected function initialize(){
		$this->_settings = new GameSettings();
	}

	public function getSettings(){
		return $this->_settings;
	}

	public function setPlayer1($player){
		$this->_player1 = $player;
	}

	public function getPlayer1(){
		return $this->_player1;
	}

	public function setPlayer2($player){
		$this->_player2 = $player;
	}

	public function getPlayer2(){
		return $this->_player2;
	}
}