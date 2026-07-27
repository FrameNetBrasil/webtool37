-- Additive half of the lu sync: 11 inserts (+ entity rows) and 16 idLemma corrections.
-- No rows are removed by this script.

START TRANSACTION;

-- 1. Entity rows for the 11 new LUs, using the bulk-migration convention
--    (alias 'lu_<idLU>', type 'LU', idOld = idLU) used by 4896/4897 existing LUs.
INSERT INTO fnjp_db.entity (alias, type, idOld)
SELECT CONCAT('lu_', j.ID), 'LU', j.ID
FROM jfn02.lexunit j
LEFT JOIN fnjp_db.lu f ON f.idLU = j.ID
WHERE f.idLU IS NULL
ORDER BY j.ID;

-- 2. The 11 LU rows, with idLU mirroring jfn02.lexunit.ID.
INSERT INTO fnjp_db.lu
  (idLU, name, senseDescription, active, importNum, incorporatedFE, bff, bffOther, idEntity, idLemma, idFrame, idForm)
SELECT j.ID,
       CONVERT(j.Name USING utf8mb4),
       CONVERT(IFNULL(j.SenseDesc, '') USING utf8mb4),
       1, j.ImportNum, j.IncorporatedFE, NULL, NULL,
       e.idEntity, j.Lemma_Ref, j.Frame_Ref, NULL
FROM jfn02.lexunit j
JOIN fnjp_db.entity e ON e.alias = CONCAT('lu_', j.ID) AND e.type = 'LU'
LEFT JOIN fnjp_db.lu f ON f.idLU = j.ID
WHERE f.idLU IS NULL;

-- 3. Repoint the 16 divergent idLemma values to the jfn02 lemma IDs.
UPDATE fnjp_db.lu f
JOIN jfn02.lexunit j ON j.ID = f.idLU
SET f.idLemma = j.Lemma_Ref
WHERE f.idLemma <> j.Lemma_Ref;

COMMIT;
