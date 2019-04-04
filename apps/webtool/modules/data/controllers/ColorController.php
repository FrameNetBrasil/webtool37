<?php

class ColorController extends MController {

    public function main() {
        $this->render("formBase");
    }
    
    public function lookupData(){
        $model = new fnbr\models\Color();
        $colors = $model->listForLookup()->asQuery()->getResult(\FETCH_ASSOC);
        $data = [];
        foreach($colors as $color) {
            $style = 'background-color:#' . $color['rgbBg'] . ';color:#' . $color['rgbFg'] . ';';
            $decorated = "<span style='{$style}'>" . $color['name'] . "</span>";            
            $data[] = (object) [
                'idColor' => $color['idColor'],
                'decorated' => $decorated,
                'name' => $color['name']
            ];
        }
        $this->renderJSON(json_encode($data));
    }

}