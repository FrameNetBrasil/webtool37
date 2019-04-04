<?php

class MDataGrid extends MControl {

    public $columns = array();

    public function __construct($args = []) {
        parent::__construct('mdatagrid', $args);
    }

    public function addControl($control) {
        if ($control->className == 'mtoolbar') {
            $control->property->id = $this->property->id . '_toolbar';
            $this->property->toolbar = $control;
        } else {
            $this->property->columns[] = $control;
        }
    }

    public function setNavigator() {
        $this->property->navigator = new StdClass();
        $this->property->navigator->pageLength = $pageLength;
        $this->property->navigator->rowCount = $this->property->rowCount;
        $this->property->navigator->pageCount = ($this->property->pageLength > 0) ? (int) (($this->property->rowCount + $this->property->pageLength - 1) / $this->property->pageLength) : 1;
        $this->property->navigator->action = $action;
        $this->property->navigator->range = new MRange($this->property->pageNumber, $this->property->pageLength, $this->property->rowCount);
        $this->property->navigator->idxFirst = $this->property->navigator->range->offset;
        $this->property->navigator->idxLast = $this->property->navigator->range->offset + $this->property->navigator->range->rows - 1;
        $this->property->navigator->gridCount = $this->property->navigator->range->rows;
    }

    public function generateData() {
        
        if ($this->property->gridData) {
            $this->property->data = $this->property->gridData;
        } else {    
            $this->property->data = json_encode(NULL);
            if ($this->property->query) {
                if (is_string($this->property->query)) {
                    $this->property->url = $this->property->query;
                } else {
                    if ($this->property->query instanceof BaseCriteria) {
                        if ($this->property->orderBy) {
                            $this->property->query->orderBy($this->property->orderBy);
                        }
                    }
                    $model = new MBusinessModel();
                    $this->property->data = $model->gridDataAsJSON($this->property->query);
                }
            }
        }
    }

    public function generate() {
        if ($this->property->actionUpdate) {
            $this->addTool(_M("Save"), $this->property->actionUpdate, "icon-save");
        }
        if ($this->property->actionDelete) {
            $this->addTool(_M("Delete"), $this->property->actionDelete, "icon-remove");
        }
        if ($this->property->actionInsert) {
            $this->addTool(_M("Insert"), $this->property->actionDelete, "icon-add");
        }
        if ($this->property->actionSelect) {
            $this->property->checkbox = true;
        }

        foreach ($this->property->columns as $column) {
            $column->style->width = $column->style->width ? : '0';
            $column->field = (\Manager::getOptions('fetchStyle') == \FETCH_NUM) ? strtoupper($column->field) : $column->field;
        }

        if ($this->property->checkbox) {
            $column = new MDatagridColumn();
            $column->field = $this->id . 'Check';
            $column->type = "check";
            array_unshift($this->property->columns, $column);
        }

        $custom = new \StdClass;
        $events = new \StdClass();
        foreach ($this->options as $option => $value) {
            if (substr($option, 0, 2) == 'on') {
                $events->$option = (object) $value;
            } else {
                $custom->$option = $value;
            }
        }
        $this->generateData();
        //$this->generateJsData();
        if ($this->property->url) {
            $custom->url = $this->property->url;
        }
        if ($this->property->singleSelect) {
            $custom->singleSelect = $this->property->singleSelect;
        }
        if ($this->property->pageLength) {
            $custom->pagination = true;
            $custom->pageSize = $this->property->pageLength;
            $custom->pageList = array(5, 10, 15, 20, 30, 50);
        } else {
            $custom->pagination = false;
        }
        if ($this->property->idField != '') {
            if (preg_match('/^[0-9]/', $this->property->idField)) {
                $this->property->idField = 'F' . $this->property->idField;
            }
            $custom->idField = (\Manager::getOptions('fetchStyle') == \FETCH_NUM) ? strtoupper($this->property->idField) : $this->property->idField;
        }
        $toolbar = '';
        if ($this->property->toolbar) {
            $custom->toolbar = "#{$this->property->toolbar->id}";
            $toolbar = $this->painter->mdiv($this->property->toolbar);
        }

        $custom->border = isset($this->style->border) ? $this->style->border : true;

        $fields = array();
        foreach ($this->property->columns as $column) {
            $field = new StdClass;
            $field->field = $column->field;
            $field->title = $column->title;
            $field->hidden = ($column->visible === false);
            $field->idGrid = $this->id;
            if ($column->action) {
                $field->action = $column->action;
            }
            if ($column->style->width) {
                $field->width = $column->style->width;
            }
            if ($column->align) {
                $field->align = $column->align;
            }
            if ($column->align) {
                $field->halign = $column->halign;
            }
            $field->type = $column->type ? : 'label';
            if ($column->property->options) {
                $field->options = $column->property->options;
            }
            if ($column->render) {
                $field->render = $column->render;
            }
            if ($column->stylizer) {
                $field->stylizer = $column->stylizer;
            }
            if ($column->index) {
                $field->field = 'F' . $column->index;
            }
            if ($column->type == "control") {
                $controls = $column->getControls();
                $firstControl = current($controls);
                $htmlControl = MJSON::encode($firstControl->generate());
                $field->idControl = $firstControl->id;
                $this->page->addJsCode("$('#{$this->id}').data('{$field->idControl}', {$htmlControl});\n");
            }
            if ($column->type == "check") {
                $field->checkbox = true;
            }
            if ($column->type == "icon") {
                $field->icon = $column->icon;
                $field->alt = $column->alt;
                if ($column->field == '') {
                    $field->field = substr(uniqid(), -6);
                }
            }
            $fields[] = $field;
        }
        $paramJson = MJSON::encode((object) ['custom' => $custom, 'columns' => $fields]);
        $eventJson = MJSON::encode((object) ['events' => $events]);
        $this->page->onLoad("theme.datagrid('{$this->property->id}','{$paramJson}', {$eventJson}, '{$this->property->data}');");

        $this->property->idHidden = $this->property->id . '_data';
        $idField = $this->property->idField;
        if ($idField != '') {
            $this->property->manager['idField'] = $idField;
        }

        $attributes = $this->painter->getAttributes($this);
        if ($this->property->head != '') {
            $thead = <<< EOT
<thead>
    <tr>
        {$this->property->head}
    </tr>
</thead>
EOT;
        }

        $grid = <<<EOT
<input type="hidden" id="{$this->property->idHidden}" name="{$this->property->idHidden}" value=""/>
<table {$attributes}>
    {$thead}
</table>
EOT;
        if (!($this->form instanceof \MForm)) { // Todos os grids precisam estar dentro de um form
            $formId = $baseId . '_form';
            $grid = <<<EOT
<form id="{$formId}" name="{$formId}" method="POST">
    {$grid}
</form>
EOT;
            $this->property->form->id = $formId;
        }

        $this->result = $grid . $toolbar;
        return $this->result;
    }

}
