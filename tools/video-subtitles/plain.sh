mencoderPath="/opt/mplayer/bin/"

${mencoderPath}mencoder \
source.mov \
-o test.avi \
-ovc raw \
  -vf scale=400:224,format=i420 \
  -ofps 15 \
-oac pcm \
  -srate 44010 \
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
-o "web/PSA-Web-400-15fps.ogg" \
test.avi \
&&
rm -f test.avi

