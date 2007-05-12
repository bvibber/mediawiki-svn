<?php

class RepoGroup {
	var $localRepo, $foreignRepos, $reposInitialised = false;
	var $localInfo, $foreignInfo;

	protected static $instance;

	function singleton() {
		if ( self::$instance ) {
			return self::$instance;
		}
		global $wgLocalFileRepo, $wgForeignFileRepos;
		self::$instance = new RepoGroup( $wgLocalFileRepo, $wgForeignFileRepos );
		return self::$instance;
	}

	/**
	 * Construct a group of file repositories. 
	 * @param array $data Array of repository info arrays. 
	 *     Each info array is an associative array with the 'class' member 
	 *     giving the class name. The entire array is passed to the repository
	 *     constructor as the first parameter.
	 */
	function __construct( $localInfo, $foreignInfo ) {
		$this->localInfo = $localInfo;
		$this->foreignInfo = $foreignInfo;
	}

	/**
	 * Search repositories for an image.
	 * You can also use wfGetFile() to do this.
	 * @param mixed $title Title object or string
	 * @return File object or false if it is not found
	 */
	function findFile( $title ) {
		if ( !$this->reposInitialised ) {
			$this->initialiseRepos();
		}

		$image = $this->localRepo->newFile( $title );
		if ( $image->exists() ) {
			return $image;
		}
		foreach ( $this->foreignRepos as $repo ) {
			$image = $repo->newFile( $title );
			if ( $image->exists() ) {
				return $image;
			}
		}
		return false;
	}

	/**
	 * Get the repo instance with a given key.
	 */
	function getRepo( $index ) {
		if ( !$this->reposInitialised ) {
			$this->initialiseRepos();
		}
		if ( $index == 'local' ) {
			return $this->localRepo;
		} elseif ( isset( $this->foreignRepos[$index] ) ) {
			return $this->foreignRepos[$index];
		} else {
			return false;
		}
	}

	function getLocalRepo() {
		return $this->getRepo( 'local' );
	}

	/**
	 * Initialise the $repos array
	 */
	function initialiseRepos() {
		if ( $this->reposInitialised ) {
			return;
		}
		$this->reposInitialised = true;

		$this->localRepo = $this->newRepo( $this->localInfo );
		$this->foreignRepos = array();
		foreach ( $this->foreignInfo as $key => $info ) {
			$this->foreignRepos[$key] = $this->newRepo( $info );
		}
	}

	function newRepo( $info ) {
		$class = $info['class'];
		return new $class( $info );
	}
}

?>
