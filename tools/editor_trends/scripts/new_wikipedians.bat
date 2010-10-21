@echo off
cd \mongodb\bin
mongo 127.0.0.1/editors last_edit.js > last_edit.csv
mongo 127.0.0.1/editors retrieve_editor_ids.js > ids.csv