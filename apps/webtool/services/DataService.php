<?php

use Maestro\Types\MFile;


class DataService extends MService
{

    public function getLanguage()
    {
        $language = new fnbr\models\Language();
        return $language->listForCombo()->asQuery()->chunkResult('idLanguage', 'language');
    }

    public function getPOS()
    {
        $pos = new fnbr\models\POS();
        return $pos->listForCombo()->asQuery()->chunkResult('idPOS', 'name');
    }

    public function exportFramesToJSON($idFrames)
    {
        $frameModel = new fnbr\models\Frame();
        $frames = $frameModel->listForExport($idFrames)->asQuery()->getResult();
        $feModel = new fnbr\models\FrameElement();
        $entry = new fnbr\models\Entry();
        foreach ($frames as $i => $frame) {
            $entity = new fnbr\models\Entity($frame['idEntity']);
            $frames[$i]['entity'] = [
                'idEntity' => $entity->getId(),
                'alias' => $entity->getAlias(),
                'type' => $entity->getType(),
                'idOld' => $entity->getIdOld()
            ];
            $frames[$i]['fes'] = [];
            $fes = $feModel->listForExport($frame['idFrame'])->asQuery()->getResult();
            foreach ($fes as $j => $fe) {
                $frames[$i]['fes'][$j] = $fe;
                $entityFe = new fnbr\models\Entity($fe['idEntity']);
                $frames[$i]['fes'][$j]['entity'] = [
                    'idEntity' => $entityFe->getId(),
                    'alias' => $entityFe->getAlias(),
                    'type' => $entityFe->getType(),
                    'idOld' => $entityFe->getIdOld()
                ];
                $coreset = $feModel->listCoreSet($fe['idFrameElement'])->asQuery()->getResult();
                $frames[$i]['fes'][$j]['coreset'] = $coreset;
                $excludes = $feModel->listExcludes($fe['idFrameElement'])->asQuery()->getResult();
                $frames[$i]['fes'][$j]['excludes'] = $exclude;
                $requires = $feModel->listRequires($fe['idFrameElement'])->asQuery()->getResult();
                $frames[$i]['fes'][$j]['requires'] = $requires;
                $color = new fnbr\models\Color($fe['idColor']);
                $frames[$i]['fes'][$j]['color'] = [
                    'name' => $color->getName(),
                    'rgbFg' => $color->getRgbFg(),
                    'rgbBg' => $color->getRgbBg(),
                ];
                $entries = $entry->listForExport($fe['entry'])->asQuery()->getResult();
                foreach ($entries as $n => $e) {
                    $frames[$i]['fes'][$j]['entries'][] = $e;
                }
            }
            $entries = $entry->listForExport($frame['entry'])->asQuery()->getResult();
            foreach ($entries as $j => $e) {
                $frames[$i]['entries'][] = $e;
            }
        }
        $result = json_encode($frames);
        return $result;
    }

