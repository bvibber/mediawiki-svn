07 04 * * *     pdns    rsync -qt rsync://countries-ns.mdc.dk/zone/zz.countries.nerd.dk.rbldnsd \
                        /etc/powerdns/zz.countries.nerd.dk.rbldnsd && pdns_control rediscover > /dev/null
