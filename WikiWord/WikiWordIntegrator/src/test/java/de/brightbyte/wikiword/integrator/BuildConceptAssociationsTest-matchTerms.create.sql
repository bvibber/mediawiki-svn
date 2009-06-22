CREATE TABLE "TEST_xx_foreign" (
	"foreign_authority" VARCHAR(64),
	"foreign_id" VARCHAR(255),
	"property" VARCHAR(64),
	"value" VARCHAR(255),
	"qualifier" VARCHAR(64)
);

CREATE TABLE "TEST_xx_meaning" (
	"concept" INT,
	"concept_name" VARCHAR(255),
	"term_text" VARCHAR(255),
	"freq" INT,
	"rule" INT
);
