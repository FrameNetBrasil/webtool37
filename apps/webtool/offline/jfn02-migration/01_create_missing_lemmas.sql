-- Create the 7 lemmas (30985-30991) missing from fnjp_db, mirroring jfn02.lemma IDs.
-- Lexemes and wordforms already exist in fnjp_db and are reused.

START TRANSACTION;

INSERT INTO fnjp_db.lemma (idLemma, name, idOld, version, idPOS, idLanguage, idEntity, idUDPOS)
SELECT lm.ID,
       CONVERT(CONCAT(GROUP_CONCAT(lx.Name ORDER BY le.LexemeOrder SEPARATOR ' '),
                      '.', LOWER(p.POS)) USING utf8mb4),
       NULL, NULL, lm.PartOfSpch_Ref, 8, NULL, NULL
FROM jfn02.lemma lm
JOIN jfn02.lexemeentry le ON le.Lemma_Ref = lm.ID
JOIN jfn02.lexeme      lx ON lx.ID        = le.Lexeme_Ref
JOIN fnjp_db.pos       p  ON p.idPOS      = lm.PartOfSpch_Ref
WHERE lm.ID IN (30985,30986,30987,30988,30989,30990,30991)
GROUP BY lm.ID, lm.PartOfSpch_Ref, p.POS;

INSERT INTO fnjp_db.lexemeentry (lexemeOrder, breakBefore, headWord, idLexeme, idLemma, idWordForm)
SELECT le.LexemeOrder, le.BreakBefore, le.Headword, le.Lexeme_Ref, le.Lemma_Ref, NULL
FROM jfn02.lexemeentry le
WHERE le.Lemma_Ref IN (30985,30986,30987,30988,30989,30990,30991)
ORDER BY le.Lemma_Ref, le.LexemeOrder;

COMMIT;
