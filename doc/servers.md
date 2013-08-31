SVN: https://svn.pgs-soft.com/fenchy/trunk

SERVERS:
    - dev/test:
        address: http://10.10.32.137/app_dev.php
        autoupdate: daily 0:00 by root at /var/fenchy/update.sh
        database: fenchy:F3nchy@localhost fenchy
        login/pass: fenchy/fenchy
        set *.fenchy.com for 10.10.32.137 in hosts (important for facebook login)
    - demo:
        address: https://phpdev104.pgs-soft.com/
        hudson: http://pgslnx05.pgs-soft.com:8080/job/Fenchy/ (DO NOT OWN FILES BY ROOT!)
        autoupdate: none
        database: fenchy:F3nchy@localhost fenchy
        login/pass: PGS-SOFT\domain_name

USEFUL CONSOLE COMMANDS:
    - php composer.phar install (use update with careful - updates all vendors to the newest version)
    - php app/console doctrine:schema:create
    - php app/console doctrine:schema:update --force
    - php app/console doctrine:fixtures:load
    - php app/console cache:clear --env=prod --no-debug
    - php app/console fenchy:users:loadTriggers


DEPLOYMENT SERVER CONFIGURATION:
1. Directories, that should be writtable by webserver ( with ALL their subdirectories ):
  app/cache
  app/logs
  app/Spool
  web/uploads

2. Example crontab configuration - for use of mail spooler queue processing:
 */2  *    * * *   php /var/fenchy/trunk/app/console swiftmailer:spool:send --time-limit=60 --env=prod
 50   1    * * *   php -d  max_execution_time=601 -d memory_limit=128M  /var/fenchy/trunk/app/console fenchy:user:notifications  --env=prod
