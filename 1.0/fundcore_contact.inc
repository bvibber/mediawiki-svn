<?php
class fundcore_contact
{
	public $first_name;
	public $last_name;
	public $location_name;
	public $email;
	public $address_street;
	public $address_city;
	public $address_state;
	public $address_country_code;
	public $address_postal_code;
	
	public $do_not_trade;
	
	private $id;
	private $location_id;
	
	// Start with an explicitly NULL id
	public function __construct()
	{
		$this->id = NULL;
		$this->location_id = NULL;
	}

	// Returns the contact id
	//
	// Returns FALSE on failure
	public function get_id()
	{
		if ($this->id === NULL)
			return FALSE;
		
		return $this->id;
	}
	
	// Attempt to synchronize current information
	// with the matching contact id
	//
	// Returns FALSE on failure
	public function synchronize()
	{
		// Try to look up id
		if (!$this->lookup_id())
			return FALSE;
		
		if ($this->address_street != '')
			$this->synchronize_address();

		return TRUE;
	}
	
	private function get_country_id()
	{
		$civicrm =& wm_db_connect('civicrm');
		$sql = 'SELECT id FROM civicrm_country WHERE iso_code = ' . $civicrm->quote($this->address_country_code);
		$res =& $civicrm->query($sql);
		if (PEAR::isError($res))
		{
			wm_log('country id lookup failure', null, null, $this, true);
			return FALSE;
		}
		if ($row =& $res->fetchRow())
		{
			return $row['id'];
		}
		
		return NULL;
	}
	
	private function get_state_id($country_id = FALSE)
	{
		// Load default country if none specified
		if ($country_id === FALSE)
		{
			$civicrm_config =& wm_config('civicrm');
			$country_id = $civicrm_config['country_id'];
		}

		$civicrm =& wm_db_connect('civicrm');
		$sql = 'SELECT id FROM civicrm_state_province WHERE country_id = ' . $civicrm->quote($country_id) . ' AND (abbreviation LIKE ' . $civicrm->quote($this->address_state) . ' OR name LIKE ' . $civicrm->quote($this->address_state) . ')';
		$res =& $civicrm->query($sql);
		if (PEAR::isError($res))
		{
			wm_log('state id lookup failure', null, null, $this, true);
			return FALSE;
		}
		if ($row =& $res->fetchRow())
		{
			return $row['id'];
		}
		
		return NULL;
	}		
	
