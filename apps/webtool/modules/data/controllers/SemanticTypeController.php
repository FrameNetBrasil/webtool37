<?php

class SemanticTypeController extends MController {

    public function lookupData($rowsOnly = false, $idDomain = 0){
        $model = new fnbr\models\SemanticType();
        $filter = (object) ['idDomain' => $idDomain, 'name' => $this->data->q];
        $criteria = $model->listForLookup($filter);
        $this->renderJSON($model->gridDataAsJSON($criteria, $rowsOnly));
    }

    public function lookupDataForLU($rowsOnly = false){
        $model = new fnbr\models\SemanticType();
        $query = $model->listForLookupLU();
        $this->renderJSON($model->gridDataAsJSON($query, $rowsOnly));
    }

    public function listFrameDomain($id)
    {
        $relations = fnbr\models\Base::relationCriteria('ViewFrame', 'SemanticType', 'rel_framal_domain', 'SemanticType.idSemanticType');
        $relations->where("idFrame = {$id}");
        $domains = $relations->asQuery()->chunkResult('idSemanticType','idSemanticType');
        $st = new fnbr\models\SemanticType();
        $sts = $st->listFrameDomain()->asQuery()->getResult();
        $result = array();
        foreach ($sts as $row) {
            $node = array();
            $node['idSemanticType'] = $row['idSemanticType'];
            $node['idEntity'] = $row['idEntity'];
            $node['name'] = $row['name'];
            $node['checked'] = ($domains[$row['idSemanticType']] != '');
            $result[] = $node;
        }
        return $result;
    }

    public function listFrameType($id)
    {
        $relations = fnbr\models\Base::relationCriteria('ViewFrame', 'SemanticType', 'rel_framal_type', 'SemanticType.idSemanticType');
        $relations->where("idFrame = {$id}");
        $types = $relations->asQuery()->chunkResult('idSemanticType','idSemanticType');
        $st = new fnbr\models\SemanticType();
        $sts = $st->listFrameType()->asQuery()->getResult();
        $result = array();
        foreach ($sts as $row) {
            $node = array();
            $node['idSemanticType'] = $row['idSemanticType'];
            $node['idEntity'] = $row['idEntity'];
            $node['name'] = $row['name'];
            $node['checked'] = ($types[$row['idSemanticType']] != '');
            $result[] = $node;
        }
        return $result;
    }

    public function listFrameCluster($id)
    {
        $relations = fnbr\models\Base::relationCriteria('ViewFrame', 'SemanticType', 'rel_framal_cluster', 'SemanticType.idSemanticType');
        $relations->where("idFrame = {$id}");
        $clusters = $relations->asQuery()->chunkResult('idSemanticType','idSemanticType');
        $st = new fnbr\models\SemanticType();
        $sts = $st->listFrameCluster()->asQuery()->getResult();
        $result = array();
        foreach ($sts as $row) {
            $node = array();
            $node['idSemanticType'] = $row['idSemanticType'];
            $node['idEntity'] = $row['idEntity'];
            $node['name'] = "<b>" . $row['name'] . "</b>" . ' : ' . $row['description'] ;
            $node['checked'] = ($clusters[$row['idSemanticType']] != '');
            $result[] = $node;
        }
        return $result;
    }


}