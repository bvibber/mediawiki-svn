mencoderPath="/opt/mplayer/bin/"
#font="FreeSans.ttf"
#font="Vera.ttf"
font="DejaVuSans.ttf"
#fontsize="-subfont-autoscale 2 -subfont-text-scale 4"
fontsize="-subfont-autoscale 2 -subfont-text-scale 4"
rtl=""


if [ "x$1" == "xhe" ]; then
  rtl="-flip-hebrew -noflip-hebrew-commas"
fi
if [ "x$1" == "xar" ]; then
  # Not enough, we don't have shaping. :(
  rtl="-flip-hebrew -noflip-hebrew-commas"
fi
if [ "x$1" == "xbn" ]; then
  font="JamrulNormal.ttf"
fi
if [ "x$1" == "xja" ]; then
  font="kochi-gothic-subst.ttf"
fi
if [ "x$1" == "xzh" ]; then
  font="uming.ttc"
fi
if [ "x$1" == "xzh-yue" ]; then
  font="uming.ttc"
fi
if [ "x$1" == "xth" ]; then
  font="Loma.ttf"
fi


${mencoderPath}mencoder \
source.mov \
-o test-$1.avi \
-ovc raw \
  -vf scale=400:224,format=i420 \
  -ofps 15 \
-oac pcm \
  -srate 44010 \
-sub "titles/PSA-Web.$1.srt" \
$rtl \
-font "$font" \
$fontsize \
-utf8 \
&&
ffmpeg2theora \
-x 400 \
-y 224 \
-v 8 \
-V 256 \
--optimize \
--no-skeleton \
-a 4 \
-A 64 \
-c 1 \
-o "web/PSA-Web-400-15fps-$1.ogg" \
test-$1.avi \
&&
rm -f test-$1.avi

