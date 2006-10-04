#!/bin/sh -e
# /usr/lib/emacsen-common/packages/remove/wikimedia-lvs-realserver

FLAVOR=$1
PACKAGE=wikimedia-lvs-realserver

if [ ${FLAVOR} != emacs ]; then
    if test -x /usr/sbin/install-info-altdir; then
        echo remove/${PACKAGE}: removing Info links for ${FLAVOR}
        install-info-altdir --quiet --remove --dirname=${FLAVOR} /usr/info/wikimedia-lvs-realserver.info.gz
    fi

    echo remove/${PACKAGE}: purging byte-compiled files for ${FLAVOR}
    rm -rf /usr/share/${FLAVOR}/site-lisp/${PACKAGE}
fi
