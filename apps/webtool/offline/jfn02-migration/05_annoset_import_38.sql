-- Steps 3-6: import 38 annotation sets, 266 layers, 167 labels from jfn02; reset AUTO_INCREMENT.
--
-- Layer ids are assigned explicitly from a deterministic block above the current MAX(idLayer),
-- rather than relying on AUTO_INCREMENT ordering, so the jfn02->fnjp layer mapping is exact
-- and reproducible for verification.

-- The 38 target annosets, captured BEFORE any insert.
CREATE TEMPORARY TABLE t_annoset (id INT UNSIGNED PRIMARY KEY);
INSERT INTO t_annoset
SELECT j.ID FROM jfn02.annotationset j
LEFT JOIN fnjp_db.annotationset f ON f.idAnnotationSet = j.ID
WHERE j.LexUnit_Ref <> 0 AND j.LexUnit_Ref IS NOT NULL AND f.idAnnotationSet IS NULL;

SELECT COUNT(*) AS target_annosets FROM t_annoset;

SET @base := (SELECT MAX(idLayer) FROM fnjp_db.layer);
SELECT @base AS layer_id_base;

CREATE TEMPORARY TABLE layer_map (
  jfn02_layer INT UNSIGNED PRIMARY KEY,
  fnjp_layer  INT UNSIGNED NOT NULL UNIQUE
);
INSERT INTO layer_map (jfn02_layer, fnjp_layer)
SELECT ly.ID, @base + ROW_NUMBER() OVER (ORDER BY ly.ID)
FROM jfn02.layer ly
JOIN t_annoset t ON t.id = ly.AnnotationSet_Ref;

START TRANSACTION;

-- Step 3: annotation sets (ids preserved from jfn02)
INSERT INTO fnjp_db.annotationset
  (idAnnotationSet, idSubCorpus, idSentence, idAnnotationStatus, idEntityRelated)
SELECT j.ID, NULL, j.Sentence_Ref,
       CASE j.CurrentAnnoStatus_Ref WHEN 1 THEN 5 WHEN 2 THEN 6 END,
       flu.idEntity
FROM jfn02.annotationset j
JOIN t_annoset t ON t.id = j.ID
JOIN fnjp_db.lu flu ON flu.idLU = j.LexUnit_Ref;

-- Step 4: layers
INSERT INTO fnjp_db.layer (idLayer, `rank`, idAnnotationSet, idLayerType)
SELECT m.fnjp_layer, ly.Rank, ly.AnnotationSet_Ref, ly.LayerType_Ref
FROM jfn02.layer ly
JOIN layer_map m ON m.jfn02_layer = ly.ID;

-- Step 5: labels, resolving jfn02's polymorphic labeltype to the FE/GL entity id
INSERT INTO fnjp_db.label
  (startChar, endChar, multi, idLabelType, idLayer, idInstantiationType)
SELECT lb.StartChar, lb.EndChar, 0,
       COALESCE(ffe.idEntity, fgl.idEntity),
       m.fnjp_layer,
       lb.InstantiationType_Ref + 11
FROM jfn02.label lb
JOIN layer_map m ON m.jfn02_layer = lb.Layer_Ref
JOIN jfn02.labeltype lt ON lt.ID = lb.LabelType_Ref
LEFT JOIN fnjp_db.frameelement ffe
       ON lt.DBTableName = 'FrameElement' AND ffe.idFrameElement = lt.DBTableID
LEFT JOIN fnjp_db.genericlabel fgl
       ON lt.DBTableName = 'MiscLabel'    AND fgl.idGenericLabel = lt.DBTableID;

COMMIT;

-- Step 6: move AUTO_INCREMENT clear of jfn02's id range (jfn02 max = 374867)
ALTER TABLE fnjp_db.annotationset AUTO_INCREMENT = 400000;
