<?php

namespace common\behaviors;

use common\behaviors\models\AttachFileModel;
use Yii;
use yii\base\Behavior;
use yii\base\ErrorException;
use yii\db\ActiveRecord;
use yii\helpers\BaseFileHelper;
use yii\validators\Validator;
use yii\web\UploadedFile;

/**
 * Поведение для сохранения файлов
 * Class FileBehavior
 * @author: Nox
 * @package common\behaviors
 */
class FileBehavior extends Behavior{

    /**
     * Поля
     * @var array
     */
    public $fields = array('file');

    /**
     * Обязательные поля
     * @var array
     */
    public $required = array();

    /**
     * Путь до изображений
     * @var string
     */
    public $path;

    /**
     * Поля которые не валидировать
     * @var array
     */
    public $skipValidate = [];

    /**
     * Папка
     * @var string
     */
    public $folder;

    /**
     * Типы файлов, которые можно загружать (нужно для валидации)
     * @var string
     */
    public $fileTypes='jpg,jpeg,gif,png,doc,docx,xls,xlsx,csv,odt,pdf,zip,rar,xml,csv';

    /**
     * Размер длины папки
     * @var int
     */
    public $folderPow = 2;

    /**
     * Имя класса
     * @var string
     */
    protected $_class;

    /**
     * @var string
     */
    protected $_modelNamespace = '\common\behaviors\models\AttachFileModel';

    /**
     * Имена файлов
     * @var array
     */
    protected $_names = [];

    /**
     * Путь к вебфайлу
     * @var array
     */
    protected $_webFile = [];

    /**
     * События
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind'
        ];
    }

    /**
     * @inheritdoc
     * @param \yii\base\Component $owner
     */
    public function attach($owner)
    {
        // Если нет полей
        if(empty($this->fields)){
            throw new ErrorException('Необходимо указать поля для '.self::className());
        }

        $this->fields = $this->toArray($this->fields);
        $this->required = $this->toArray($this->required);

        parent::attach($owner);
    }

    /**
     * Выполняем ряд действий перед валидацией
     */
    public function beforeValidate(){
        $this->setAttach();
        foreach($this->fields as $field){
            $name = $this->owner->{$field}->name;
            $webFile = false;
            if(preg_match('!^http!', $name)){
                $parts = explode('/', $name);
                $name = array_pop($parts);
                if(strpos($name, '.') === false){
                    $name = uniqid('img').'.jpg';
                }
                unset($parts);
                $webFile = $this->owner->{$field}->name;
            }
            $this->_names[$field] = $name;

            $this->_webFile[$field] = $webFile;
        }

        // Добавляем нужные валидаторы
        foreach($this->fields as $field){
            if(!in_array($field, $this->skipValidate)){
                $this->addValidator($field);
            }
        }

        foreach($this->fields as $field){
            $this->owner->{$field} = UploadedFile::getInstance($this->owner, $field);
        }
    }

    /**
     * Перед сохранением
     */
    public function beforeSave(){
        $isRequest = Yii::$app->request instanceof \yii\web\Request;
        $data = $isRequest ? Yii::$app->request->post($this->getClassName()) : null;
        foreach($this->fields as $field) {
            $set = false;
            //Удаляем файл если поступила соответствующая команда
            if ($data) {
                if(!empty($data[$field]) && $data[$field] == 'remove'){
                    $this->deleteFile($field);
                    $this->owner->setAttribute($field, '');
                    $set = true;
                }
            }

            if(!$set){
                $attach = $this->getAttachModel($field, true);
                $this->owner->setAttribute($field, $attach->name);
            }
        }
    }

    /**
     * После сохранения
     */
    public function afterSave()
    {
        $this->path = null;
        //Сохраняем файл
        foreach($this->fields as $field){
            $isWeb = $this->_webFile[$field];
            $file = null;
            if($isWeb || $file = UploadedFile::getInstance($this->owner, $field)){
                //Директория для сохранения
                $path = $this->getPath($field);
                $files = BaseFileHelper::findFiles($path);
                foreach($files as $cFile){
                    @unlink($cFile);
                }

                $fileName = self::normalize($isWeb ? $this->_names[$field] : $file->name);
                $filePath = BaseFileHelper::normalizePath($path.'/'.$fileName);
                // d($filePath);

                if($this->_webFile[$field]){
                    $result = @copy($this->_webFile[$field], $filePath);
                    if(!$result){
                        $fileName = '';
                    }
                }
                else{
                    $file->saveAs($path.'/'.$fileName);
                    chmod($filePath, 0666);
                }

                $this->owner->updateAttributes(array($field => $fileName));
            }
        }

        $this->setAttach();
    }

    /**
     * Выполянем ряд действий после нахождения
     */
    public function afterFind(){
        $this->setAttach();
    }

