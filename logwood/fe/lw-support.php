<?php
require_once('/etc/logwood-fe.conf');

class lw_db {
	var	$db;
	var	$site;

	function version() {
		return "R1";
	}

	function mysql_version() {
		return $this->db->server_info;
	}

	function __construct ($site) {
		global $server, $username, $password, $database;

		$this->db = new mysqli($server, $username, $password, $database);
		$this->site = $site;

		if (mysqli_connect_errno()) {
			printf("MySQL connection failed: %s\n", mysqli_connect_erro());
			exit();
		}
	}

	function edits_by_hour() {
		$hours = array();

		/*
		 * Ensure all hours have at least a 0 value.
		 */
		for ($i = 0; $i < 24; ++$i)
			$hours[$i] = 0;

		$r_hour = $r_count = 0;
		$stmt = $this->db->prepare("
				SELECT hr_hour, SUM(hr_count) AS total FROM sites,hours
				WHERE si_name = ? AND hr_site = si_id
				GROUP BY hr_hour ORDER BY hr_hour ASC
			");
		if (!$stmt) {
			die($this->db->error());
		}
		$stmt->bind_param("s", $this->site);
		$stmt->bind_result($r_hour, $r_count);

		if (!$stmt->execute()) {
			printf("MySQL query failed: %s\n", $stmt->error);
			exit();
		}

		while ($stmt->fetch()) {
			$hours[$r_hour] = $r_count;
		}
		
		/* Could do error handling here... */
		$stmt->close();
		return $hours;
	}

	function edits_by_wday() {
		$days = array();
		for ($i = 0; $i < 7; ++$i)
			$days[$i] = 0;

		$stmt = $this->db->prepare("
			SELECT wd_day, SUM(wd_hits) AS total FROM sites,wdays
			WHERE si_name = ? AND wd_site=si_id
			GROUP BY wd_day ORDER BY wd_day ASC
		");
		if (!$stmt)
			die($this->db->error());
		
		$r_day = $r_hits = 0;
		$stmt->bind_param("s", $this->site);
		$stmt->bind_result($r_day, $r_hits);
		
		if (!$stmt->execute())
			die($stmt->error());

		while ($stmt->fetch())
			$days[$r_day] = $r_hits;

		$stmt->close();
		return $days;
	}

	function close() {
		$this->db->close();
	}
}
