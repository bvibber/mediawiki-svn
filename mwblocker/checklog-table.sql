CREATE TABLE checklog (
  check_timestamp DATETIME,
  check_ip VARCHAR(40),
  check_blocked TINYINT(1),
  check_log TEXT,
  KEY (check_ip,check_blocked),
  KEY (check_blocked,check_IP),
  KEY (check_timestamp)
);
