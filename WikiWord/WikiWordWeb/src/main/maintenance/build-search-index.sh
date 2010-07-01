#!/bin/bash
set -e

db="$1"
collection="$2"
thesaurus="$3"

echo "building search index"
replace  '{collection}' "$collection" '{thesaurus}' "$thesaurus"  < search-index.sql | mysql "$db"

echo "building resource index"
replace  '{collection}' "$collection" '{thesaurus}' "$thesaurus"  < resource-index.sql | mysql "$db"
