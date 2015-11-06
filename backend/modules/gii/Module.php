<?php

namespace backend\modules\gii;

use yii\base\BootstrapInterface;

/**
* Gii generator
* @author: Nox
*/
class Module extends \yii\gii\Module implements BootstrapInterface{

    /**
     * @inheritdoc
     */
    // public $controllerNamespace = 'backend\modules\gii\controllers';

    /**
     * Returns the list of the core code generator configurations.
     * @return array the list of the core code generator configurations.
     */
    protected function coreGenerators()
    {
        return [
            'model' => ['class' => 'yii\gii\generators\model\Generator'],
            'crud' => ['class' => 'backend\modules\gii\generators\crud\Generator'],
            'controller' => ['class' => 'yii\gii\generators\controller\Generator'],
            'form' => ['class' => 'yii\gii\generators\form\Generator'],
            'module' => ['class' => 'yii\gii\generators\module\Generator'],
            'extension' => ['class' => 'yii\gii\generators\extension\Generator'],
        ];
    }

}

