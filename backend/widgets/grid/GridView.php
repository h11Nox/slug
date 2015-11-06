<?php
/**
* @author: Nox
*/

namespace backend\widgets\grid;

use yii\helpers\Html;

class GridView extends \yii\grid\GridView{

    /**
     * @var array the HTML attributes for the grid table element.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $tableOptions = ['class' => 'table table-striped'];

    /**
     * @inheritdoc
     * @var string
     */
    public $layout = "{items}\n{pager}";

    /**
     * @inheritdoc
     * @return string
     */
    public function renderTableHeader()
    {
        if(!$this->showHeader){
            return parent::renderTableHeader();
        }
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column Column */
            $cells[] = $column->renderHeaderCell();
        }
        $content = Html::tag('tr', implode('', $cells), $this->headerRowOptions);
        if ($this->filterPosition == self::FILTER_POS_HEADER) {
            $content = $this->renderFilters() . $content;
        } elseif ($this->filterPosition == self::FILTER_POS_BODY) {
            $content .= $this->renderFilters();
        }

        return "<tbody>\n" . $content . "\n";
    }

    /**
     * @inheritdoc
     * @return string the rendering result.
     */
    public function renderTableBody()
    {
        if(!$this->showHeader){
            return parent::renderTableBody();
        }

        $models = array_values($this->dataProvider->getModels());
        $keys = $this->dataProvider->getKeys();
        $rows = [];
        foreach ($models as $index => $model) {
            $key = $keys[$index];
            if ($this->beforeRow !== null) {
                $row = call_user_func($this->beforeRow, $model, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }

            $rows[] = $this->renderTableRow($model, $key, $index);

            if ($this->afterRow !== null) {
                $row = call_user_func($this->afterRow, $model, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }
        }

        if (empty($rows)) {
            $colspan = count($this->columns);

            return "\n<tr><td colspan=\"$colspan\">" . $this->renderEmpty() . "</td></tr>\n</tbody>";
        } else {
            return "\n" . implode("\n", $rows) . "\n</tbody>";
        }
    }
}
