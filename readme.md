Concrete Ostrich Web Page
=============

Installing
----------

Clone repository to web server's document path and run composer update
```
cd /var/www/
git clone https://github.com/tulinkry/concreteostrich
cd concreteostrich
composer update

# log and temp must be writable by the web server
sudo chown -R www-data:www-data {log,temp}
```

Generate database schema
```
cd /var/www/concreteostrich
php www/index.php orm:schema-tool:create
```

Create local configuration
```
cd /var/www/concreteostrich/app/config
nano config.local.neon
```

```txt
common:
	parameters:
		database:
			driver: pdo_mysql
			dsn: 'pdo_mysql:host=127.0.0.1;dbname=test'
			host: localhost
			user: root
			password:
			dbname: concreteostrich
			charset: utf8
			collation: utf8_czech_ci
			options:
				lazy: yes

	facebook:
		appId: "your facebook secret"
		appSecret: "your app secret"

	database:
		dsn: %database.dsn%
		user: %database.user%
		password: %database.password%
		options: %database.options%

	doctrine:
		user: %database.user%
		password: %database.password%
		dbname: %database.dbname%
		host: %database.host%
		charset: %database.charset%
		driver: %database.driver%
		proxyDir: %appDir%/proxies
		autoGenerateProxyClasses: false
		types: [ Tulinkry\Model\Doctrine\Types\DateTimeType ]

	services:
		authenticator: Nette\Security\SimpleAuthenticator([
				'admin': examplepass
			])

development < common:

console < common:

production < common:
	parameters:
		database:
			driver: pdo_mysql
			dsn: ---
			host: ---
			user: ---
			password: ---
			dbname: ---
			charset: utf8
			collation: utf8_czech_ci
			options:
				lazy: yes
```