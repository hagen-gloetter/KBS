#! /bin/bash
EXCLUDE="--exclude=.git --exclude=__pycache__ --exclude=*.log --exclude=3d-case --exclude=wiring-diagram --exclude=webserver --exclude=.gitignore --exclude=README.md --exclude=CHANGELOG.md --exclude=SECURITY.md"

echo "########  rsync to pi-zero1"
rsync -ave ssh $EXCLUDE  .  pi@pi-zero1:~/kbs/

echo "########  rsync to pi-zero2"
rsync -ave ssh $EXCLUDE  .  pi@pi-zero2:~/kbs/

echo "########  rsync to pi-zero3"
rsync -ave ssh $EXCLUDE  .  pi@pi-zero3:~/kbs/

echo "########  rsync webserver to atlas"
rsync -ave ssh --exclude=.git webserver/* hagen@atlas:/var/www/html/

echo "########  Deployment abgeschlossen"
