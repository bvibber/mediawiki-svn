<?php

/**
 * CodeBrowse
 * Requires CodeReview
 * GPLv2
 * Author: Bryan Tong Minh
 */

$dir = dirname( __FILE__ );
$wgAutoloadClasses['CodeBrowseView'] = $dir.'/CodeBrowseView.php';
$wgAutoloadClasses['CodeBrowseItemView'] = $dir.'/CodeBrowseItemView.php';
$wgAutoloadClasses['CodeBrowseRepoListView'] = $dir.'/CodeBrowseRepoListView.php';
$wgAutoloadClasses['SpecialCodeBrowse'] = $dir.'/SpecialCodeBrowse.php';
$wgSpecialPages['CodeBrowse'] = 'SpecialCodeBrowse';
 