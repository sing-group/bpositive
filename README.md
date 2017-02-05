B+
==

[B+](http://bpositive.i3s.up.pt) is a database that has been specifically designed to store and show the information
contained in [ADOPS](https://sing.ei.uvigo.es/ADOPS/) (Automatic Detection Of Positively Selected Sites) project files.

Team
----
This project is an idea and is developed by:
* Jorge Vieira [Molecular Evolution Group](http://evolution.ibmc.up.pt)
* José Sousa [i3S](http://www.i3s.up.pt/)
* Miguel Reboiro-Jato [SING Group](http://www.sing-group.org)
* Noé Vázquez González [SING Group](http://www.sing-group.org)
* Bárbara Amorim [i3S](http://www.i3s.up.pt/)
* Cristina P. Vieira [Molecular Evolution Group](http://evolution.ibmc.up.pt)
* André Torres [i3S](http://www.i3s.up.pt/)
* Hugo López-Fernández [SING Group](http://www.sing-group.org)
* Florentino Fdez-Riverola [SING Group](http://www.sing-group.org)

## Downloading and Installing B+
### 0. Prerequisites

To install B+, the server must meet the following requirements:
* Web Server with PHP capabilities. For example, Apache >= 2.4.
* PHP >= 5.6.4.
* OpenSSL PHP Extension.
* PDO PHP Extension.
* Mbstring PHP Extension.
* Tokenizer PHP Extension.
* XML PHP Extension.
* Database Server. For example, MySQL Server 5.5.
* Database driver for PHP.
* [Composer](https://getcomposer.org/).

### 1. Download

Clone B+ from [GitHub](https://github.com/sing-group/bpositive.git) (`git clone https://github.com/sing-group/bpositive.git`) or download a release version from [here](https://github.com/sing-group/bpositive/releases). Place all files in a directory you can use as document root of your web server.

### 2. Web Server configuration

The Web Server's document root must be the *public* directory of the B+ code.
*AllowOverride* must be enabled in this directory.

### 3. Data cofiguration

The database and all the tables needed can be created using de SQL script included in [database/sql/bpositive.sql](database/sql/bpositive.sql).

Compressed files for each project must be stored under *storage/app* directory.
By default, there is a disk named 'bpositive' configured to load files from *app/bpositive*,
with this default configuration, files must be stored in *storage/app/bpositive/files*.
Storage can be customized in [config/filesystems.php](config/filesystems.php).  

### 3. Install

All dependencies in B+ are managed using [Composer](https://getcomposer.org/). Just open a terminal on the root directory
of B+ and run:

`composer install`

### 4. B+ configuration

All the parameters can be customized in the *.env* file. At least, you need to customize the following parameters:

* APP_KEY: Make sure this key is set and remain secret.
* APP_DEBUG: If enabled, errors will be displayed on the client browser*
* APP_LOG_LEVEL: Sets the log level.
* APP_URL: URL where B+ will be runing.
* DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD: Database connection parameters.

Last but not least, directories within the *storage* and *bootstrap/cache* should be writable by the Web Server.

### 5. B+ Web Page

If you followed all the previous steps, B+ should be accesible from your Web Server root URL using any Web Browser with
JavaScript enabled.
