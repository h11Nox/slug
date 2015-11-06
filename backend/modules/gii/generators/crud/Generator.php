<?php
/**
 * @author: Nox
 */

namespace backend\modules\gii\generators\crud;

use Yii;

class Generator extends \yii\gii\generators\crud\Generator
{
    public $controllerClass = 'backend\modules\{module}\controllers\{controller}Controller';
    public $viewPath = '@app/modules/{module}/views/{controller}';
    public $baseControllerClass = 'backend\components\Controller';

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'CRUD Генератор';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'Этот генератор реализовывает базовые операции (создания, редактирования, удаления) для модели.';
    }
}
