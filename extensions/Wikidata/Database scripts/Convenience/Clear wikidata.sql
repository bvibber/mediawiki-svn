DELETE FROM uw_alt_meaningtexts;
DELETE FROM uw_collection_contents;
DELETE FROM uw_collection_language;
DELETE FROM uw_collection_ns;
DELETE FROM uw_defined_meaning;
DELETE FROM uw_dm_text_attribute_values;
DELETE FROM uw_expression_ns;
DELETE FROM uw_meaning_relations;
DELETE FROM uw_syntrans;
DELETE FROM uw_syntrans_relations;
DELETE FROM uw_versions_ns_collection;
DELETE FROM uw_versions_ns_gemet;

DELETE translated_content, text FROM translated_content, text WHERE translated_content.text_id=text.old_id;
DELETE page, revision FROM page, revision WHERE page.page_namespace >= 16 AND revision.rev_page=page.page_id;