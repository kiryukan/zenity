BACKUP_DIR="/home/zenity/backup/"
mysqldump zenityService -u root --password=Kopazz28 --result-file=$BACKUP_DIR/$(date +%d-%m-%y)_dumpzenityService.sql
find $BACKUP_DIR* -mtime +14 -delete
