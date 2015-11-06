<?php

namespace common\behaviors\models;

use Yii;
use yii\base\Component;
use yii\helpers\Url;

/**
 * Файл
 * Class AttachFileModel
 * @author: Nox
 * @package common\behaviors\models
 */
class AttachFileModel extends Component{

    /**
     * Имя файла
     * @var string
     */
    public $name;

    /**
     * Базовый путь
     * @var string
     */
    public $basePath;

    /**
     * Путь
     * @var string
     */
    public $path;

    /**
     * При попытке вывести обьект
     * @return string
     */
    public function __toString(){
        return $this->name;
    }

    /**
     * Получить путь
     * @return string
     */
    public function getPath(){
        return $this->basePath.$this->path.DIRECTORY_SEPARATOR.$this->name;
    }

    /**
     * Получить путь к папке
     * @param bool $relative
     * @return string
     */
    public function getPathFolder($relative = false){
        return $relative ? $this->path : $this->basePath.$this->path;
    }

    /**
     * Получить относительный путь
     * @return string
     */
    public function getRelativePath(){
        return substr($this->getPath(), strlen(Yii::getAlias('@app')));
    }

    /**
     * Получить размер файла
     * @return string
     */
    public function getSize(){
        return Yii::$app->formatter->asShortSize(filesize($this->getPath()));
    }

    /**
     * Существует ли файл
     * @return bool
     */
    public function isExists(){
        return is_file($this->getPath());
    }

    /**
     * Получить ссылку на скачивание
     * @return string
     */
    public function getDownloadLink(){
        return Url::toRoute(['/site/download', 'file'=>$this->getRelativePath()]);
    }
}