# AVPanel

Website management panel

### Server Requirements
 - nginx
 - mysql
 - php-fpm
 - PHP >= 5.5.9
 - OpenSSL PHP Extension
 - PDO PHP Extension
 - Mbstring PHP Extension
 - Tokenizer PHP Extension

Tested in Debian 8
### Version
1.0.1

### Installation

Install LEMP

```sh
# apt-get update
# apt-get install mysql-server mysql-client nginx php5 php5-cli php5-curl php5-fpm php5-mysql php5-json php5-mcrypt git curl
```

Composer install
```sh
# wget --no-check-certificate https://getcomposer.org/composer.phar
# mv composer.phar /usr/local/bin/composer
# chmod 775 /usr/local/bin/composer
```
Install panel
```sh
# git clone https://github.com/avengerweb/AVPanel.git avpanel
# cd avpanel
# composer install
# mv .env.example .env
```

In next step
 * Create database
 * Configure .env file (Database)
 * Configure config/remote.php (provide ssh access for root user)
 * Configure config/panel.php (Panel and webserver settings)

Generate app key
```sh
# php artisan key:generate
```

Apply database structure
```sh
# php artisan migrate
```
After installing AVPanel, you may need to configure some permissions. Directories within the ```storage``` and the ```bootstrap/cache``` directories should be writable by your web server or AVPanel will not run.

### Web server configuration
Nginx configuration for panel:
```sh
# cat /dev/null > /etc/nginx/sites-available/default
```
Then insert into /etc/nginx/sites-available/default (Update it for yourself):
```
server {
    listen 80 default_server;
    listen [::]:80 default_server ipv6only=on;

    root /var/www/avpanel/public;
    index index.php index.html index.htm;
    server_name localhost;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        try_files $uri /index.php =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}

server {
   listen 80;

   root /var/www/avpanel/public;
   index index.php index.html index.htm;
   server_name s056571b9.fastvps-server.com;

    #include our vhosts :)
   include /etc/nginx/vhosts/*/*;
}
```

Create ```vhosts``` directory for configuration files
```sh
# cd /etc/nginx/
# mkdir vhosts
```

Create ```avpanel``` directory for websites (Edit in config/panel.php)
```sh
# cd /var/
# mkdir avpanel
```