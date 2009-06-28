SELECT * FROM /* wikiword_prefix *//* wikiword_mapping_table */
GROUP BY /* group_fields */
HAVING count(*) = 1
ORDER BY /* group_fields */;