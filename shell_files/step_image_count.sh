#!/bin/sh

URL=$1;

#画像数を計算する PHPで利用する時はsnapshot-finalとsnapshot-initを除くため-2した数でやる

find ${URL} -name '*.png' -type f | wc -l > "${URL}/step_count.txt"