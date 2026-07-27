-- Final half of the lu sync: remove the 6 LUs that exist only in fnjp_db,
-- together with their annotation sets, layers and labels.
-- User-approved (option "Delete all 6 + annotations").
-- Pre-delete backup: fnjp_db_predelete_backup_20260727_083855.sql
--
-- LUs      : 21772, 26577, 26622, 27223, 29069, 30789
-- Entities : 238330, 240813, 240849, 241356, 242949, 267444
-- Expected : 48 labels, 77 layers, 11 annotationsets, 6 lu, 6 entity

START TRANSACTION;

DELETE lb FROM fnjp_db.label lb
JOIN fnjp_db.layer ly         ON ly.idLayer = lb.idLayer
JOIN fnjp_db.annotationset a  ON a.idAnnotationSet = ly.idAnnotationSet
WHERE a.idEntityRelated IN (238330,240813,240849,241356,242949,267444);

DELETE ly FROM fnjp_db.layer ly
JOIN fnjp_db.annotationset a ON a.idAnnotationSet = ly.idAnnotationSet
WHERE a.idEntityRelated IN (238330,240813,240849,241356,242949,267444);

DELETE FROM fnjp_db.annotationset
WHERE idEntityRelated IN (238330,240813,240849,241356,242949,267444);

DELETE FROM fnjp_db.lu
WHERE idLU IN (21772,26577,26622,27223,29069,30789);

DELETE FROM fnjp_db.entity
WHERE idEntity IN (238330,240813,240849,241356,242949,267444) AND type = 'LU';

COMMIT;
