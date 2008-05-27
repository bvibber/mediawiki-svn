PHP_ARG_ENABLE(perusersessionsavepath, whether to enable per-user session save path,
[  --enable-perusersessionsavepath   Enable per-user session save paths])

if test "$PHP_PERUSERSESSIONSAVEPATH" != "no"; then
  PHP_NEW_EXTENSION(perusersessionsavepath, perusersessionsavepath.c, $ext_shared)
fi

