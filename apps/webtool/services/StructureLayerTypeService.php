<?php



class StructureLayerTypeService extends MService
{

    public function listAll($data = '', $idLanguage = '')
    {
        $lt = new fnbr\models\LayerType();
        $rows = $lt->listAll()->asQuery()->getResult();
        $result = array();
        foreach ($rows as $row) {
            $node = array();
            $node['id'] = 'm' . $row['idLayerType'];
            $node['text'] = $row['name'];
            $node['state'] = 'closed';
            $node['entry'] = $row['entry'];
            $result[] = $node;
        }
        return $result;
    }

    public function listGLByLayer($idLayerType, $idLanguage)
    {
        $result = array();
        $genericLabel = new fnbr\models\GenericLabel();
        $gls = $genericLabel->listByLayerType($idLayerType, $idLanguage)->asQuery()->getResult();
        foreach ($gls as $gl) {
            $node = array();
            $node['id'] = 'g' . $gl['idGenericLabel'];
            $style = 'background-color:#' . $gl['rgbBg'] . ';color:#' . $gl['rgbFg'] . ';';
            $node['text'] = "<span style='{$style}'>" . $gl['name'] . "</span>";
            $node['state'] = 'closed';
            $node['entry'] = $gl['name'];
            $node['iconCls'] = 'icon-blank fa-icon fa fa-align-justify';
            $result[] = $node;
        }
        return json_encode($result);
    }


}