	// Tries to sync the given address for current contact id
	//
	// 1. Tries to find an existing billing address
	// 2. Or, creates a new address and stores it
	//
	// Returns FALSE on failure
	private function synchronize_address()
	{
		// Try to lookup location id
		if (!$this->lookup_location_id())
			return FALSE;

		$civicrm =& wm_db_connect('civicrm');
		
		// Try and find an existing address
		$sql = 'SELECT id FROM civicrm_address WHERE location_id = ' . $civicrm->quote($this->location_id);
		$res =& $civicrm->query($sql);			
		if (PEAR::isError($res))
		{
			wm_log('existing address lookup failure', null, null, $this, true);
			return FALSE;
		}
		$address_id = NULL;
		if ($row =& $res->fetchRow())
		{
			$address_id = $row['id'];
		}
		
		// Map fields
		$fields = array();
		$fields['street_address'] = $this->address_street;
		$fields['city'] = $this->address_city;
		$fields['country_id'] = $this->get_country_id();
		$fields['state_province_id'] = $this->get_state_id($fields['country_id']);
		$fields['postal_code'] = $this->address_postal_code;
		
		// Create or update address
		if ($address_id === NULL)
		{
			// Only a new record needs the location_id
			$fields['location_id'] = $this->location_id;
			
			// Add a new address record
			$res =& $civicrm->autoExecute('civicrm_address', $fields);
			if (PEAR::isError($res))
			{
				wm_log('address creation failure', null, null, $this, true);
				return FALSE;
			}
			$address_id = $civicrm->lastInsertID();				
		} else {
			// Update an existing record	
			$res =& $civicrm->autoExecute('civicrm_address', $fields, MDB2_AUTOQUERY_UPDATE, 'id = ' . $civicrm->quote($address_id));
			if (PEAR::isError($res))
			{
				wm_log('address update failure', null, null, $this, true);
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	// Attempts to look up or create the contact id
	//
	// Returns FALSE on failure
	private function lookup_location_id()
	{
		//print('lookup_location_id<br />');

		if ($this->location_id !== NULL)
			return TRUE;

		// If id is NULL, try once to lookup id
		if (!$this->lookup_id())
			return FALSE;
		
		$civicrm =& wm_db_connect('civicrm');
		$civicrm_config =& wm_config('civicrm');
		$location_type_id = $civicrm_config['location_type_id'];

		// Find existing location record
		$sql = 'SELECT id FROM civicrm_location l WHERE l.location_type_id = ' . $civicrm->quote($location_type_id) . ' AND l.entity_table = "civicrm_contact" AND l.entity_id = ' . $civicrm->quote($this->id);
		$res =& $civicrm->query($sql);
		if (PEAR::isError($res))
		{
			wm_log('existing location lookup failure', null, null, $this, true);
			return FALSE;
		}
		
		// Set the location_id if found
		if ($row = $res->fetchRow())
		{
			$this->location_id = $row['id'];
		}
		
		// Return now if we have a location_id
		if ($this->location_id !== NULL)
			return TRUE;

		// Initialize a new location
		$fields = array();
		$fields['entity_table'] = 'civicrm_contact';
		$fields['entity_id'] = $this->id;
		$fields['location_type_id'] = $civicrm_config['location_type_id'];
		if ($this->location_name != '')
			$fields['name'] = $this->location_name;
		
		// Check if contact has other locations to determine PRIMARY status
		$sql = 'SELECT id FROM civicrm_location l WHERE l.entity_table = "civicrm_contact" AND l.entity_id = ' . $civicrm->quote($this->id);
		$res =& $civicrm->query($sql);
		if (PEAR::isError($res))
		{
			wm_log('location count lookup failure', null, null, $this, true);
			return FALSE;
		}
		if ($row = $res->fetchRow())
		{
			$fields['is_primary'] = 0;
		} else {
			$fields['is_primary'] = 1;	
		}

		// Add location record
		$res =& $civicrm->autoExecute('civicrm_location', $fields);
		if (PEAR::isError($res))
		{
			wm_log('location creation failure', null, null, $this, true);
			return FALSE;
		}
		$this->location_id = $civicrm->lastInsertID();
		
		return TRUE;
	}
	
	// Looks up contact id
	// If none, creates one that can be subsequently found by this function
	// with the email address
	//
	// Returns FALSE on failure
	private function lookup_id()
	{
		//print('lookup_id<br />');
		
		if ($this->id !== NULL)
			return TRUE;
		
		// email is the method for lookup
		// so a lack of email makes lookup impossible
		// id remains in NULL state if currently NULL
		if ($this->email == '')
		{
			return FALSE;
		}
		
		// Find lowest ID existing contact with the given email address
		$civicrm =& wm_db_connect('civicrm');
		$sql  = 'SELECT civicrm_contact.id AS id FROM civicrm_contact ';
		$sql .= 'INNER JOIN civicrm_location ON civicrm_location.entity_id = civicrm_contact.id ';
		$sql .= 'INNER JOIN civicrm_email ON civicrm_email.location_id = civicrm_location.id ';
		$sql .= 'WHERE civicrm_location.entity_table = "civicrm_contact" AND ';
		$sql .= 'civicrm_email.email LIKE ' . $civicrm->quote($this->email) . ' ';
		$sql .= 'ORDER BY civicrm_contact.id LIMIT 1 ';
		$res =& $civicrm->query($sql);
		if (PEAR::isError($res))
		{
			wm_log('existing contact lookup (by email) failed', null, null, $this, true);
			return FALSE;
		}
		
		//echo $sql . '<br />';
		
		// Return the contact_id if found
		if ($row =& $res->fetchRow())
		{
			$this->id = $row['id'];
			return TRUE;
		}
		
		// If we're here, no email match exists
		
		// Load civicrm schema configuration			
		$civicrm_config =& wm_config('civicrm');
		
		// Encapsulate the manipulation in a transaction
		// because the INSERTs will create a temporary
		// inconsistent state
		$civicrm->beginTransaction();
		
		// Add parent contact record
		$fields = array();
		$fields['domain_id'] = $civicrm_config['domain_id'];
		$fields['contact_type'] = 'Individual';
		
		if ($this->do_not_trade)
			$fields['do_not_trade'] = 1;
		
		// Use the email address for the names
		
		// if the first and last names are blank
		if ($this->last_name == '' && $this->first_name == '')
		{
			$fields['sort_name'] = $this->email;
			$fields['display_name'] = $this->email;
		} else {
			$fields['sort_name'] = trim($this->last_name . ', ' . $this->first_name, ' ,');
			$fields['display_name'] = trim($this->first_name . ' ' . $this->last_name);
		}
		$res =& $civicrm->autoExecute('civicrm_contact', $fields);
		if (PEAR::isError($res))
		{
			wm_log('contact creation failure', null, null, $this, true);
			return FALSE;
		}
		$this->id = $civicrm->lastInsertID();
		
		// Add individual record
		$fields = array();
		$fields['contact_id'] =$this->id;
		$fields['first_name'] = $this->first_name;
		$fields['last_name'] = $this->last_name;
		$res =& $civicrm->autoExecute('civicrm_individual', $fields);
		if (PEAR::isError($res))
		{
			wm_log('individual creation failure', null, null, $this, true);
			return FALSE;
		}
		$civicrm->commit();

		if (!$this->lookup_location_id())
			return FALSE;

		// Add email record for future lookup
		$fields = array();
		$fields['location_id'] = $this->location_id;
		$fields['email'] = strtolower($this->email);
		$fields['is_primary'] = 1;
		$res =& $civicrm->autoExecute('civicrm_email', $fields);
		if (PEAR::isError($res))
		{
			wm_log('email creation failure', null, null, $this, true);
			return FALSE;
		}

		$this->synchronize();
		
		return TRUE;
	}
}
