#!/bin/bash
set -e

db="$1"
collection="$2"
thesaurus="$3"
languages="commons en de fr nl it es pt pl"

echo "preparing concept info"
replace  '{collection}' "$collection" '{thesaurus}' "$thesaurus"  < concept-info.sql | mysql "$db"

for n in $languages; do
    echo "collection concept info: $n"
    replace  '{collection}' "$collection" '{thesaurus}' "$thesaurus" '{lang}' "$n" < concept-info-local.sql | mysql "$db"
done
