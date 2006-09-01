TRUNCATE TABLE uw_alt_meaningtexts;
TRUNCATE TABLE uw_collection_contents;
TRUNCATE TABLE uw_collection_language;
TRUNCATE TABLE uw_collection_ns;
TRUNCATE TABLE uw_defined_meaning;
TRUNCATE TABLE uw_dm_text_attribute_values;
TRUNCATE TABLE uw_expression_ns;
TRUNCATE TABLE uw_meaning_relations;
TRUNCATE TABLE uw_syntrans;
TRUNCATE TABLE uw_syntrans_relations;
TRUNCATE TABLE uw_versions_ns_collection;
TRUNCATE TABLE uw_versions_ns_gemet;
TRUNCATE TABLE transactions;

DELETE translated_content, text FROM translated_content, text WHERE translated_content.text_id=text.old_id;
DELETE page, revision FROM page, revision WHERE page.page_namespace >= 16 AND revision.rev_page=page.page_id;

TRUNCATE TABLE translated_content;