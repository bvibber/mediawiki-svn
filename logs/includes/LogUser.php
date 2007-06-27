<?php

/**
 * Simple convenience wrapper around a user identifier
 * and their username
 *
 * @addtogroup Logging
 * @author Rob Church <robchur@gmail.com>
 */
class LogUser {

	/**
	 * User identifier
	 */
	private $id = 0;
	
	/**
	 * Username
	 */
	private $name = 0;

	/**
	 * Constructor
	 *
	 * @param int $id
	 * @param string $name
	 */
	public function __construct( $id, $name ) {
		$this->id = $id;
		$this->name = $name;
	}
	
	/**
	 * Get the identifier
	 *
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Get the username
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Get the user page
	 *
	 * @return Title
	 */
	public function getTitle() {
		return Title::makeTitleSafe( NS_USER, $this->name );
	}
	
	/**
	 * Get a real User object for this user
	 *
	 * @return User
	 */
	public function getObject() {
		return User::newFromId( $this->id );
	}	

}

?>