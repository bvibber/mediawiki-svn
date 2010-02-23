#!/bin/bash
set -e

db="$1"
collection="$2"
thesaurus="$3"
languages="en de fr nl it es pt pl"

echo "preparing search index"
replace  '{collection}' "$collection" '{thesaurus}' "$thesaurus"  < search-index.sql | mysql "$db"

for n in $languages; do
    echo "collection search index: $n"
    replace  '{collection}' "$collection" '{thesaurus}' "$thesaurus" '{lang}' "$n" < search-index-local.sql | mysql "$db"
done
