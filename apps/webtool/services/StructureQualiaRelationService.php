<?php


class StructureQualiaRelationService extends MService
{

    public function listAll($data = '', $idLanguage = '')
    {
        $rt = new fnbr\models\Qualia();
        $rows = $rt->listByFilter($data)->asQuery()->getResult();
        $result = array();
        foreach ($rows as $row) {
            $node = array();
            $node['id'] = 'r' . $row['idQualia'];
            $node['text'] = $row['name'];
            $node['entry'] = $row['entry'];
            $node['iconCls'] = 'icon-blank fas fa-arrows-alt-h fa16px ' . $row['qualiaType'];
            $result[] = $node;
        }
        return $result;
    }

    public function lookupDataQualiaRelation()
    {
        $model = new fnbr\models\RelationType();
        $filter = (object)[
            'group' => 'rgp_qualia'
        ];
        $criteria = $model->listbyFilter($filter);
        return $this->renderJSON($model->gridDataAsJSON($criteria));
    }

}
