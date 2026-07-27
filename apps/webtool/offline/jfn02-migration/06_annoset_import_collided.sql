-- Import the 2 annotation sets missed by the ID-keyed comparison.
-- jfn02 374827 / 374828 (both LU 29148 取る.v, Manipulation) collide with unrelated
-- fnjp_db rows of the same id, so they are given fresh ids from the 400000 block.
--
-- Expected: 2 annotationsets, 14 layers, 15 labels.

CREATE TEMPORARY TABLE as_map (jfn02_as INT UNSIGNED PRIMARY KEY, fnjp_as INT UNSIGNED NOT NULL UNIQUE);
INSERT INTO as_map VALUES (374827, 400000), (374828, 400001);

SET @lbase := (SELECT MAX(idLayer) FROM fnjp_db.layer);

CREATE TEMPORARY TABLE layer_map2 (
  jfn02_layer INT UNSIGNED PRIMARY KEY,
  fnjp_layer  INT UNSIGNED NOT NULL UNIQUE
);
INSERT INTO layer_map2 (jfn02_layer, fnjp_layer)
SELECT ly.ID, @lbase + ROW_NUMBER() OVER (ORDER BY ly.ID)
FROM jfn02.layer ly
JOIN as_map m ON m.jfn02_as = ly.AnnotationSet_Ref;

START TRANSACTION;

INSERT INTO fnjp_db.annotationset
  (idAnnotationSet, idSubCorpus, idSentence, idAnnotationStatus, idEntityRelated)
SELECT m.fnjp_as, NULL, j.Sentence_Ref,
       CASE j.CurrentAnnoStatus_Ref WHEN 1 THEN 5 WHEN 2 THEN 6 END,
       flu.idEntity
FROM jfn02.annotationset j
JOIN as_map m ON m.jfn02_as = j.ID
JOIN fnjp_db.lu flu ON flu.idLU = j.LexUnit_Ref;

INSERT INTO fnjp_db.layer (idLayer, `rank`, idAnnotationSet, idLayerType)
SELECT lm.fnjp_layer, ly.Rank, am.fnjp_as, ly.LayerType_Ref
FROM jfn02.layer ly
JOIN layer_map2 lm ON lm.jfn02_layer = ly.ID
JOIN as_map    am ON am.jfn02_as    = ly.AnnotationSet_Ref;

INSERT INTO fnjp_db.label
  (startChar, endChar, multi, idLabelType, idLayer, idInstantiationType)
SELECT lb.StartChar, lb.EndChar, 0,
       COALESCE(ffe.idEntity, fgl.idEntity),
       lm.fnjp_layer,
       lb.InstantiationType_Ref + 11
FROM jfn02.label lb
JOIN layer_map2 lm ON lm.jfn02_layer = lb.Layer_Ref
JOIN jfn02.labeltype lt ON lt.ID = lb.LabelType_Ref
LEFT JOIN fnjp_db.frameelement ffe
       ON lt.DBTableName = 'FrameElement' AND ffe.idFrameElement = lt.DBTableID
LEFT JOIN fnjp_db.genericlabel fgl
       ON lt.DBTableName = 'MiscLabel'    AND fgl.idGenericLabel = lt.DBTableID;

COMMIT;
