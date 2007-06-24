7 4 * * *     pdns    rsync -qt "rsync://countries-ns.mdc.dk/zone/zz.countries.nerd.dk.rbldnsd'" /etc/powerdns/ip-map/zz.countries.nerd.dk.rbldnsd && pdns_control rediscover > /dev/null
