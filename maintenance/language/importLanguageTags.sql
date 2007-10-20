--- Import Language Tags

--- Merge ISO639, RFC4646 AND Wikimedia
INSERT INTO /*$wgDBPrefix*/langtags (native_name,wikimedia_key,
  english_name,iso639,iso639_3,iso639_3_revision,
  rfc4646,rfc4646_added,rfc4646_suppress,rfc4646_deprecated)

SELECT langtags_wikimedia.native_name,langtags_wikimedia.wikimedia_key,
  langtags_iso639.english_name,langtags_iso639.iso639,langtags_iso639.iso639_3,langtags_iso639.iso639_3_revision,
  langtags_rfc4646.tag,langtags_rfc4646.added,langtags_rfc4646.suppress_script,langtags_rfc4646.deprecated
  FROM
  /*$wgDBPrefix*/langtags_wikimedia AS langtags_wikimedia,
  /*$wgDBPrefix*/langtags_iso639 AS langtags_iso639,
  /*$wgDBPrefix*/langtags_rfc4646 AS langtags_rfc4646

  WHERE
  (langtags_wikimedia.wikimedia_key = langtags_iso639.tag OR langtags_wikimedia.wikimedia_key=langtags_iso639.iso639)
  AND langtags_wikimedia.wikimedia_key = langtags_rfc4646.tag
;


--- Merge ISO639 AND RFC4646
INSERT INTO /*$wgDBPrefix*/langtags (native_name,wikimedia_key,
  english_name,iso639,iso639_3,iso639_3_revision,
  rfc4646,rfc4646_added,rfc4646_suppress,rfc4646_deprecated)

SELECT null,null,
  langtags_iso639.english_name,langtags_iso639.iso639,langtags_iso639.iso639_3,langtags_iso639.iso639_3_revision,
  langtags_rfc4646.tag,langtags_rfc4646.added,langtags_rfc4646.suppress_script,langtags_rfc4646.deprecated
  FROM
  /*$wgDBPrefix*/langtags_iso639 AS langtags_iso639,
  /*$wgDBPrefix*/langtags_rfc4646 AS langtags_rfc4646

  WHERE
  (langtags_iso639.tag = langtags_rfc4646.tag OR langtags_iso639.iso639 = langtags_rfc4646.tag)
  AND NOT EXISTS (SELECT * FROM /*$wgDBPrefix*/langtags WHERE rfc4646 = langtags_rfc4646.tag)
;

--- Merge RFC4646 AND Wikimedia
INSERT INTO /*$wgDBPrefix*/langtags (native_name,wikimedia_key,
  rfc4646,rfc4646_added,rfc4646_suppress,rfc4646_deprecated,
  english_name)

SELECT langtags_wikimedia.native_name,langtags_wikimedia.wikimedia_key,
  langtags_rfc4646.tag,langtags_rfc4646.added,langtags_rfc4646.suppress_script,langtags_rfc4646.deprecated,
  langtags_rfc4646.description
  FROM
  /*$wgDBPrefix*/langtags_wikimedia AS langtags_wikimedia,
  /*$wgDBPrefix*/langtags_rfc4646 AS langtags_rfc4646

  WHERE
  langtags_wikimedia.wikimedia_key = langtags_rfc4646.tag
  AND NOT EXISTS (SELECT * FROM /*$wgDBPrefix*/langtags WHERE rfc4646 = langtags_rfc4646.tag)
;

--- Merge ISO639 AND Wikimedia
INSERT INTO /*$wgDBPrefix*/langtags (native_name,wikimedia_key,
  english_name,iso639,iso639_3,iso639_3_revision,
  rfc4646)

SELECT langtags_wikimedia.native_name,langtags_wikimedia.wikimedia_key,
  langtags_iso639.english_name,langtags_iso639.iso639,langtags_iso639.iso639_3,langtags_iso639.iso639_3_revision,
  langtags_iso639.tag
  FROM
  /*$wgDBPrefix*/langtags_wikimedia AS langtags_wikimedia,
  /*$wgDBPrefix*/langtags_iso639 AS langtags_iso639

  WHERE
  (langtags_wikimedia.wikimedia_key = langtags_iso639.tag OR langtags_wikimedia.wikimedia_key=langtags_iso639.iso639)
  AND NOT EXISTS (SELECT * FROM /*$wgDBPrefix*/langtags WHERE rfc4646 = langtags_wikimedia.wikimedia_key)
;

--- Append ISO639
INSERT INTO /*$wgDBPrefix*/langtags (native_name,wikimedia_key,
  english_name,iso639,iso639_3,iso639_3_revision,
  rfc4646)

SELECT null,null,
  langtags_iso639.english_name,langtags_iso639.iso639,langtags_iso639.iso639_3,langtags_iso639.iso639_3_revision,
  langtags_iso639.tag
  FROM
  /*$wgDBPrefix*/langtags_iso639 AS langtags_iso639

  WHERE
  NOT EXISTS (SELECT * FROM /*$wgDBPrefix*/langtags WHERE iso639 = langtags_iso639.iso639 OR rfc4646 = langtags_iso639.tag)
;

--- Append RFC4646
INSERT INTO /*$wgDBPrefix*/langtags (native_name,wikimedia_key,
  rfc4646,rfc4646_added,rfc4646_suppress,rfc4646_deprecated,
  english_name)

SELECT null,null,
  langtags_rfc4646.tag,langtags_rfc4646.added,langtags_rfc4646.suppress_script,langtags_rfc4646.deprecated,
  langtags_rfc4646.description
  FROM
  /*$wgDBPrefix*/langtags_rfc4646 AS langtags_rfc4646

  WHERE
  NOT EXISTS (SELECT * FROM /*$wgDBPrefix*/langtags WHERE rfc4646 = langtags_rfc4646.tag)
;

--- Append Wikimedia Codes
INSERT INTO /*$wgDBPrefix*/langtags (native_name, wikimedia_key)

SELECT langtags_wikimedia.native_name,langtags_wikimedia.wikimedia_key
  FROM
  /*$wgDBPrefix*/langtags_wikimedia AS langtags_wikimedia

  WHERE
  NOT EXISTS (SELECT * FROM /*$wgDBPrefix*/langtags WHERE wikimedia_key = langtags_wikimedia.wikimedia_key)
;

--- Test Suite
SELECT count(*) FROM /*$wgDBPrefix*/langtags_rfc4646 WHERE NOT EXISTS (SELECT * FROM /*$wgDBPrefix*/langtags WHERE rfc4646 = tag);
SELECT count(*) FROM /*$wgDBPrefix*/langtags_iso639 WHERE NOT EXISTS (SELECT * FROM /*$wgDBPrefix*/langtags WHERE iso639=langtags.iso639);
SELECT count(*) FROM /*$wgDBPrefix*/langtags_wikimedia WHERE NOT EXISTS (SELECT * FROM /*$wgDBPrefix*/langtags WHERE wikimedia_key=langtags_wikimedia.wikimedia_key);

UPDATE /*$wgDBPrefix*/langtags SET tag_name = coalesce(iso639_3,iso639,rfc4646,wikimedia_key,tag_name) where wikimedia_key <> 'zh-yue';
UPDATE /*$wgDBPrefix*/langtags SET display_name = coalesce(native_name,english_name,display_name);
UPDATE /*$wgDBPrefix*/langtags SET language_id = 0 WHERE iso639='mul';
UPDATE /*$wgDBPrefix*/langtags SET is_enabled = 1 where iso639 IS NOT NULL;

