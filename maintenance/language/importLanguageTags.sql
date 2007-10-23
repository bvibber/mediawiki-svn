--- Import Language Tags

--- Merge ISO639, RFC4646 AND Wikimedia
INSERT INTO /*$wgDBprefix*/langtags (native_name,wikimedia_key,
  english_name,iso639,iso639_3,iso639_3_revision,
  rfc4646,rfc4646_added,rfc4646_suppress,rfc4646_deprecated)

SELECT langtags_wikimedia.native_name,langtags_wikimedia.wikimedia_key,
  langtags_iso639.english_name,langtags_iso639.iso639,langtags_iso639.iso639_3,langtags_iso639.iso639_3_revision,
  langtags_rfc4646.tag,langtags_rfc4646.added,langtags_rfc4646.suppress_script,langtags_rfc4646.deprecated
  FROM
  /*$wgDBprefix*/langtags_wikimedia AS langtags_wikimedia,
  /*$wgDBprefix*/langtags_iso639 AS langtags_iso639,
  /*$wgDBprefix*/langtags_rfc4646 AS langtags_rfc4646

  WHERE
 (langtags_wikimedia.wikimedia_key = langtags_iso639.tag OR langtags_wikimedia.wikimedia_key=langtags_iso639.iso639)
--  Too slow in MySQL
--  AND (langtags_wikimedia.wikimedia_key = langtags_rfc4646.tag OR langtags_iso639.tag = langtags_rfc4646.tag)
 AND (
  langtags_wikimedia.wikimedia_key = langtags_rfc4646.tag
  OR ( wikimedia_key = 'wuu' and langtags_rfc4646.tag='zh-wuu' )
  OR ( wikimedia_key = 'yue' and langtags_rfc4646.tag='zh-yue' )
 )
;

--- Merge ISO639 AND RFC4646
INSERT INTO /*$wgDBprefix*/langtags (native_name,wikimedia_key,
  english_name,iso639,iso639_3,iso639_3_revision,
  rfc4646,rfc4646_added,rfc4646_suppress,rfc4646_deprecated)

SELECT null,null,
  langtags_iso639.english_name,langtags_iso639.iso639,langtags_iso639.iso639_3,langtags_iso639.iso639_3_revision,
  langtags_rfc4646.tag,langtags_rfc4646.added,langtags_rfc4646.suppress_script,langtags_rfc4646.deprecated
  FROM
  /*$wgDBprefix*/langtags_iso639 AS langtags_iso639,
  /*$wgDBprefix*/langtags_rfc4646 AS langtags_rfc4646

  WHERE
  (langtags_iso639.tag = langtags_rfc4646.tag OR langtags_iso639.iso639 = langtags_rfc4646.tag)
  AND NOT EXISTS (SELECT * FROM /*$wgDBprefix*/langtags WHERE rfc4646 = langtags_rfc4646.tag)
;

--- Merge RFC4646 AND Wikimedia
INSERT INTO /*$wgDBprefix*/langtags (native_name,wikimedia_key,
  rfc4646,rfc4646_added,rfc4646_suppress,rfc4646_deprecated,
  english_name)

SELECT langtags_wikimedia.native_name,langtags_wikimedia.wikimedia_key,
  langtags_rfc4646.tag,langtags_rfc4646.added,langtags_rfc4646.suppress_script,langtags_rfc4646.deprecated,
  langtags_rfc4646.description
  FROM
  /*$wgDBprefix*/langtags_wikimedia AS langtags_wikimedia,
  /*$wgDBprefix*/langtags_rfc4646 AS langtags_rfc4646

  WHERE
  langtags_wikimedia.wikimedia_key = langtags_rfc4646.tag
  AND NOT EXISTS (SELECT * FROM /*$wgDBprefix*/langtags WHERE rfc4646 = langtags_rfc4646.tag)
;

--- Merge ISO639 AND Wikimedia
INSERT INTO /*$wgDBprefix*/langtags (native_name,wikimedia_key,
  english_name,iso639,iso639_3,iso639_3_revision,
  rfc4646)

