<?php

class StructureDomainService extends MService
{

    public function listFrameDomain($id)
    {
        $relations = fnbr\models\Base::relationCriteria('ViewFrame', 'Domain', 'rel_hasdomain', 'Domain.idDomain');
        $relations->where("idFrame = {$id}");
        $domains = $relations->asQuery()->chunkResult('idDomain','idDomain');
        $domain = new fnbr\models\Domain();
        $types = $domain->listAll()->asQuery()->getResult();
        $result = array();
        foreach ($types as $row) {
            $node = array();
            $node['idDomain'] = $row['idDomain'];
            $node['idEntity'] = $row['idEntity'];
            $node['name'] = $row['name'];
            $node['checked'] = ($domains[$row['idDomain']] != '');
            $result[] = $node;
        }
        return $result;
    }

    public function saveFrameDomain($idFrame, $toSave) {
        $frame = new fnbr\models\Frame($idFrame);
        $transaction = $frame->beginTransaction();
        try {
            fnbr\models\Base::deleteEntity1Relation($frame->getIdEntity(), 'rel_hasdomain');
            foreach($toSave as $dm) {
                fnbr\models\Base::createEntityRelation($frame->getIdEntity(), 'rel_hasdomain', $dm->idEntity);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \exception("Error updating frame-domains.");
        }

    }

    public function listCxnDomain($id)
    {
        $relations = fnbr\models\Base::relationCriteria('ViewConstruction', 'Domain', 'rel_hasdomain', 'Domain.idDomain');
        $relations->where("idConstruction = {$id}");
        $domains = $relations->asQuery()->chunkResult('idDomain','idDomain');
        $domain = new fnbr\models\Domain();
        $types = $domain->listAll()->asQuery()->getResult();
        $result = array();
        foreach ($types as $row) {
            $node = array();
            $node['idDomain'] = $row['idDomain'];
            $node['idEntity'] = $row['idEntity'];
            $node['name'] = $row['name'];
            $node['checked'] = ($domains[$row['idDomain']] != '');
            $result[] = $node;
        }
        return $result;
    }

    public function saveConstructionDomain($idConstruction, $toSave) {
        $cxn = new fnbr\models\Construction($idConstruction);
        $transaction = $cxn->beginTransaction();
        try {
            fnbr\models\Base::deleteEntity1Relation($cxn->getIdEntity(), 'rel_hasdomain');
            foreach($toSave as $dm) {
                fnbr\models\Base::createEntityRelation($cxn->getIdEntity(), 'rel_hasdomain', $dm->idEntity);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \exception("Error updating construction-domains.");
        }

    }
}
