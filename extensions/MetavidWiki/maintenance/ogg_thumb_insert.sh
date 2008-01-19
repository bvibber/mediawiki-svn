#!/bin/bash

streamid=${1};
filename=${2};
interval=${3};

table="mv_stream_images";
db="mvwiki";
user="user";
pw="password";
hostname="localhost";

chars=`echo -n ${streamid} | wc -c`;
dots=`for i in \`seq 1 ${chars}\`; do echo -n .; done | sed -e s/^.//`
dir=`echo ${streamid} | sed -e s/^${dots}//`

filedir=`echo "../stream_images/"${dir}"/"${streamid}`

mkdir -p ${filedir}

duration=`ogginfo ${filename}  | grep -A 2 "^Vorbis stream" | grep "Playback length" | awk '{print $3}' | gawk -F 'm:' '{print "(" $1 "*60)+" $2}' | sed -e s/s//g | bc`

ffmpeg -ss 1 -i ${filename} -vcodec mjpeg -vframes 1 -an -f rawvideo -y -s 80x60 ${filedir}/1_80x60.jpg
convert ${filedir}/1_80x60.jpg ${filedir}/1_80x60.png

for i in `seq 1 ${interval} ${duration}`
do
  #echo "insert into ${table}(stream_id, time) values(${streamid}, ${i});" | mysql -u ${user} --password=${pw} ${db}
  ffmpeg -ss ${i} -i ${filename} -vcodec mjpeg -vframes 1 -an -f rawvideo -s 320x240 -y ${filedir}/${i}_320x240.jpg
done


