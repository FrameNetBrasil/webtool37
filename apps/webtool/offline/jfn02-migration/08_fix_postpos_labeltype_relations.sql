-- Repoint the 11 Japanese postposition GenericLabels from lty_cee to lty_postpos.
--
-- The rel_haslabeltype relations for these label types had idEntity1 = 1291
-- (layertype lty_cee, idLayerType 19) instead of 218481 (lty_postpos,
-- idLayerType 24). AnnotationSet::getLabelTypesGL() reaches label types by
-- joining LayerType -> GenericLabel over rel_haslabeltype, so none of the 11
-- reached the annotation.labelTypes JS map, and annotation.cellStyler
-- (modules/annotation/views/main/layers/formatter.js) threw
-- "Cannot read properties of undefined (reading 'idColor')" as soon as a
-- Postpos cell was styled -- aborting the whole EasyUI datagrid render.
-- Only ノ (220295) was linked correctly, so the layer appeared to work until a
-- sentence used any of the others.
--
-- The genericlabel rows themselves were intact (name, idColor, idLanguage = 8);
-- only idEntity1 on entityrelation was wrong. lty_cee keeps its own legitimate
-- CEE label types (3147 pt, 1520 en), which are NOT touched here.
--
-- 220270 ガ / 220271 ヲ / 220272 ニ / 220273 ヘ / 220274 デ / 220283 ハ
-- 220284 カラ / 220285 ト / 220286 マデ / 220287 モ / 220288 ヨリ
--
-- Affected entityrelation ids: 67296-67300, 67309-67314.
-- Expected: 11 entityrelation rows updated.

START TRANSACTION;

UPDATE fnjp_db.entityrelation er
JOIN fnjp_db.relationtype rt ON rt.idRelationType = er.idRelationType
SET er.idEntity1 = 218481
WHERE rt.entry = 'rel_haslabeltype'
  AND er.idEntity1 = 1291
  AND er.idEntity2 IN
    (220270,220271,220272,220273,220274,220283,220284,220285,220286,220287,220288);

COMMIT;

-- Verification: expect 12 rows for lty_postpos (the 11 above plus 220295 ノ),
-- and only the pt/en CEE pair left on lty_cee.
--
-- SELECT er.idEntity1, lt.entry, gl.idEntity, gl.idLanguage, gl.name
-- FROM fnjp_db.entityrelation er
-- JOIN fnjp_db.relationtype rt ON rt.idRelationType = er.idRelationType
--                             AND rt.entry = 'rel_haslabeltype'
-- JOIN fnjp_db.genericlabel gl ON gl.idEntity = er.idEntity2
-- JOIN fnjp_db.layertype lt ON lt.idEntity = er.idEntity1
-- WHERE er.idEntity1 IN (1291, 218481)
-- ORDER BY er.idEntity1, gl.idEntity;