SELECT langtags_wikimedia.native_name,langtags_wikimedia.wikimedia_key,
  langtags_iso639.english_name,langtags_iso639.iso639,langtags_iso639.iso639_3,langtags_iso639.iso639_3_revision,
  langtags_iso639.tag
  FROM
  /*$wgDBprefix*/langtags_wikimedia AS langtags_wikimedia,
  /*$wgDBprefix*/langtags_iso639 AS langtags_iso639

  WHERE
  (langtags_wikimedia.wikimedia_key = langtags_iso639.tag OR langtags_wikimedia.wikimedia_key=langtags_iso639.iso639)
  AND NOT EXISTS (SELECT * FROM /*$wgDBprefix*/langtags WHERE rfc4646 = langtags_wikimedia.wikimedia_key)
;

--- Append ISO639
INSERT INTO /*$wgDBprefix*/langtags (native_name,wikimedia_key,
  english_name,iso639,iso639_3,iso639_3_revision,
  rfc4646)

SELECT null,null,
  langtags_iso639.english_name,langtags_iso639.iso639,langtags_iso639.iso639_3,langtags_iso639.iso639_3_revision,
  langtags_iso639.tag
  FROM
  /*$wgDBprefix*/langtags_iso639 AS langtags_iso639

  WHERE
  NOT EXISTS (SELECT * FROM /*$wgDBprefix*/langtags WHERE iso639 = langtags_iso639.iso639 OR rfc4646 = langtags_iso639.tag)
;

--- Append RFC4646
INSERT INTO /*$wgDBprefix*/langtags (native_name,wikimedia_key,
  rfc4646,rfc4646_added,rfc4646_suppress,rfc4646_deprecated,
  english_name)

SELECT null,null,
  langtags_rfc4646.tag,langtags_rfc4646.added,langtags_rfc4646.suppress_script,langtags_rfc4646.deprecated,
  langtags_rfc4646.description
  FROM
  /*$wgDBprefix*/langtags_rfc4646 AS langtags_rfc4646

  WHERE
  NOT EXISTS (SELECT * FROM /*$wgDBprefix*/langtags WHERE rfc4646 = langtags_rfc4646.tag)
;

--- Append Wikimedia Codes
INSERT INTO /*$wgDBprefix*/langtags (native_name, wikimedia_key)

SELECT langtags_wikimedia.native_name,langtags_wikimedia.wikimedia_key
  FROM
  /*$wgDBprefix*/langtags_wikimedia AS langtags_wikimedia

  WHERE
  NOT EXISTS (SELECT * FROM /*$wgDBprefix*/langtags WHERE wikimedia_key = langtags_wikimedia.wikimedia_key)
;

--- Test Suite
-- SELECT count(*) FROM /*$wgDBprefix*/langtags_rfc4646 WHERE NOT EXISTS (SELECT * FROM /*$wgDBprefix*/langtags WHERE rfc4646 = tag);
-- SELECT count(*) FROM /*$wgDBprefix*/langtags_iso639 AS langtags_iso639 WHERE NOT EXISTS (SELECT * FROM /*$wgDBprefix*/langtags WHERE iso639=langtags_iso639.iso639);
-- SELECT count(*) FROM /*$wgDBprefix*/langtags_wikimedia AS langtags_wikimedia WHERE NOT EXISTS (SELECT * FROM /*$wgDBprefix*/langtags WHERE wikimedia_key=langtags_wikimedia.wikimedia_key);

UPDATE /*$wgDBprefix*/langtags SET tag_name = coalesce(iso639_3,iso639,rfc4646,wikimedia_key,tag_name) where (wikimedia_key <> 'yue' and wikimedia_key <> 'wuu') or wikimedia_key IS NULL;
UPDATE /*$wgDBprefix*/langtags SET display_name = coalesce(native_name,english_name,display_name);
UPDATE /*$wgDBprefix*/langtags SET language_id = 0 WHERE iso639='mul';
UPDATE /*$wgDBprefix*/langtags SET is_enabled = 1 where iso639 IS NOT NULL and tag_name IS NOT NULL and (is_enabled <> 1 OR is_enabled IS NULL);


