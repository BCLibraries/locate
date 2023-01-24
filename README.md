# Locate

Locate is a Web application for displaying shelf maps at Boston College Libraries.

## Installation (for development)

### Pre-requisites

Local development requires:

* a supported version of [PHP](https://www.php.net/) (8.0, 8.1, or 8,2)
* [MySQL](https://www.mysql.com/) or [MariaDB](https://mariadb.org/)
* [NodeJS](https://nodejs.org/en/)
* [yarn](https://yarnpkg.com/)

### Creating the database

Inside MySQL/MariaDB, create a database called `locate` and a user `locate` with the password `locate_develop`:

```SQL
CREATE DATABASE locate;
CREATE USER 'locate'@'localhost' IDENTIFIED BY 'locate_develop';
GRANT ALL PRIVILEGES ON locate.* TO 'locate'@'localhost';
FLUSH PRIVILEGES;
```

From the command line, import the development data, providing the database user password when prompted:

```shell
mysql -u locate -p locate < locate-dev-data.sql
```
### Installing the application

Use the package manager [composer](https://getcomposer.org/) to install the Locate server:

```bash
git clone https://github.com/BCLibraries/locate.git
cd locate
composer install 
```

Install the required JavaScript with yarn and build the scripts:

```shell
yarn install
yarn encore dev
```

Install the [`symfony` CLI tool](https://symfony.com/download) to control the server.

Finally, find a copy of the development decryption key on the wiki and copy it into config/secrets/dev/.

## Running locally

Start Launch the server:

```bash
symfony server:start -d
```

Look up a [sample record](http://localhost:8000/map/ONL/PA6047+.N63+2021?title=Ancient+Latin+poetry+books+%3A+materiality+and+context+%2F&location_code=STACK&collection=Stacks&source=Alma).

To view the development server log:

```bash
symfony server:log
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)