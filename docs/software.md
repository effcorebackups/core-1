

Server software support
=====================================================================

Operating Systems:
- UNIX (macOS, FreeBSD) [priority]
- Linux
- Windows
Architectures:
- x86
- x64
Web Servers:
- Apache v2.4+ [priority]
- NGINX v1.10+ [priority]
- Internet Information Services (IIS) v7.5+
Server-side scripting language:
- PHP v7.1+
Storage systems:
- MySQL v5.6+
- SQLite v3.6.19+


Note: the many hostings still using the official discontinued software.
For example: PHP before version 7 or MySQL before version 5.6.
It's bad practice and we do not recommend to use such hostings.
Read more about PHP Supported Versions: http://php.net/supported-versions.php
Read Oracle Lifetime Support Policy for identifying the life cycle of MySQL.


Apache 2.4 requires
---------------------------------------------------------------------
- enable module "php7_module"
- enable module "rewrite_module"
- enable module "dir_module"
- enable module "mime_module"
If you got "500 Internal Server Error" see the Apache error log.


NGINX requires
---------------------------------------------------------------------
- enable "php-fpm.conf"
- run "php-fpm"
- in ".nginx" replace placeholders started with "%%_" to real values
If you got "502 Bad Gateway" check if php-fpm is running.


IIS requires (for v7.5)
---------------------------------------------------------------------
- enable option "IIS → WWW Services → Application Development Features → CGI" in "Turn Windows features on or off"
- enable option "IIS → WWW Services → Common HTTP Features → Static Content" in "Turn Windows features on or off"
- add new "Module Mapping" with parameters "Request Path = *.php", "Module = FastCgiModule", "Executable = {PHP_ROOT}\php-cgi.exe" in "IIS Manager → Handler Mappings"
- install the module "URL Rewrite" (from IIS official site)
If you got "500 Internal Server Error" check "web.config" in www root (section "rewrite").


PHP requires
---------------------------------------------------------------------
- enable module "Core"                                   [always enabled]
- enable module "date"                                   [always enabled]
- enable module "fileinfo"                               [enabled by default]
- enable module "filter"                                 [enabled by default]
- enable module "mbstring"
- enable module "pcre"                                   [always enabled]
- enable module "SPL"                                    [always enabled]
- enable module "standard"                               [always enabled]
- enable module "pdo_mysql"    (for working with MySQL)
- enable module "pdo_sqlite"   (for working with SQLite)
- enable module "Zend OPcache" (for best performance)
- enable module "curl"         (for development)
- enable module "json"         (for development)         [always enabled]


SQLite requires
---------------------------------------------------------------------
SQLite support foreign keys when:
- it is not older than v3.6.19 and
- it was compiled without SQLITE_OMIT_FOREIGN_KEY
  and SQLITE_OMIT_TRIGGER definitions


Client software support
=====================================================================

- Apple Safari v10+
- Apple Mobile Safari on iOS v8+
- Opera           (modern versions)
- Mozilla Firefox (modern versions)
- Google Chrome   (modern versions)
- Microsoft Internet Explorer v9+
- Microsoft Edge


Recommended software for development
=====================================================================

- macOS High Sierra or newer for using quality software and make development environment.
- Coda (mac) for working with code|markup|styles and searching in the code.
- Tower (mac|win) for working with Git.
- Charles (mac|win|linux) for debug HTTP|S traffic and validate the markup.
- Proxie (mac) for debug HTTP|S traffic.
- Rested (mac) for debug HTTP traffic.
- Kaleidoscope (mac) for search differences in code.
- SequelPro (mac) for working with MySQL databases.
- StarUML (mac|win|linux) for working with UML diagrams.
- BoxySVG (mac|win) for working with SVG vector graphics.
- Monodraw (mac) for working with pseudo-graphics.
- ColorSnapper (mac) for pick the "right" colors.
- RegExRX (mac|win) for improve your regular expressions.
- VMWare Fusion Pro (mac) for working with virtual machines.
- Find Any Files (mac) for searching in the code (note: can not search is some cases).
- VisualGrep (mac) for searching in the code.
- Neor Profile SQL (mac|win|linux) for debug SQL queries.


Improving performance
=====================================================================

Highly recommend to use the SSD (Solid State Drives).
Recommend to enable the PHP OPcache.

