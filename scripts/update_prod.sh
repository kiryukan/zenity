ROLLBACK_SCRIPT='./rollback'
service zen-jms-job-queue stop
service zen-jms-job-scheduler stop
service apache2 stop
service mysql stop 
echo "numero de version"
read version 
mkdir -p /home/zenity/backup/zenityService_$version
cp -Rp /var/lib/mysql/zenityService /home/zenity/backup/zenityService_$version
echo "Choose a branch (stable/hotfix)"
read branch
git pull origin $branch 
git tag -a v$version
service mysql start
service apache2 start
/home/zenity/zenity/webService/bin/console doctrine:schema:update --force
/home/zenity/zenity/webService/bin/console cache:clear --no-warmup --env prod
/home/zenity/zenity/webService/bin/console test:parser
echo "is it okay(y/n)"
read is_okay
if [$is_okay -nq y];then
	$ROLLBACK_SCRIPT 
	exit
fi

service mysql start 
service apache2 start
service zen-jms-job-queue start
service zen-jms-job-scheduler start
