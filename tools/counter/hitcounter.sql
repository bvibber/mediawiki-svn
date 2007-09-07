CREATE TABLE hit_counter (
  hc_ts TIMESTAMP,

  hc_site VARCHAR(255) BINARY,
  hc_page VARCHAR(255) BINARY,

  KEY (hc_ts, hc_site, hc_page),
  KEY (hc_site, hc_page, hc_ts)
) CHARSET=binary;
