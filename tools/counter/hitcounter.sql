CREATE TABLE hit_counter (
  hc_tsstart TIMESTAMP,
  hc_tsend  TIMESTAMP,

  hc_site VARCHAR(255) BINARY,
  hc_page VARCHAR(255) BINARY,

  hc_count int8,

  KEY (hc_tsend, hc_site, hc_page),
  KEY (hc_site, hc_page, hc_tsend)
) CHARSET=binary;
