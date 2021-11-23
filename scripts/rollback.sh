service zen-jms-job-queue stop
service zen-jms-job-scheduler stop
service apache2 stop
service mysql stop 
echo "version to revert"
read version
mkdir -p /home/zenity/backup/zenityService_beforeBackup
cp -Rp /var/lib/mysql/zenityService /home/zenity/backup/zenityService_beforeBackup
backup_location=/home/zenity/backup/zenityService_$version
if [ ! -f $backup_location ]; then
    echo "Backup located at $backup_location not found!"
    exit
fi

git reset --hard v$version

service mysql start 
service apache2 start
/home/zenity/zenity/webService/bin/console doctrine:schema:update --force
/home/zenity/zenity/webService/bin/console cache:clear --no-warmup --env prod
/home/zenity/zenity/webService/bin/console test:parser
rm -rf /var/lib/mysql/zenityService 
cp -Rp /var/lib/mysql/zenityService $backup_location

echo "if the database is not up to date specify the gap(in day) to parse the snapshots again"
read gap
find +time $gap /home/zenity/zenity/snapshots-done/ -exec mv -t /home/zenity/zenity/snapshots/ {} + 

service zen-jms-job-queue start
service zen-jms-job-scheduler start
