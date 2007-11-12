<?php

function fundcore_get_total($since = NULL, $until = NULL)
{
	$civicrm_config =& wm_config('civicrm');
	$exclude_id = $civicrm_config['contribution_type_id_matching'];

	$civicrm =& wm_db_connect('civicrm');
	$sql = 'SELECT SUM(total_amount) AS total FROM civicrm_contribution WHERE contribution_type_id <> ' . $civicrm->quote($exclude_id) . ' ';
	
	if ($since != NULL)
		$sql .= ' AND receive_date >= ' . $civicrm->quote($since);
	if ($until != NULL)
		$sql .= ' AND receive_date <= ' . $civicrm->quote($until . ' 23:59');
	
	//echo $sql;
	$res =& $civicrm->query($sql);
	if (PEAR::isError($res))
	{
		die($sql);
	}
	if ($row =& $res->fetchRow())
	{
		return $row['total'];
	}	
}

function fundcore_download($url, $post_vars = null)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	if ($post_vars !== NULL)
	{
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vars);
	}
	
	$data = curl_exec($ch);
	curl_close($ch);
	
	if (!$data)
	{
	    return curl_error($ch);
	}
	
	return $data;
}	