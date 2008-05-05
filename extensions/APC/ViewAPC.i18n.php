<?php

$messages = array();

$messages['en'] = array(
	'viewapc'      => 'APC information',
	'viewapc-desc' => '[[Special:ViewAPC|View and manage APC cache]] with MediaWiki',
	'right-apc'    => 'Use all features in Special:ViewAPC',

	'viewapc-apc-not-available' => 'No cache info available.
APC does not appear to be running.',
	'viewapc-clear-confirm'     => 'Do you want to clear the cache?',
	'viewapc-clear-user-cache'  => 'Clear user cache',
	'viewapc-clear-code-cache'  => 'Clear opcode cache',

	'viewapc-mode-stats'            => 'View host stats',
	'viewapc-mode-system-cache'     => 'System cache entries',
	'viewapc-mode-system-cache-dir' => 'Per-directory entries',
	'viewapc-mode-user-cache'       => 'User cache entries',
	'viewapc-mode-version-check'    => 'Check version',

	'viewapc-info-general'          => 'General information',
	'viewapc-apc-version'           => 'APC version',
	'viewapc-php-version'           => 'PHP version',
	'viewapc-shared-memory'         => 'Shared memory',
	'viewapc-shared-memory-details' => '{{PLURAL:$1|1 segment of $2|$1 segments $2 each}}.<br />
($3 memory, $4 locking)',
	'viewapc-start-time'            => 'Start time',
	'viewapc-uptime'                => 'Uptime',
	'viewapc-upload-support'        => 'File upload support',

	'viewapc-filecache-info' => 'File cache information',
	'viewapc-usercache-info' => 'User cache information',
	'viewapc-cached-files'   => 'Cached files',
	'viewapc-cached-files-d' => '$1 {{PLURAL:$1|file|files}} ($2)',
	'viewapc-hits'           => 'Hits',
	'viewapc-misses'         => 'Misses',
	'viewapc-requests'       => 'Request rate',
	'viewapc-hitrate'        => 'Hit rate',
	'viewapc-missrate'       => 'Miss rate',
	'viewapc-insertrate'     => 'Insertion rate',
	'viewapc-cachefull'      => 'Cache full count',
	'viewapc-rps'            => '$1 requests per second',

	'viewapc-info-runtime' => 'Runtime information',

	'viewapc-info-memory'           => 'Host status diagrams',
	'viewapc-memory-usage-detailed' => 'Memory Usage<br />
(multiple slices indicate fragments)',
	'viewapc-memory-usage'          => 'Memory Usage',
	'viewapc-cache-efficiency'      => 'Hits and misses',

	'viewapc-memory-free' => 'Free: $1 ($2)',
	'viewapc-memory-used' => 'Used: $1 ($2)',
	'viewapc-memory-hits' => 'Hits: $1 ($2)',
	'viewapc-memory-miss' => 'Misses: $1 ($2)',

	'viewapc-memoryfragmentation' => 'Detailed memory usage and fragmentation',
	'viewapc-fragmentation-info'  => 'Fragmentation: $1 ($2 out of $3 in $4 {{PLURAL:$4|fragment|fragments}})',
	'viewapc-fragmentation-none'  => 'Fragmentation: no fragmentation',

	'viewapc-display-attribute' => 'Attribute',
	'viewapc-display-value'     => 'Value',

	'viewapc-display-filename'      => 'Filename',
	'viewapc-display-device'        => 'Device',
	'viewapc-display-info'          => 'Name',
	'viewapc-display-ttl'           => 'Expiry time',
	'viewapc-display-inode'         => 'Inode',
	'viewapc-display-type'          => 'Type',
	'viewapc-display-type-file'     => 'Cached file',
	'viewapc-display-type-user'     => 'Cached application data',
	'viewapc-display-num_hits'      => 'Hits',
	'viewapc-display-mtime'         => 'Modified',
	'viewapc-display-creation_time' => 'Created',
	'viewapc-display-deletion_time' => 'Deleted',
	'viewapc-display-no-delete'     => 'Not deleted',
	'viewapc-display-access_time'   => 'Accessed',
	'viewapc-display-ref_count'     => 'Reference count',
	'viewapc-display-mem_size'      => 'Size',
	'viewapc-display-stored-value'  => 'Stored value',

	'viewapc-ls-options-legend' => 'Options',
	'viewapc-ls-options'        => 'Scope: $1 Sorting: $2$3$4 Search: $5 $6',
	'viewapc-ls-submit'         => 'Go!',

	'viewapc-ls-header-name'     => 'Name',
	'viewapc-ls-header-hits'     => 'Hits',
	'viewapc-ls-header-size'     => 'Size',
	'viewapc-ls-header-accessed' => 'Last accessed',
	'viewapc-ls-header-modified' => 'Last modified',
	'viewapc-ls-header-created'  => 'Created',
	'viewapc-ls-header-deleted'  => 'Deleted',
	'viewapc-ls-header-timeout'  => 'Timeout',

	'viewapc-ls-delete' => '[Delete now]',

	'viewapc-ls-scope-active'  => 'Active',
	'viewapc-ls-scope-deleted' => 'Deleted',
	'viewapc-ls-scope-both'    => 'Both',

	'viewapc-ls-sort-hits'     => 'Hits',
	'viewapc-ls-sort-size'     => 'Size',
	'viewapc-ls-sort-name'     => 'Name',
	'viewapc-ls-sort-accessed' => 'Last accessed',
	'viewapc-ls-sort-modified' => 'Last modified',
	'viewapc-ls-sort-created'  => 'Created',
	'viewapc-ls-sort-deleted'  => 'Deleted',
	'viewapc-ls-sort-timeout'  => 'Timeout',

	'viewapc-ls-limit-none' => 'All',
	'viewapc-ls-more'       => "''There is $1 more {{PLURAL:$1|entry|entries}}''",
	'viewapc-ls-nodata'     => "''No matching data''",

	'viewapc-delete-ok'     => 'Cache entry <nowiki>$1</nowiki> is deleted.',
	'viewapc-delete-failed' => 'Failed to delete cache entry <nowiki>$1</nowiki>.',

	'viewapc-version-info'      => 'Version information',
	'viewapc-version-changelog' => 'Changelog',
	'viewapc-version-failed'    => 'Unable to fetch version information.',
	'viewapc-version-ok'        => 'You are running the latest version of APC ($1)',
	'viewapc-version-old'       => 'You are running an older version of APC ($1).
Newer version $2 is available at http://pecl.php.net/package/APC/$2',

	'viewapc-filecache-cleared' => "'''''File cache cleared.'''''",
	'viewapc-usercache-cleared' => "'''''Application cache cleared.'''''",

);
