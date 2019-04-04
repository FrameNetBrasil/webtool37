<?php



class StructureRelationTypeService extends MService
{

    public function listAll($data = '', $idLanguage = '')
    {
        $rt = new fnbr\models\RelationType();
        $rows = $rt->listAll()->asQuery()->getResult();
        $result = array();
        foreach ($rows as $row) {
            $node = array();
            $node['id'] = 'm' . $row['idRelationType'];
            $node['text'] = $row['name'];
            $node['entry'] = $row['entry'];
            $node['nameEntity1'] = $row['nameEntity1'];
            $node['nameEntity2'] = $row['nameEntity2'];
            $result[] = $node;
        }
        return $result;
    }

    public function lookupDataQualiaRelation(){
        $model = new fnbr\models\RelationType();
        $filter = (object)[
            'group' => 'rgp_qualia'
        ];
        $criteria = $model->listbyFilter($filter);
        return $this->renderJSON($model->gridDataAsJSON($criteria));
    }
    
}
