<?php

namespace common\behaviors\models;
use common\helpers\image\Image;
use Imagine\Image\ManipulatorInterface;
use yii\console\Application;

/**
 * Изображение
 * Class AttachImageModel
 * @author: Nox
 * @package common\behaviors\models
 */
class AttachImageModel extends AttachFileModel{

    /**
     * Получить уменьшенную копию изображения
     * @param $size
     * @param int $resize
     * @return string
     */
    public function getThumb($size = array(100, 100), $resize = 1){
        if(!is_array($size)){
            $size = explode('x', $size);
        }

        $sizes = $size;
        $byWidth = $byHeight = false;
        if(empty($size[0])){
            $size[0] = $size[1];
            $sizes[0] = 0;
            $byHeight = true;
            $resize = 3;
        }
        if(empty($size[1])) {
            $size[1] = $size[0];
            $sizes[1] = 0;
            $byWidth = true;
            $resize = 3;
        }
        $parts = explode('.', $this->name);

        if(!$this->isExists()){
            return 'http://placehold.it/'.$size[0].'x'.$size[1].'?text=NO+IMAGE';
        }

        $fileName = implode('_', array($parts[0], $sizes[0], $sizes[1], $resize)).'.'.$parts[1];
        $filePath = $this->getPathFolder().DIRECTORY_SEPARATOR.$fileName;

        if(!file_exists($filePath)){
            switch ($resize){
                case 1:
                    $thumb = Image::thumbnail($this->getPath(), $size[0], $size[1]);
                    break;
                case 2:
                    $thumb = Image::thumbnail($this->getPath(), $size[0], $size[1], ManipulatorInterface::THUMBNAIL_INSET);
                    break;
                case 3:
                    // By Side
                    $imageSize = getimagesize($this->getPath());
                    $ratio = $imageSize[0] / $imageSize[1];
                    if($byWidth){
                        $size[1] = ceil($size[0] / $ratio);
                    }
                    if($byHeight){
                        $size[0] = ceil($ratio * $size[1]);
                    }
                    $thumb = Image::thumbnail($this->getPath(), $size[0], $size[1]);
                    break;
                default :
                    $thumb = Image::thumbnail($this->getPath(), $size[0], $size[1]);
                    break;
            }

            $thumb->save($filePath, ['quality' => 100]);
        }

        if(\Yii::$app instanceof Application){
            $path = 'http://'.\Yii::$app->params['domain'].str_replace(DIRECTORY_SEPARATOR, '/', substr($this->getPathFolder(), strlen(\Yii::getAlias('@app')) + 16).'/'.$fileName);
        }
        else{
            $path = '/'.str_replace(DIRECTORY_SEPARATOR, '/', substr($this->getPathFolder(), strlen(\Yii::getAlias('@webroot'))).'/'.$fileName);
        }
        return $path;
    }

}