#!/bin/bash

###########################################################################
#
# DESCRIPTION
#
# This script can extract jpg frames from your ogg files at a specified
# interval.  It can also insert this information into your mvWiki
# database.
#
# USAGE
#
# ./ogg_thumb_insert.sh stream_id filename interval
#
# EXAMPLE
#
# ./ogg_thumb_insert.sh 17 /var/www/localhost/htdocs/media/stream.ogg 20
#
#  The previous example should extract frames every 20 seconds into the
#  file named stream.ogg.  It will place them in the appropriate stream
#  directory which by default is '../stream_images/7/17/'.  It should also
#  insert information about the frame into the 'mv_stream_images' table.
#
###########################################################################
#
#  This script relies on a number of programs being in your path, and is
#  intended to be executed from the 'maintenance' directory.
#
#  Requirements:
#
#  ffmpeg
#  mysql
#  imagemagick
#  ogginfo
#  grep
#  sed
#  awk
#  gawk
#  echo
#  wc
#  bc
#  seq
#  mkdir
#
###########################################################################
#
#  Use at your own risk.  There is very little error checking.
#
###########################################################################
#  This quick hack brought to you by Seth McClain smcclain@opengov.org
###########################################################################


## REMOVE THE FOLLOWING TWO LINES BEFORE EXECUTING ##
echo "Please be sure to edit this file and change some variables before executing it";
exit
## REMOVE THE PREVIOUS TWO LINES BEFORE EXECUTING  ##


## The following variables need to be set to allow the script access to your
## MySQL database

table="mv_stream_images";
db="mvwiki";
user="user";
pw="password";
hostname="localhost";

## Do not edit below this line

streamid=${1};
filename=${2};
interval=${3};

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
  echo "insert into ${table}(stream_id, time) values(${streamid}, ${i});" | mysql -u ${user} --password=${pw} ${db}
  ffmpeg -ss ${i} -i ${filename} -vcodec mjpeg -vframes 1 -an -f rawvideo -s 320x240 -y ${filedir}/${i}_320x240.jpg
done


