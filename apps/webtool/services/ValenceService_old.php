<?php



class ValenceService extends MService
{
    public $lang;
    public $gfEquivalence;
    public $ptEquivalence;
    public $weight;

    public function init()
    {
        Manager::checkLogin(false);
        $this->idLanguage = Manager::getConf('options.language');
        $this->idDomain = 5;
    }

    public function getCombinatorialScore($idFrame, $idLanguageSource, $idLanguageTarget)
    {
        if (substr($idFrame, 0, 1) == 'f') {
            $frame = new fnbr\models\ViewFrame();
            $idFrame = $frame->listByFilter((object)['idEntity' => substr($idFrame, 1)])->asQuery()->getResult()[0]['idFrame'];
        }
        $this->lang = ['pt' => 1, 'en' => 2, 'es' => 3];
        $this->setWeight();
        $this->setGFEquivalence();
        $this->setPTEquivalence();
        $sourceLUs = $this->getLU($idFrame, $idLanguageSource);
        foreach ($sourceLUs as $idLU => $lu) {
            $valenciaSource = $this->getValenciaFromTable($idLU, $idLanguageSource);
            //mdump($valenciaSource);
            $targetLUs = $this->getLU($idFrame, $idLanguageTarget);
            foreach ($targetLUs as $idLUTarget => $luTarget) {
                $valenciaTarget = $this->getValenciaFromTable($idLUTarget, $idLanguageTarget);
                //mdump($valenciaTarget);
                $score[$lu][$luTarget] = $this->compareCombinatorialValencia($valenciaSource, $valenciaTarget, $idLanguageSource);
                //break;
            }
            //break;
        }
        if ($this->data->id == 'json') {
            return json_encode($score);
        }
        return $score;
    }

    public function getMaximumScore($idFrame, $idLanguageSource, $idLanguageTarget, $byId = false)
    {
        if (substr($idFrame, 0, 1) == 'f') {
            $frame = new fnbr\models\ViewFrame();
            $idFrame = $frame->listByFilter((object)['idEntity' => substr($idFrame, 1)])->asQuery()->getResult()[0]['idFrame'];
        }
        $this->lang = ['pt' => 1, 'en' => 2, 'es' => 3];
        $this->setWeight();
        $this->setGFEquivalence();
        $this->setPTEquivalence();
        $sourceLUs = $this->getLU($idFrame, $idLanguageSource);
        foreach ($sourceLUs as $idLU => $lu) {
            $valenciaSource = $this->getValenciaFromTable($idLU, $idLanguageSource);
            //mdump($valenciaSource);
            $targetLUs = $this->getLU($idFrame, $idLanguageTarget);
            foreach ($targetLUs as $idLUTarget => $luTarget) {
                $valenciaTarget = $this->getValenciaFromTable($idLUTarget, $idLanguageTarget);
                //mdump($valenciaTarget);
                if ($byId) {
                    $score[$idLU][$idLUTarget] = $this->compareMaximumValencia($valenciaSource, $valenciaTarget, $idLanguageSource);

                } else {
                    $score[$lu][$luTarget] = $this->compareMaximumValencia($valenciaSource, $valenciaTarget, $idLanguageSource);
                }
                //break;
            }
            //break;
        }
        if ($this->data->id == 'json') {
            return json_encode($score);
        }
        return $score;
    }

