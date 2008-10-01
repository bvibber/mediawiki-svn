-- Script URLs for interwiki, for RemoteSite access

ALTER TABLE /*$wgDBprefix*/interwiki 
	ADD iw_wikiname barbinary(20) not null after iw_url,
	ADD iw_scripturl blob not null after iw_url,
	ADD iw_type varbinary(10) not null default 'gen_us' after iw_url;
