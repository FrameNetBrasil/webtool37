-- Step 2: delete the 11 fnjp-only annotation sets that jfn02 superseded by re-annotating.
-- 144351 and 367488 are deliberately EXCLUDED (they exist in jfn02 too).
-- Expected: 89 labels, 124 layers, 11 annotationsets.

START TRANSACTION;

DELETE lb FROM fnjp_db.label lb
JOIN fnjp_db.layer ly ON ly.idLayer = lb.idLayer
WHERE ly.idAnnotationSet IN
  (374812,374814,374817,366368,367433,366296,365851,365965,367802,371218,367487);

DELETE ly FROM fnjp_db.layer ly
WHERE ly.idAnnotationSet IN
  (374812,374814,374817,366368,367433,366296,365851,365965,367802,371218,367487);

DELETE FROM fnjp_db.annotationset
WHERE idAnnotationSet IN
  (374812,374814,374817,366368,367433,366296,365851,365965,367802,371218,367487);

COMMIT;