    public function updateMaximumScore()
    {
        $v = new fnbr\models\ViewFrame();
        $db = $v->getDb();
        $transaction = $db->beginTransaction();
        try {
            $command = "delete from LUEquivalence";
            $db->executeCommand($command);
            $query = "
SELECT f.idFrame
FROM view_domain d join Frame f on (d.idEntityRel = f.idEntity)
where (d.iddomain = {$this->idDomain}) and (d.idLanguage = 1)
";
            $rows = $db->getQueryCommand($query)->getResult();
            foreach ($rows as $row) {
                $idFrame = $row['idFrame'];
                $score = $this->getMaximumScore($idFrame, 1, 2, true);
                foreach($score as $idLUSource => $result) {
                    foreach($result as $idLUTarget => $values) {
                        $value = $values['score'];
                        $variance = $values['variancia'];
                        $command = "insert into luequivalence(idLuSource, idLuTarget, score, variance) values ({$idLUSource},{$idLUTarget}, {$value}, {$variance})";
                        //mdump($command);
                        $db->executeCommand($command);
                    }
                }
                $score = $this->getMaximumScore($idFrame, 1, 3, true);
                foreach($score as $idLUSource => $result) {
                    foreach($result as $idLUTarget => $values) {
                        $value = $values['score'];
                        $variance = $values['variancia'];
                        $command = "insert into luequivalence(idLuSource, idLuTarget, score, variance) values ({$idLUSource},{$idLUTarget}, {$value}, {$variance})";
                        //mdump($command);
                        $db->executeCommand($command);
                    }
                }
                $score = $this->getMaximumScore($idFrame, 2, 3, true);
                foreach($score as $idLUSource => $result) {
                    foreach($result as $idLUTarget => $values) {
                        $value = $values['score'];
                        $variance = $values['variancia'];
                        $command = "insert into luequivalence(idLuSource, idLuTarget, score, variance) values ({$idLUSource},{$idLUTarget}, {$value}, {$variance})";
                        //mdump($command);
                        $db->executeCommand($command);
                    }
                }
                $score = $this->getMaximumScore($idFrame, 2, 1, true);
                foreach($score as $idLUSource => $result) {
                    foreach($result as $idLUTarget => $values) {
                        $value = $values['score'];
                        $variance = $values['variancia'];
                        $command = "insert into luequivalence(idLuSource, idLuTarget, score, variance) values ({$idLUSource},{$idLUTarget}, {$value}, {$variance})";
                        //mdump($command);
                        $db->executeCommand($command);
                    }
                }
                $score = $this->getMaximumScore($idFrame, 3, 1, true);
                foreach($score as $idLUSource => $result) {
                    foreach($result as $idLUTarget => $values) {
                        $value = $values['score'];
                        $variance = $values['variancia'];
                        $command = "insert into luequivalence(idLuSource, idLuTarget, score, variance) values ({$idLUSource},{$idLUTarget}, {$value}, {$variance})";
                        //mdump($command);
                        $db->executeCommand($command);
                    }
                }
                $score = $this->getMaximumScore($idFrame, 3, 2, true);
                foreach($score as $idLUSource => $result) {
                    foreach($result as $idLUTarget => $values) {
                        $value = $values['score'];
                        $variance = $values['variancia'];
                        $command = "insert into luequivalence(idLuSource, idLuTarget, score, variance) values ({$idLUSource},{$idLUTarget}, {$value}, {$variance})";
                        //mdump($command);
                        $db->executeCommand($command);
                    }
                }
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
        }
    }


    public function getFramePatterns($idLanguage, $idFrame = '')
    {
        $result = $patterns = [];
        if ($idFrame != '') {
            $frames = [$idFrame];
        } else {
            $frames = $this->getFrame();
        }
        foreach ($frames as $idFrame) {
            $lus = $this->getLU($idFrame, $idLanguage);
            foreach ($lus as $idLU => $lu) {
                $valencias = $this->getValencia($idLU, $idLanguage);
                foreach ($valencias as $valencia) {
                    $this->updatePatterns($patterns, $valencia);
                }
                $result[$idLU] = $patterns;
            }
        }
        return $result;
    }

    public function updateFramePatterns($idLanguage, $idFrame = '')
    {
        $this->lang = ['pt' => 1, 'en' => 2, 'es' => 3];
        $this->setGFEquivalence();
        $this->setPTEquivalence();
        $v = new fnbr\models\ViewFrame();
        $db = $v->getDb();
        $query = "
SELECT f.idFrame
FROM view_domain d join Frame f on (d.idEntityRel = f.idEntity)
where (d.iddomain = {$this->idDomain}) and (d.idLanguage = 1)
" . ($idFrame != '' ? "where idFrame = {$idFrame}" : "");
        $rows = $db->getQueryCommand($query)->getResult();
        foreach ($rows as $row) {
            $transaction = $db->beginTransaction();
            try {
                $idFrame = $row['idFrame'];
                $command = "delete from ValencePattern where (idFrame = {$idFrame}) and (idLanguage = {$idLanguage}) ";
                $db->executeCommand($command);
                $result = $this->getFramePatterns($idLanguage, $idFrame);
                $command = "insert into ValencePattern (idFrame, idLU, idLanguage, idPattern, countPattern, idFrameElement, GF, GFSource, PT) values(?,?,?,?,?,?,?,?,?)";
                foreach ($result as $idLU => $patterns) {
                    foreach ($patterns as $idPattern => $pattern) {
                        mdump($idPattern);
                        $count = $pattern['count'];
                        foreach ($pattern['valence'] as $idFE => $valence) {
                            mdump($valence);
                            $db->executeCommand($command, [$idFrame, $idLU, $valence['idLanguage'], $idPattern, $count, $idFE, $valence['GF'], $valence['GFSource'], $valence['PT']]);
                        }
                    }
                }
                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollback();
            }
        }

    }

    /*
    public function getPatterns($idFrame, $idLanguageSource, $idLanguageTarget)
    {
        $this->lang = ['pt' => 1, 'en' => 2, 'es' => 3];
        $this->setGFEquivalence();
        $this->setPTEquivalence();
        $source = $patternsSource = [];
        $sourceLUs = $this->getLU($idFrame, $idLanguageSource);
        foreach ($sourceLUs as $idLU => $lu) {
            $valencias = $this->getValencia($idLU, $idLanguageSource);
            foreach ($valencias as $valencia) {
                $this->updatePatterns($patternsSource, $valencia);
            }
            $source[$lu] = $patternsSource;
        }
        $target = $patternsTarget = [];
        $targetLUs = $this->getLU($idFrame, $idLanguageTarget);
        foreach ($targetLUs as $idLU => $lu) {
            $valencias = $this->getValencia($idLU, $idLanguageTarget);
            foreach ($valencias as $valencia) {
                $this->updatePatterns($patternsTarget, $valencia);
            }
            $target[$lu] = $patternsTarget;
        }
        return ['source' => $source, 'target' => $target];
    }
    */

    public function updatePatterns(&$patterns, $valencia)
    {
        $found = false;
        foreach ($patterns as $i => $pattern) {
            $baseValence = $pattern['valence'];
            if ($this->matchPatterns($baseValence, $valencia)) {
                $patterns[$i]['count'] = $patterns[$i]['count'] + 1;
                $found = true;
            }
        }
        if (!$found) {
            $a = [
                'count' => 1,
                'valence' => $valencia
            ];
            $patterns[] = $a;
        }
    }

    public function matchPatterns($base, $valencia)
    {
        $match = true;
        foreach ($base as $i => $trioBase) {
            $v = $valencia[$i];
            if ($v == '') {
                $match = false;
            } else {
                $match = $match && ($trioBase['FE'] == $v['FE']) && ($trioBase['GFSource'] == $v['GFSource']) && ($trioBase['PT'] == $v['PT']);
            }
        }
        return $match;
    }

    public function setWeight()
    {
        $this->weight = [
            1 => [
                'Aposto' => 0.2,
                'Dep' => 0.5,
                'DetPoss' => 0.7,
                'Ext' => 1.0,
                'Núcleo' => 0.7,
                'ObjD' => 0.8,
                'ObjInd' => 0.6,
                'Quant' => 0.2,
                'CNI' => 1.0,
                'DNI' => 0.5,
                'INI' => 0.8,
                'INC' => 1.0
            ],
            2 => [
                'Appositive' => 0.2,
                'Dep' => 0.6,
                'Ext' => 1.0,
                'Gen' => 0.7,
                'Head' => 0.7,
                'Obj' => 0.8,
                'Quant' => 0.2,
                'CNI' => 1.0,
                'DNI' => 0.5,
                'INI' => 0.8,
                'INC' => 1.0

            ],
            3 => [
                'Appositive' => 0.2,
                'Adjct' => 0.5,
                'AdvObj' => 0.5,
                'Comp' => 0.5,
                'IObj' => 0.6,
                'Mod' => 0.5,
                'PObj' => 0.5,
                'Ext' => 1.0,
                'APos' => 0.7,
                'Head' => 0.7,
                'DObj' => 0.8,
                'Quant' => 0.2,
                'CNI' => 1.0,
                'DNI' => 0.5,
                'INI' => 0.8,
                'INC' => 1.0
            ]
        ];
    }

    public function setGFEquivalence()
    {
        $v = new fnbr\models\ViewLU();
        $query = "
SELECT langSource, langDest, labelSource, labelDest
from gfequivalence
";
        $rows = $v->getDb()->getQueryCommand($query)->getResult();
        foreach ($rows as $row) {
            $langSource = $this->lang[$row['langSource']];
            $langDest = $this->lang[$row['langDest']];
            $this->gfEquivalence[$langSource][$langDest][$row['labelSource']] = $row['labelDest'];
        }

    }

    public function setPTEquivalence()
    {
        $v = new fnbr\models\ViewLU();
        $query = "
SELECT langSource, langDest, labelSource, labelDest
from ptequivalence
";
        $rows = $v->getDb()->getQueryCommand($query)->getResult();
        foreach ($rows as $row) {
            $langSource = $this->lang[$row['langSource']];
            $langDest = $this->lang[$row['langDest']];
            $this->ptEquivalence[$langSource][$langDest][$row['labelSource']] = $row['labelDest'];
        }

    }

    public function getFrame()
    {
        $v = new fnbr\models\ViewFrame();
        $frames = $v->listByFilter((object)[])->asQuery()->chunkResult('idFrame', 'idFrame');
        return $frames;
    }

    public function getLU($idFrame, $idLanguage)
    {
        $v = new fnbr\models\ViewLU();
        $lus = $v->listByFrame($idFrame, $idLanguage)->asQuery()->chunkResult('idLU', 'name');
        asort($lus);
        return $lus;
    }

    public function getValencia($idLU, $idLanguage)
    {
        $query = "SELECT lu.name luName, lu.idLanguage idLanguage, v2.idAnnotationSet, v3.startChar, v3.endChar, v3.layerEntry, v3.itEntry, v3.itName, v3.feId, v3.feName, v3.feTypeEntry, v3.glName
FROM view_lu lu join view_subcorpuslu v1 on (lu.idLU = v1.idLU)
join view_annotationset v2 on (v1.idSubCorpus = v2.idSubCorpus)
join (
select a.idAnnotationSet, a.idSentence, a.idSubCorpus, l.idLayer, l.entry layerEntry, lb.idLabel, entry_it.entry itEntry, entry_it.name itName, entry_fe.name feName, fe.idFRameElement feId, fe.typeEntry feTypeEntry, gl.name glName, lb.startChar, lb.endChar
from View_AnnotationSet a
join View_Layer l on (a.idAnnotationSet = l.idAnnotationSet)
join Label lb on (l.idLayer = lb.idLayer)
join View_InstantiationType it on (lb.idInstantiationType = it.idTypeInstance)
join Entry entry_it on (it.entry = entry_it.entry)
left join View_FrameElement fe on (lb.idLabelType = fe.idEntity)
left join Entry entry_fe on (fe.entry = entry_fe.entry)
left join GenericLabel gl on (lb.idLabelType = gl.IdEntity)
where (entry_it.idLanguage = 2)
and ((entry_fe.idLanguage is null) or (entry_fe.idLanguage = 2))
and (l.entry in ('lty_fe','lty_gf','lty_pt'))
) v3 on (v2.idAnnotationset = v3.idAnnotationSet)
where (lu.idLU = {$idLU}) and ((v3.glName is null) or (v3.glName <> 'Target'))
order by lu.name, v2.idAnnotationSet, v3.startChar, v3.endChar,v3.layerEntry
";
        $lu = new fnbr\models\LU();
        $query = $lu->getDb()->getQueryCommand($query);
        $result = $query->getResult();

        $valencia = [];
        $i = -1;
        if (count($result) > 0) {
            $anId = -1;
            $startChar = -1;
            foreach ($result as $row) {
                if ($row['idAnnotationSet'] != $anId) { // comecou nova anotação
                    $anId = $row['idAnnotationSet'];
                    $startChar = $row['startChar'];
                    $feName = $row['feName'];
                    $feId = $row['feId'];
                    $valencia[$anId][$feId] = [];
                    if (($row['itEntry'] == 'int_ini') || ($row['itEntry'] == 'int_dni') || ($row['itEntry'] == 'int_cni') || ($row['itEntry'] == 'int_inc')) { // INC OU NI
                        $valencia[$anId][$feId]['FE'] = $row['feName'];
                        $valencia[$anId][$feId]['PT'] = $row['itName'];
                        $valencia[$anId][$feId]['GF'] = $row['itName'];
                        $valencia[$anId][$feId]['GFSource'] = $row['itName'];
                        $valencia[$anId][$feId]['idLanguage'] = $row['idLanguage'];
                        $startChar = -1;
                        continue;
                    }
                }

                if (($row['startChar'] != '') && (($row['startChar'] != $startChar) || ($row['layerEntry'] == 'lty_fe'))) { // comecou nova valencia
                    if ($row['layerEntry'] != 'lty_fe') continue;
                    if ($row['feTypeEntry'] != 'cty_core') continue;
                    $feName = $row['feName'];
                    $feId = $row['feId'];
                    $valencia[$anId][$feId] = [];
                    $startChar = $row['startChar'];
                    if (($row['itEntry'] == 'int_ini') || ($row['itEntry'] == 'int_dni') || ($row['itEntry'] == 'int_cni') || ($row['itEntry'] == 'int_inc')) { // INC OU NI
                        $valencia[$anId][$feId]['FE'] = $row['feName'];
                        $valencia[$anId][$feId]['PT'] = $row['itName'];
                        $valencia[$anId][$feId]['GF'] = $row['itName'];
                        $valencia[$anId][$feId]['GFSource'] = $row['itName'];
                        $valencia[$anId][$feId]['idLanguage'] = $row['idLanguage'];
                        $startChar = -1;
                        continue;
                    }
                }
                if ($row['layerEntry'] == 'lty_pt') {
                    $l = 'PT';
                } elseif ($row['layerEntry'] == 'lty_gf') {
                    $l = 'GF';
                } elseif ($row['layerEntry'] == 'lty_fe') {
                    $l = 'FE';
                } else {
                    continue;
                }
                if ($l != 'FE') {
                    $name = $row['glName'];
                    if ($valencia[$anId][$feId]['FE'] != '') {
                        $valencia[$anId][$feId][$l] = $this->getEquivalentLabel($l, $name, $idLanguage);
                        if ($l == 'GF') {
                            $valencia[$anId][$feId]['GFSource'] = $name;
                        }
                    }
                } else {
                    $valencia[$anId][$feId]['FE'] = $feName; //$this->getEquivalentLabel($l, $name, $idLanguage);
                }
                $valencia[$anId][$feId]['idLanguage'] = $row['idLanguage'];
            }
        }
        return $valencia;
    }

    public function getValenciaFromTable($idLU, $idLanguage)
    {
        $v = new fnbr\models\ViewFrame();
        $db = $v->getDb();
        $query = "
SELECT v.idPattern, v.idFrameElement, v.GF, v.GFSource, v.PT, e.name feName
from valencepattern v join view_frameelement fe on (v.idFrameElement = fe.idFrameElement)
join entry e on (fe.entry = e.entry)
where (v.idLU = {$idLU}) and (v.idLanguage = {$idLanguage}) and (e.idLanguage = {$idLanguage})
order by v.idPattern, v.idFrameElement
";
        $valencia = [];
        $rows = $db->getQueryCommand($query)->getResult();
        foreach ($rows as $row) {
            $valencia[$row['idPattern']][$row['idFrameElement']] = [
                'FE' => $row['feName'],
                'PT' => $row['PT'],
                'GF' => $row['GF'],
                'GFSource' => $row['GFSource'],
                'idLanguage' => $idLanguage
            ];
        }
        return $valencia;
    }

    public function getEquivalentLabel($type, $label, $idLanguage)
    {
        $value = $label;
        if ($type == 'GF') {
            if ($idLanguage != 2) {
                $value = $this->gfEquivalence[$idLanguage][2][$label];
            }
        }
        if ($type == 'PT') {
            if ($idLanguage != 2) {
                $value = $this->ptEquivalence[$idLanguage][2][$label];
            }
        }
        return $value;
    }

    function compareCombinatorialValencia($valenciaSource, $valenciaTarget, $idLanguage)
    {
        $somaFinal = $totalFinal = 0;
        $scoreParcial = [];
        $score = [];
        foreach ($valenciaTarget as $idPatternTarget => $patternTarget) {
            $somaParcial = $totalParcial = 0;
            foreach ($valenciaSource as $idPatternSource => $patternSource) {
//			var_dump ($vd . ' - ' . $vs);
                $total = $soma = 0;
                $fator1 = (count($patternTarget) == count($patternSource)) ? 1 : 0.75;
                foreach ($patternTarget as $idFETarget => $trioTarget) {
//				var_dump('v1');
                    //mdump($v1);
                    $total = $total + 3;
                    foreach ($patternSource as $idFESource => $trioSource) {
//					var_dump('v2');
                        //mdump($v2);
                        //mdump($trioSource['FE'] . ' -- ' . $trioTarget['FE']);
                        //if ($trioSource['FE'] == $trioTarget['FE']) {
                        if ($idFESource == $idFETarget) {
                            //if (($v2['GF'] == 'CNI') || ($v1['GF'] == 'CNI')) {
                            //	var_dump($v2['GF'] . ' - ' . $v1['GF']);
                            //	var_dump('peso = ' . $peso[$langSource][$langDest][$v2['GF']][$v1['GF']]);
                            //}
                            $soma++;
                            $weight = ($idLanguage == 2) ? 1 : $this->weight[$idLanguage][$trioSource['GFSource']];
                            $soma += ($trioSource['GF'] == $trioTarget['GF']) ? 1 * $weight : 0;
                            $soma += ($trioSource['PT'] == $trioTarget['PT']) ? 1 : 0;
                            //mdump('soma = ' . $soma);
                        }
                    }
                    //mdump ('  total = '. $total . '  soma = ' . $soma);
                }
//			var_dump ($vd . ' - ' . $vs . '  total = '. $total . '  soma = ' . $soma);
                $scoreParcial[$idPatternTarget][$idPatternSource] = ($soma / $total) * $fator1;
                $somaParcial += $scoreParcial[$idPatternTarget][$idPatternSource];
                $totalParcial++;
                //          mdump($scoreParcial);
                //mdump($idAnnotationSetTarget . ' - ' . $idAnnotationSetSource . ' - ' . $somaParcial . ' - ' . $totalParcial);
            }
            $score[$idPatternTarget] = $somaParcial / $totalParcial;
            $somaFinal += $score[$idPatternTarget];
            $totalFinal++;
        }
        //dumpScoreParcial($luSource, $luDest, $scoreParcial);
        $media = $scoreFinal = $somaFinal / $totalFinal;
        $soma = 0;
        $count = 0;
        foreach ($scoreParcial as $vt) {
            foreach ($vt as $vs) {
                $soma += pow($vs - $media, 2);
                $count++;
            }
        }
        $variancia = $soma / $count;
        //var_dump('Score Parcial por valencia');
        //var_dump($score);
        //var_dump('Score Final (Media)');
        //mdump($scoreFinal);
        //mdump(['score' => $scoreFinal, 'variancia' => $variancia]);
        return ['score' => number_format($scoreFinal, 3), 'variancia' => number_format($variancia, 3)];
    }

    function compareMaximumValencia($valenciaSource, $valenciaTarget, $idLanguage)
    {
        $somaFinal = $totalFinal = 0;
        $scoreParcial = [];
        $score = [];
        foreach ($valenciaTarget as $idAnnotationSetTarget => $patternTarget) {
            $maximum = 0;
            foreach ($valenciaSource as $idAnnotationSetSource => $patternSource) {
                $total = $soma = 0;
                $fator1 = (count($patternTarget) == count($patternSource)) ? 1 : 0.75;
                foreach ($patternTarget as $idFETarget => $trioTarget) {
                    $total = $total + 3;
                    foreach ($patternSource as $idFESource => $trioSource) {
                        if ($idFESource == $idFETarget) {
                            $soma++;
                            $weight = ($idLanguage == 2) ? 1 : $this->weight[$idLanguage][$trioSource['GFSource']];
                            $soma += ($trioSource['GF'] == $trioTarget['GF']) ? 1 * $weight : 0;
                            $soma += ($trioSource['PT'] == $trioTarget['PT']) ? 1 : 0;
                        }
                    }
                }
                $comp = ($soma / $total) * $fator1;
                if ($comp > $maximum) {
                    $maximum = $comp;
                }
                $scoreParcial[$idAnnotationSetTarget][$idAnnotationSetSource] = $comp;
            }
            //mdump($maximum);
            $score[$idAnnotationSetTarget] = $maximum;
            $somaFinal += $score[$idAnnotationSetTarget];
            $totalFinal++;
        }
        //dumpScoreParcial($luSource, $luDest, $scoreParcial);
        $media = $scoreFinal = $somaFinal / $totalFinal;
        $soma = 0;
        $count = 0;
        foreach ($scoreParcial as $vt) {
            foreach ($vt as $vs) {
                $soma += pow($vs - $media, 2);
                $count++;
            }
        }
        $variancia = $soma / $count;
        //var_dump('Score Parcial por valencia');
        //var_dump($score);
        //var_dump('Score Final (Media)');
//	var_dump($scoreFinal);
        //mdump(['score' => $scoreFinal, 'variancia' => $variancia]);
        return ['score' => number_format($scoreFinal, 3), 'variancia' => number_format($variancia, 3)];
    }

}
