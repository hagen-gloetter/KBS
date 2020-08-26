#! /bin/bash
echo "########  rsync -ave ssh  .  pi@pi-zero1:~/kbs/"
rsync -ave ssh  .  pi@pi-zero1:~/kbs/

echo "########  rsync -ave ssh  .  pi@pi-zero2:~/kbs/"
rsync -ave ssh  .  pi@pi-zero2:~/kbs/

echo "########  rsync -ave ssh  .  pi@pi-zero3:~/kbs/"
rsync -ave ssh  .  pi@pi-zero3:~/kbs/

echo "########  rsync -ave ssh  webserver/* hagen@atlas:/var/www/html/  "
rsync -ave ssh webserver/* hagen@atlas:/var/www/html/
