<?php

namespace fnbr\models;

class Valence
{

    public function FERealizations($idLU)
    {
        $cmd = <<<HERE
select a.idAnnotationSet, lb.startChar, lb.endChar, l.entry layerEntry, entry_it.entry itEntry, entry_it.name itName, entry_fe.entry feEntry,
entry_fe.name feName, fe.idFRameElement feId, fe.typeEntry feTypeEntry, gf.name gfName, pt.name ptName
FROM view_lu lu join view_subcorpuslu v1 on (lu.idLU = v1.idLU)
join View_AnnotationSet a on (v1.idSubCorpus = a.idSubCorpus)
join View_Layer l on (a.idAnnotationSet = l.idAnnotationSet)
join Label lb on (l.idLayer = lb.idLayer)
join View_InstantiationType it on (lb.idInstantiationType = it.idTypeInstance)
join Entry entry_it on (it.entry = entry_it.entry)
left join View_FrameElement fe on (lb.idLabelType = fe.idEntity)
left join Entry entry_fe on (fe.entry = entry_fe.entry)
left join (
select a1.idAnnotationset, lb1.startchar, gl1.name name
from View_AnnotationSet a1
join View_Layer l1 on (a1.idAnnotationSet = l1.idAnnotationSet)
join Label lb1 on (l1.idLayer = lb1.idLayer)
left join GenericLabel gl1 on (lb1.idLabelType = gl1.IdEntity)
where ((gl1.name is null) or (gl1.name <> 'Target'))
and (l1.entry in ('lty_gf'))
) gf on ((a.idAnnotationSet = gf.idAnnotationSet) and (lb.startChar = gf.startchar))
left join (
select a2.idAnnotationset, lb2.startchar, gl2.name name
from View_AnnotationSet a2
join View_Layer l2 on (a2.idAnnotationSet = l2.idAnnotationSet)
join Label lb2 on (l2.idLayer = lb2.idLayer)
left join GenericLabel gl2 on (lb2.idLabelType = gl2.IdEntity)
where ((gl2.name is null) or (gl2.name <> 'Target'))
and (l2.entry in ('lty_pt'))
) pt on ((a.idAnnotationSet = pt.idAnnotationSet) and (lb.startChar = pt.startchar))
where (lu.idLU = {$idLU})
and (entry_it.idLanguage = lu.idlanguage)
and ((entry_fe.idLanguage is null) or (entry_fe.idLanguage = lu.idLanguage))
and (l.entry in ('lty_fe'))
order by a.idAnnotationSet, lb.startChar, lb.endChar, l.layerOrder, l.entry
HERE;

        $query = \Manager::getDatabase(\Manager::getConf('fnbr.db'))->getQueryCommand($cmd);
        return $query;

    }

}
