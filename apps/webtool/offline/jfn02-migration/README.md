# jfn02 → fnjp_db migration (2026-07-27)

Record of the SQL run to bring `fnjp_db.lu` and `fnjp_db.annotationset` into line with the
legacy `jfn02` database. Scripts are numbered in execution order and were run exactly as
committed here, against MariaDB 12.2 with both schemas on the same server.

```sh
mariadb -h <host> -P 3306 -u <user> -p -D fnjp_db --default-character-set=utf8mb4 \
        < 01_create_missing_lemmas.sql
```

Both flags matter:

- `--default-character-set=utf8mb4` — `jfn02` is utf8mb3 and `fnjp_db` is utf8mb4; without
  it the Japanese text is mangled.
- `-D fnjp_db` — multi-table `DELETE ... JOIN` fails with "No database selected" without a
  default schema.

**Take a `mysqldump` of the affected tables before each step.** Every script is wrapped in
a transaction, but the deletes are not recoverable once committed.

## Steps

| # | Script | Effect |
|---|---|---|
| 01 | `01_create_missing_lemmas.sql` | 7 lemmas (ids 30985–30991, mirroring `jfn02.lemma.ID`) + 26 `lexemeentry` rows |
| 02 | `02_lu_import_and_lemma_fix.sql` | 11 `entity` + 11 `lu` rows (ids from `jfn02.lexunit.ID`); repoints 16 divergent `lu.idLemma` |
| 03 | `03_lu_delete_fnjp_only.sql` | Deletes 6 fnjp-only LUs + their entities, annotation sets, layers, labels |
| 04 | `04_annoset_delete_superseded.sql` | Deletes 11 annotation sets jfn02 superseded by re-annotating |
| 05 | `05_annoset_import_38.sql` | Imports 38 annotation sets / 266 layers / 167 labels; resets `annotationset` AUTO_INCREMENT to 400000 |
| 06 | `06_annoset_import_collided.sql` | Imports 2 more sets whose jfn02 ids were occupied in fnjp_db, under new ids 400000/400001 |
| 07 | `07_annoset_delete_surplus.sql` | Deletes the remaining 97 surplus fnjp-only annotation sets |

End state: `lu` = 4902 rows matching `jfn02.lexunit` with zero field differences;
`annotationset` = 9489 rows matching jfn02's LU-linked sets, with matching multiplicity
across all 9293 distinct (LU, sentence) pairs.

## Mapping notes

Things that are not obvious from the schemas and that any future sync must respect.

**`label.idLabelType` holds the `idEntity` of the FrameElement or GenericLabel — not its
primary key.** The column comment says "Reference to FrameElement or GenericLabel" but the
FK is `fk_Label_Entity → entity(idEntity)`. jfn02 reaches the same target through its
polymorphic `labeltype` table; both branches are plain id mirrors:

| `jfn02.labeltype.DBTableName` | resolution |
|---|---|
| `FrameElement` | `DBTableID` → `fnjp_db.frameelement.idFrameElement` → `.idEntity` |
| `MiscLabel` | `DBTableID` → `fnjp_db.genericlabel.idGenericLabel` → `.idEntity` |

Do **not** match generic labels by name — names are not unique (`NP` occurs 6 times across
languages).

**Ids that mirror between the databases:** `lexunit.ID`↔`lu.idLU`, `lemma.ID`↔`idLemma`,
`lexeme.ID`↔`idLexeme`, `frame.ID`↔`idFrame`, `frameelement.ID`↔`idFrameElement`,
`misclabel.ID`↔`idGenericLabel`, `layertype.ID`↔`idLayerType`.

**Ids that do NOT mirror:** `lexemeentry`, `layer`, `label` (independent sequences), and —
critically — `annotationset`. Both databases kept allocating annotation-set ids from their
own AUTO_INCREMENT after they diverged, so the same id can denote different annotations on
each side. **Compare annotation sets on (LU, sentence), never on id.** Two ids (374827,
374828) collided this way; step 06 exists solely to fix that, and step 05 moves
`fnjp_db.annotationset` AUTO_INCREMENT to 400000 to stop it recurring.

**Value mappings:** annotation status `1 UNANN → 5 ast_unann`, `2 MANUAL → 6 ast_manual`;
instantiation type is a `+11` offset (`1 Normal→12`, `2 APos→13`, `3 CNI→14`, `4 DNI→15`,
`5 INI→16`, `6 INC→17`). jfn02's type `7 DEN` has **no** fnjp counterpart — guard for it.
`label.Multi 'N' → 0`. `annotationset.idSubCorpus` is set NULL because `fnjp_db.subcorpus`
is empty.

## Re-running elsewhere

Steps 01–06 are derived from queries and are safe to re-run against another copy: they
select what is missing rather than hard-coding it.

**Step 07 is not.** It hard-codes the 97 ids computed from the database state at the time.
Recompute the list for any other target before running it:

```sql
WITH fn AS (
  SELECT a.idAnnotationSet aset, lu.idLU lu, a.idSentence sent
  FROM fnjp_db.annotationset a JOIN fnjp_db.lu lu ON lu.idEntity = a.idEntityRelated),
jf AS (
  SELECT j.ID aset, j.LexUnit_Ref lu, j.Sentence_Ref sent
  FROM jfn02.annotationset j WHERE j.LexUnit_Ref <> 0 AND j.LexUnit_Ref IS NOT NULL),
jcnt AS (SELECT lu, sent, COUNT(*) n FROM jf GROUP BY lu, sent),
ranked AS (
  SELECT fn.*, IFNULL(jc.n, 0) keep_n,
    ROW_NUMBER() OVER (PARTITION BY fn.lu, fn.sent
      ORDER BY (EXISTS(SELECT 1 FROM jf
                       WHERE jf.aset = fn.aset AND jf.lu = fn.lu AND jf.sent = fn.sent)) DESC,
               fn.aset DESC) rn
  FROM fn LEFT JOIN jcnt jc ON jc.lu = fn.lu AND jc.sent = fn.sent)
SELECT GROUP_CONCAT(aset ORDER BY aset) FROM ranked WHERE rn > keep_n;
```

It keeps `min(fnjp_count, jfn02_count)` rows per (LU, sentence) pair, preferring rows whose
id also matches jfn02, and deletes the surplus.

## Not addressed

- jfn02's 52520 annotation sets with no LexUnit or Construction ref — empty per-sentence
  placeholders (39 layers, 134 labels between all of them), never migrated.
- `fnjp_db.subcorpus` is empty (0 rows vs jfn02's 4496), so every `idSubCorpus` is NULL.
- Pre-existing POS divergence: 248 lexemes and 14 lemmas differ on POS between the schemas.
- Pre-existing fnjp artifact: some annotation sets carry duplicated layer sets (the same 7
  layer types repeated).
- `fnjp_db` has no `int_den` instantiation type.