    /**
     * Установить файлы
     */
    protected function setAttach(){
        foreach($this->fields as $field){
            if(!$this->owner->getAttribute($field) instanceof AttachFileModel){
                $model = $this->getAttachModel($field);

                // Устанавливаем обьект в значение атрибута
                $this->owner->setAttribute($field, $model);
            }
        }
    }

    /**
     * Получить модель аттача
     * @param $field
     * @param bool $names
     * @return AttachFileModel
     */
    protected function getAttachModel($field, $names = false){
        // Создаем обьект класса для работы с файлами\картинками
        $model = new $this->_modelNamespace;
        $model->path = $this->getPath($field, true);
        $model->basePath = Yii::getAlias($this->root);
        $model->name = $names ? $this->_names[$field] : (string)$this->owner->getAttribute($field);

        return $model;
    }

    /**
     * Приводим название файла в нормальный вид
     * @param $str
     * @return string
     */
    public static function normalize($str)
    {
        $tr = array(
            "а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d",
            "е"=>"e","ж"=>"j","з"=>"z","и"=>"i","й"=>"y",
            "к"=>"k","л"=>"l","м"=>"m","н"=>"n","о"=>"o",
            "п"=>"p","р"=>"r","с"=>"s","т"=>"t","у"=>"u",
            "ф"=>"f","х"=>"h","ц"=>"ts","ч"=>"ch","ш"=>"sh",
            "щ"=>"sch","ъ"=>"y","ы"=>"yi","ь"=>"","э"=>"e",
            "ю"=>"yu","я"=>"ya","і"=>"i","ї"=>"ji",
            " "=> "_", "/"=> "_"
        );
        $value = mb_strtolower($str, Yii::$app->charset);
        $value = strtr($value,$tr);

        $pattern = '/[^a-z0-9\-\.\+_()]/';
        $value = preg_replace($pattern, '-', $value);
        $value = preg_replace('/\-(\-)+/', '-', $value);
        $value = trim($value, '-');

        $extData = explode('.', $value);
        $ext = array_pop($extData);
        $value = $extData[0].'.'.$ext;

        return (string) $value;
    }

    /**
     * Получить путь
     * @param $field
     * @param bool $relative
     * @return bool|string
     * @throws \yii\base\Exception
     */
    protected function getPath($field, $relative = false){
        if($this->path === null){
            $this->path = DIRECTORY_SEPARATOR.$this->getFolder().DIRECTORY_SEPARATOR.$field.$this->getFolderPath();
            BaseFileHelper::createDirectory(\Yii::getAlias($this->root.$this->path));
        }

        return $relative ? $this->path : \Yii::getAlias($this->root.$this->path);
    }

    /**
     * Получить путь в папке
     * @return string
     */
    protected function getFolderPath(){
        $path = '';
        $cId = (string)$this->owner->getPrimaryKey();
        for($i=0; $i<ceil(strlen($cId)/$this->folderPow); $i++){
            $folder = (int)substr($cId, $i*$this->folderPow, $this->folderPow);
            $path .= DIRECTORY_SEPARATOR.$folder;
        }

        return $path;
    }

    /**
     * Получить имя класса (без пространства имен)
     * @return string
     */
    protected function getClassName(){
        if($this->_class === null){
            $className = $this->owner->className();
            $this->_class = substr($className, strrpos($className, '\\') + 1);
        }

        return $this->_class;
    }

    /**
     * Добавить валидатор
     * @param $field
     */
    protected function addValidator($field){
        $this->owner->validators[] = Validator::createValidator('file', $this->owner, $field,
            [
                'skipOnEmpty'=>!in_array($field, $this->required) || $this->owner->{$field}->isExists(),
                'extensions'=>$this->fileTypes,
                'enableClientValidation'=>false,
                'maxSize' => 7200000
            ]);
    }

    /**
     * Удалить файл
     * @param $field
     */
    protected function deleteFile($field){
        $path = $this->getPath($field).DIRECTORY_SEPARATOR.$this->owner->{$field};
        if(is_file($path)){
            @unlink($path);
        }
    }

    /**
     * Превратить в массив
     * @param $fields
     * @return array
     */
    protected function toArray($fields){
        if(!is_array($fields)){
            // Получаем поля
            $fields = is_string($fields) ? array_map(create_function('$x', 'return trim($x);'), explode(',', $fields)) : array();
        }

        return $fields;
    }

    /**
     * Название директории
     * @return string
     */
    protected function getFolder(){
        return $this->folder ? $this->folder : $this->getClassName();
    }

    /**
     * Корневой путь
     * @return string
     */
    public function getRoot(){
        return Yii::getAlias('@app').DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'frontend'.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'files';
    }
}