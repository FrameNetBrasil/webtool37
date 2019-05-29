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
                foreach ($treeEvokes as $evokes) {
                    foreach ($evokes as $evoke) {
                        $result[$i]['ces'][$j]['evokes'][] = $evoke['frameEntry'];
                    }
                }
                $treeRelations = $ceModel->listDirectRelations();
                foreach ($treeRelations as $relationEntry => $relations) {
                    foreach ($relations as $relation) {
                        $result[$i]['ces'][$j]['relations'][] = [$relationEntry, $relation['ceEntry']];
                    }
                }

                $chain = $cnModel->getChainForExportByIdConstrained($ce['idEntity']);
                $result[$i]['ces'][$j]['constraints'] = $chain;
            }
            $entries = $entry->listForExport($cxn['entry'])->asQuery()->getResult();
            foreach ($entries as $j => $e) {
                $result[$i]['entries'][] = $e;
            }
            $treeEvokes = $cxnModel->listEvokesRelations();
            foreach ($treeEvokes as $evokes) {
                foreach ($evokes as $evoke) {
                    $result[$i]['evokes'][] = $evoke['frameEntry'];
                }
            }
            $treeRelations = $cxnModel->listDirectRelations();
            foreach ($treeRelations as $relationEntry => $relations) {
                foreach ($relations as $relation) {
                    $result[$i]['relations'][] = [$relationEntry, $relation['cxnEntry']];
                }
            }
            $treeRelations = $cxnModel->listInverseRelations();
            foreach ($treeRelations as $relationEntry => $relations) {
                foreach ($relations as $relation) {
                    $result[$i]['inverse'][] = [$relationEntry, $relation['cxnEntry']];
                }
            }

            $chain = $cnModel->getChainForExportByIdConstrained($cxn['idEntity']);
            $result[$i]['constraints'] = $chain;

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

    public function exportDocumentToXML()
    {
        $document = new \fnbr\models\Document();
        $document->getByEntry($this->data->documentEntry);
        $corpus = $document->getCorpus();

        $idLanguage = \fnbr\models\Base::getIdLanguage($this->data->language);

        print_r("idlanguage = " . $idLanguage . "\n");

        $xmlStr = <<<HERE
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<?xml-stylesheet type="text/xsl" href="fullText.xsl"?>
<fullTextAnnotation>
    <header>
        <corpus description="{$corpus->getDescription()}" name="{$corpus->getName()}" ID="{$corpus->getId()}">
            <document></document>
        </corpus>
    </header>
</fullTextAnnotation>

HERE;

        $sxe = simplexml_load_string($xmlStr);
        $sxe->header->corpus->document->addAttribute('description', $document->getName());
        $sxe->header->corpus->document->addAttribute('name', $document->getName());
        $sxe->header->corpus->document->addAttribute('ID', $document->getId());
        $sentences = $document->listSentenceForXML()->getResult();
        $i = 0;
        foreach ($sentences as $sentence) {
            $s = $sxe->addChild('sentence');
            $s->addAttribute('ID', $sentence['idSentence']);
            $t = $s->addChild('text', $sentence['text']);
            $queryAS = $document->listAnnotationSetForXML($sentence['idSentence'], $idLanguage);
            $idAS = 0;
            $layer = '';
            $labels = $queryAS->getResult();
            foreach ($labels as $label) {
                if ($label['idAnnotationSet'] != $idAS) {
                    $idAS = $label['idAnnotationSet'];
                    $aset = $s->addChild('annotationSet');
                    $aset->addAttribute('ID', $label['idAnnotationSet']);
                    $aset->addAttribute('luID', $label['idLU']);
                    $aset->addAttribute('luName', $label['luName']);
                    $aset->addAttribute('frameID', $label['idFrame']);
                    $aset->addAttribute('frameName', $label['frameName']);
                    $layer = '';
                }
                if ($layer != $label['layerTypeEntry']) {
                    $layer = $label['layerTypeEntry'];
                    $ly = $aset->addChild('layer');
                    $ly->addAttribute('name', str_replace('lty_', '', $label['layerTypeEntry']));
                }
                $lb = $ly->addChild('label');
                $lb->addAttribute('ID', $label['idFrameElement'] . $label['idGenericLabel']);
                $lb->addAttribute('name', $label['feName'] . $label['glName']);
                $lb->addAttribute('start', $label['startChar']);
                $lb->addAttribute('end', $label['endChar']);
            }
            if ((++$i % 5) == 0) {
                print_r($i . ' sentence(s)' . "\n");
            }
        }
        file_put_contents($this->data->filename, $sxe->asXML());
    }

    public function exportDocumentToCONLL($document)
    {
        $document = new \fnbr\models\Document();
        $document->getByEntry($this->data->documentEntry);
        $idLanguage = 2;//$this->data->idLanguage; //\Manager::getSession()->idLanguage;
        $count = 0;
        $lines = '';
        $lexemeCache = [];
        $wf = new fnbr\models\Wordform();//new fnbr\models\ViewWfLexemeLemma();
        $querySentence = $document->listSentenceForCONLL();
        $sentences = $querySentence->getResult();
        foreach ($sentences as $sentence) {
            $words = [];
            $lexemes = [];
            $pos = 0;
            $text = utf8_decode($sentence['text']) . ' ';
            $n = strlen($text);
            mdump($text);
            for ($i = 0; $i < $n; $i++) {
                if (($text{$i} == ' ')
                    || ($text{$i} == '.')
                    || ($text{$i} == ',')
                    || ($text{$i} == ':')
                    || ($text{$i} == ';')
                    || ($text{$i} == '-')
                    || ($text{$i} == '=')
                    || ($text{$i} == '?')
                    || ($text{$i} == '!')
                    || ($text{$i} == '/')
                    || ($text{$i} == '\'')
                    || ($text{$i} == '"')
                    || ($text{$i} == '<')
                    || ($text{$i} == '>')) {
                    $pos = $i;
                    if ($text{$i} != ' ') {
                        if ($text{$i} == '\'') {
                            $words[$pos] = '\\\'';
                        } else {
                            $words[$pos] = $text{$i};
                        }
                    }
                    $pos++;
                } else {
                    $words[$pos] .= $text{$i};
                }

            }
            foreach ($words as $i => $word) {
                $words[$i] = utf8_encode($word);
            }
            $queryLx = $wf->listLexemes($words)->treeResult("'form'", 'lexeme,POSLexeme');

            foreach ($words as $i => $word) {
                if (isset($lexemeCache[$word])) {
                    $lexemes[$i] = $lexemeCache[$word];
                } else {
                    //$lexeme = $wf->listByFilter((object)['form' => $words[$i]])->asQuery()->getResult()[0];
                    $lexemes[$i] = $lexemeCache[$word] = [
                        $queryLx[$word]['lexeme'],
                        $queryLx[$word]['POSLexeme']
                    ];
                }
            }
            //mdump($lexemes);
            $queryAS = $document->listAnnotationSetForCONLL($sentence['idSentence']);
            // a.idAnnotationSet, lb.layerTypeEntry, lb.startChar, lb.endChar, e1.name frame, e3.name fe, lu.name lu, pos.POS, lx.name lexeme
            $annotationSets = [];
            $idAS = 0;
            $labels = $queryAS->getResult();
            foreach ($labels as $label) {
                if ($label['idAnnotationSet'] != $idAS) {
                    $idAS = $label['idAnnotationSet'];
                    $annotationSets[$idAS][9999] = '# ' . $idAS . ' - ' . $sentence['text'] . "\n";
                }
                $annotationSets[$idAS][$label['startChar']] = $label;
            }
            foreach ($annotationSets as $idAS => $annotationSet) {
                $endChar = -1;
                $mark = -1;
                $id = 1;
                $lines .= $annotationSet[9999];
                $marking = false;
                foreach ($words as $start => $word) {
                    $form = $word;
                    $lexeme = $lexemes[$start][0];
                    $pos = $lexemes[$start][1];
                    $sentenceNum = $sentence['idSentence'];
                    if (isset($annotationSet[$start])) {
                        $label = $annotationSet[$start];
                        if ($label['fe'] == '') {
                            $lu = $label['lu'];
                            $frame = $label['frame'];
                            $biofe = 'O';
                        } else {
                            $lu = '_';
                            $frame = '_';
                            $biofe = 'B-' . $label['fe'];
                        }
                        $marking = true;
                        $endChar = $label['endChar'];
                        $mark = $start;
                    } else if ($marking && ($start <= $endChar)) {
                        $label = $annotationSet[$mark];
                        if ($label['fe'] == '') {
                            $lu = $label['lu'];
                            $frame = $label['frame'];
                            $biofe = 'O';
                        } else {
                            $lu = '_';
                            $frame = '_';
                            $biofe = 'I-' . $label['fe'];
                        }
                    } else {
                        $lu = '_';
                        $frame = '_';
                        $biofe = 'O';
                        $marking = false;
                    }
                    $lines .= $id++ . "\t" . $form . "\t" . '_' . "\t" . $lexeme . "\t" . $pos . "\t" . "_" . "\t" . $sentenceNum . "\t_\t_\t_\t_\t_\t" . $lu . "\t" . $frame . "\t" . $biofe . "\n";
                }
                $lines .= "\n";
            }
            if ((++$count % 5) == 0) {
                print_r($count . ' sentence(s)' . "\n");
            }

        }
        //return $lines;
        file_put_contents($this->data->filename, $lines);

    }


}
