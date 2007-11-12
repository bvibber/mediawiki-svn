<?php

function fundcore_get_rates()
{
	$rate_config =& fundcore_config('exchange_rates');
	$data = fundcore_download($rate_config['url']);
	return $data;
}

function fundcore_update_rate($currency, $value_in_usd, $date)
{
	$payments =& wm_db_connect('payments');
	$attr['currency'] = $currency;
	$attr['value_in_usd'] = $value_in_usd;
	$attr['info_date'] = $date;

	// Try inserting first
	$res =& $payments->autoExecute('exchange_rates', $attr);
	if (PEAR::isError($res))
	{
		//die('Insert query error');
	}

	// Then try updating
	$res =& $payments->autoExecute('exchange_rates', $attr, MDB2_AUTOQUERY_UPDATE, 'currency = ' . $payments->quote($attr['currency']));
	if (PEAR::isError($res))
	{
		die('Update query error');
	}

}

function fundcore_update_rates()
{
	$xml_data = wm_get_rates();
	$p = xml_parser_create();
	xml_parse_into_struct($p, $xml_data, $vals, $index);
	xml_parser_free($p);

	// Get data and base USD rate
	$usd_base = 0;
	$date = '';
	foreach ($index['CUBE'] as $val_index)
	{
		$current = $vals[$val_index];
		if ($current['attributes']['CURRENCY'] == 'USD' && isset($current['attributes']['RATE']))
		{
			$usd_base = $current['attributes']['RATE'];
		}
		if (isset($current['attributes']['TIME']))
		{
			$date = $current['attributes']['TIME'];
		}
	}

	// Table is based on EUR, so must insert manually
	wm_update_rate('EUR', $usd_base, $date);

	// Calculate remaining rates
	foreach ($index['CUBE'] as $val_index)
	{
		$current = $vals[$val_index];
		if (isset($current['attributes']['CURRENCY']) && isset($current['attributes']['RATE']))
		{
			wm_update_rate($current['attributes']['CURRENCY'], $usd_base / $current['attributes']['RATE'], $date);
		}
	}
}

function fundcore_convert_to_usd($currency, $amount)
{
	if ($currency == 'USD')
		return $amount;
	
	$amount = str_replace(',', '', $amount);
	
	$payments =& wm_db_connect('payments');
	
	//echo $payments->quote($currency) . "\n";
	
	$sql = 'SELECT value_in_usd FROM exchange_rates WHERE currency = ' . $payments->quote($currency);
	
	//echo $sql . "\n";
	
	$res =& $payments->query($sql);

	if (PEAR::isError($res))
	{
			//print_r($res);
			wm_log('converting currency failed', null, null, $currency . ' ' . $amount . "\n" . $sql, true);
			return 0;
	}

	if ($row =& $res->fetchRow())
	{
		return $row['value_in_usd'] * $amount;
	}
	
	wm_log('converting currency failed (invalid currency)', null, null, $currency . ' ' . $amount . "\n" . $sql, true);		
	return 0;
}