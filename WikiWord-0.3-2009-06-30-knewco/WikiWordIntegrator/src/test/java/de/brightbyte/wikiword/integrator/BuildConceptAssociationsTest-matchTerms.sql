select "foreign_id", 
	M."concept" as "concept", M."concept_name" as "concept_name", 
	F."property" as "foreign_property", F."value" as "value", 
	'term' as "concept_property", M."freq" as "concept_property_freq", M."rule" as "concept_property_source",
	"freq" * "rule" as "weight"
from "TEST_xx_foreign" as F join "TEST_xx_meaning" as M on F."property" IN ('fullName', 'alias', 'sortName') and F."value" = M."term_text";