    public function importFramesFromJSON($json)
    {
        $frames = json_decode($json);
        $frame = new fnbr\models\Frame();
        $fe = new fnbr\models\FrameElement();
        $entity = new fnbr\models\Entity();
        $entry = new fnbr\models\Entry();
        $transaction = $frame->beginTransaction();
        try {
            foreach ($frames as $frameData) {
                // create entries
                $entries = $frameData->entries;
                foreach ($entries as $entryData) {
                    $entry->createFromData($entryData);
                }
                // create entity
                $entity->createFromData($frameData->entity);
                // craete frame
                $frameData->idEntity = $entity->getId();
                $frame->createFromData($frameData);
                // create frameElements
                $fes = $frameData->fes;
                foreach ($fes as $feData) {
                    // create fe entries
                    $entries = $feData->entries;
                    foreach ($entries as $entryData) {
                        $entry->createFromData($entryData);
                    }
                    // create fe entity
                    $entity->createFromData($feData->entity);
                    // craete frame
                    $feData->idEntity = $entity->getId();
                    $feData->idFrame = $frame->getId();
                    $fe->createFromData($feData);
                    $feData->idFrameElement = $fe->getId();
                }
                // create frameElements relations (fes must be created before)
                foreach ($fes as $feData) {
                    $fe->getById($feData->idFrameElement);
                    $fe->createRelationsFromData($feData);
                }
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \exception($e->getMessage());
        }
    }

    public function parseDocWf($file)
    {
        $getOutput = function ($diff) {
            $output = '';
            foreach ($diff as $w) {
                if (!is_numeric($w)) {
                    $output .= $w . ' X ' . $w . "\n";
                }
            }
            return $output;
        };
        $words = [];
        $rows = file($file->getTmpName());
        foreach ($rows as $row) {
            //mdump($row);
            // excludes punctuation
            $row = strtolower(str_replace([',', '.', ';', '!', '?', ':', '"', '(', ')', '[', ']', '<', '>', '{', '}'], ' ', utf8_decode($row)));
            $row = str_replace("\t", " ", $row);
            $row = str_replace("\n", " ", $row);
            $row = utf8_encode(trim($row));

            if ($row == '') {
                continue;
            }
            $wordsTemp = explode(' ', $row);
            foreach ($wordsTemp as $word) {
                $word = str_replace("'", "''", $word);
                $words[$word] = $word;
            }
        }
        $wf = new fnbr\models\WordForm();
        $output = "";
        $i = 0;
        foreach ($words as $word) {
            if (trim($word) != '') {
                $lookFor[$word] = $word;
                if ($i++ == 200) {
                    $found = $wf->lookFor($lookFor);
                    $output .= $getOutput(array_diff($lookFor, $found));
                    $lookFor = [];
                    $i = 0;
                }
            }
        }
        if (count($lookFor)) {
            $found = $wf->lookFor($lookFor);
            $output .= $getOutput(array_diff($lookFor, $found));
        }
        $fileName = str_replace(' ', '_', $file->getName()) . '_docwf.txt';
        $mfile = MFile::file("\xEF\xBB\xBF" . $output, false, $fileName);
        return $mfile;
    }

    private function getFSTree($structure, $idEntity)
    {
        $tree = [];
        foreach ($structure as $node) {
            if ($node['idEntity'] == $idEntity) {
                $tree = [
                    'id' => $node['idEntity'],
                    'nick' => $node['nick'],
                    'name' => $node['name'],
                    'entry' => $node['entry'],
                    'typeSystem' => $node['typeSystem'],
                    'children' => []
                ];
            }
        }
        foreach ($structure as $node) {
            if ($node['source'] == $idEntity) {
                $tree['children'][$node['idEntity']] = $this->getFSTree($structure, $node['idEntity']);
            }
        }
        return $tree;
    }

    private function getFSTreeText($node, &$text, $ident = '')
    {
        $text .= $ident . $node['typeSystem'] . '_' . $node['entry'] . ($node['name'] ? "  [" . $node['name'] . "]" : "") . "\n";
        foreach ($node['children'] as $child) {
            $this->getFSTreeText($child, $text, $ident . '    ');
        }
    }

    public function exportCxnToFS($data = '')
    {
        $data = $data ?: $this->data;
        $viewCxn = new fnbr\models\ViewConstruction();
        $filter = (object)['idDomain' => $data->idDomain, 'idLanguage' => $data->idLanguage];
        $cxns = $viewCxn->listByFilter($filter)->asQuery()->getResult(\FETCH_ASSOC);
        $construction = new fnbr\models\Construction();
        $network = [];
        foreach ($cxns as $cxn) {
            $construction->getById($cxn['idConstruction']);
            if ($construction->getActive()) {
                $structure = $construction->getStructure();
                $network[$structure->entry] = $structure;
            }
        }
        $fs = [
            'network' => $network
        ];
        mdump(json_encode($fs));
        return json_encode($fs);
    }

    public function exportCxnToJSON($idCxns)
    {
        $cxnModel = new fnbr\models\Construction();
        $cxns = $cxnModel->listForExport($idCxns)->asQuery()->getResult();
        $ceModel = new fnbr\models\ConstructionElement();
        $entry = new fnbr\models\Entry();
        $cnModel = new fnbr\models\ViewConstraint();
        $result = [];
        foreach ($cxns as $i => $cxn) {
            $cxnModel->getById($cxn['idConstruction']);
            $entity = new fnbr\models\Entity($cxn['idEntity']);
            $result[$i]['data'] = $cxn;
            $result[$i]['entity'] = [
                'idEntity' => $entity->getId(),
                'alias' => $entity->getAlias(),
                'type' => $entity->getType(),
                'idOld' => $entity->getIdOld()
            ];
            $result[$i]['ces'] = [];
            $ces = $ceModel->listForExport($cxn['idConstruction'])->asQuery()->getResult();
            foreach ($ces as $j => $ce) {
                $ceModel->getById($ce['idConstructionElement']);
                $result[$i]['ces'][$j]['data'] = $ce;
                $entityCe = new fnbr\models\Entity($ce['idEntity']);
                $result[$i]['ces'][$j]['entity'] = [
                    'idEntity' => $entityCe->getId(),
                    'alias' => $entityCe->getAlias(),
                    'type' => $entityCe->getType(),
                    'idOld' => $entityCe->getIdOld()
                ];
                $entries = $entry->listForExport($ce['entry'])->asQuery()->getResult();
                foreach ($entries as $n => $e) {
                    $result[$i]['ces'][$j]['entries'][] = $e;
                }
                $treeEvokes = $ceModel->listEvokesRelations();
                foreach($treeEvokes as $evokes) {
                    foreach($evokes as $evoke) {
                        $result[$i]['ces'][$j]['evokes'][] = $evoke['frameEntry'];
                    }
                }
                $treeRelations = $ceModel->listDirectRelations();
                foreach($treeRelations as $relationEntry => $relations) {
                    foreach($relations as $relation) {
                        $result[$i]['ces'][$j]['relations'][] = [$relationEntry, $relation['ceEntry']];
                    }
                }

                $chain = $cnModel->getChainForExportByIdConstrained($ce['idEntity']);
                $result[$i]['ces'][$j]['constraints']= $chain;
            }
            $entries = $entry->listForExport($cxn['entry'])->asQuery()->getResult();
            foreach ($entries as $j => $e) {
                $result[$i]['entries'][] = $e;
            }
            $treeEvokes = $cxnModel->listEvokesRelations();
            foreach($treeEvokes as $evokes) {
                foreach($evokes as $evoke) {
                    $result[$i]['evokes'][] = $evoke['frameEntry'];
                }
            }
            $treeRelations = $cxnModel->listDirectRelations();
            foreach($treeRelations as $relationEntry => $relations) {
                foreach($relations as $relation) {
                    $result[$i]['relations'][] = [$relationEntry, $relation['cxnEntry']];
                }
            }
            $treeRelations = $cxnModel->listInverseRelations();
            foreach($treeRelations as $relationEntry => $relations) {
                foreach($relations as $relation) {
                    $result[$i]['inverse'][] = [$relationEntry, $relation['cxnEntry']];
                }
            }

            $chain = $cnModel->getChainForExportByIdConstrained($cxn['idEntity']);
            $result[$i]['constraints']= $chain;

        }
        $json = json_encode($result);
        return $json;
    }

    public function importCxnFromJSON($json)
    {
        $cxns = json_decode($json);
        $cxnModel = new fnbr\models\Construction();
        $ceModel = new fnbr\models\ConstructionElement();
        $entityModel = new fnbr\models\Entity();
        $entryModel = new fnbr\models\Entry();
        $transaction = $cxnModel->beginTransaction();
        try {
            foreach ($cxns as $cxnData) {
                // create entries
                $entries = $cxnData->entries;
                foreach ($entries as $entryData) {
                    $entryModel->createFromData($entryData);
                }
                // create entity
                $entityModel->createFromData($cxnData->entity);
                // craete cxn
                $cxnData->data->idEntity = $entityModel->getId();
                $cxnModel->createFromData($cxnData->data);
                $cxnData->data->idConstruction = $cxnModel->getId();
                // create ces
                $ces = $cxnData->ces;
                foreach ($ces as $ceData) {
                    // create ce entries
                    $entries = $ceData->entries;
                    foreach ($entries as $entryData) {
                        $entryModel->createFromData($entryData);
                    }
                    // create ce entity
                    $entityModel->createFromData($ceData->entity);
                    // craete ce
                    $ceData->data->idEntity = $entityModel->getId();
                    $ceData->data->idConstruction = $cxnModel->getId();
                    $ceModel->createFromData($ceData->data);
                    $ceData->data->idConstructionElement = $ceModel->getId();
                }
                // create ces relations (ces must be created before)
                foreach ($ces as $ceData) {
                    $ceModel->getById($ceData->data->idConstructionElement);
                    $ceModel->createRelationsFromData($ceData->data);
                }
            }
            // create cxns relations (cxns must be created before)
            foreach ($cxns as $cxnData) {
                $cxnModel->getById($cxnData->data->idConstruction);
                $cxnModel->createRelationsFromData($cxnData);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \exception($e->getMessage());
        }
    }



}
