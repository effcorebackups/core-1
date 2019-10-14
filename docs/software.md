

Supported Software
=====================================================================


Clients Software
---------------------------------------------------------------------

The following clients are supported:
- Apple Safari v10+;
- Apple Mobile Safari on iOS v8+;
- Opera           (modern versions);
- Mozilla Firefox (modern versions);
- Google Chrome   (modern versions);
- Microsoft Internet Explorer v9+;
- Microsoft Edge.


Server Software
---------------------------------------------------------------------

Operating Systems:
- UNIX (Apple macOS, FreeBSD) [priority];
- Linux;
- Microsoft Windows.

Architectures:
- x86;
- x64.

Web Servers:
- Apache v2.4+ [priority];
- NGINX v1.10+ [priority];
- Internet Information Services (IIS) v7.5+.

Server-side scripting language:
- PHP v7.1+.

Storage systems:
- MySQL v5.6+;
- SQLite v3.6.19+.


Apache
---------------------------------------------------------------------

To enable Apache, do the following:
- enable module "php7_module";
- enable module "rewrite_module";
- enable module "dir_module";
- enable module "mime_module".

If you got "500 Internal Server Error" see the Apache error log.


NGINX
---------------------------------------------------------------------

To enable NGINX, do the following:
- enable "php-fpm.conf";
- run "php-fpm";
- in ".nginx" replace placeholders started with "%%_" to real values.

If you got "502 Bad Gateway" check if php-fpm is running.


Internet Information Services (IIS)
---------------------------------------------------------------------

To enable IIS (for v7.5), do the following:
- in "Turn Windows features on or off" enable option "IIS → WWW Services → Application Development Features → CGI";
- in "Turn Windows features on or off" enable option "IIS → WWW Services → Common HTTP Features → Static Content";
- in "IIS Manager → Handler Mappings" add new "Module Mapping" with parameters:
  - "Request Path = *.php";
  - "Module = FastCgiModule";
  - "Executable = {PHP_ROOT}\php-cgi.exe";
- install the module "URL Rewrite" v7.2.2 (7.1.761.0) or newer
  from IIS official site.

If you got "500 Internal Server Error" check "web.config"
in www root (section "rewrite").


PHP
---------------------------------------------------------------------

    | name         | is enabled | description             |
    |-----------------------------------------------------|
    | Core         | always     |                         |
    | date         | always     |                         |
    | fileinfo     | by default |                         |
    | filter       | by default |                         |
    | hash         | by default |                         |
    | mbstring     | no         |                         |
    | pcre         | always     |                         |
    | SPL          | always     |                         |
    | standard     | always     |                         |
    | pdo_mysql    | no         | for working with MySQL  |
    | pdo_sqlite   | no         | for working with SQLite |
    | Zend OPCache | no         | for best performance    |
    | curl         | no         | for development         |
    | json         | always     | for development         |


SQLite
---------------------------------------------------------------------

SQLite support foreign keys when:
- it's not older than v3.6.19 and
- it was compiled without SQLITE_OMIT_FOREIGN_KEY
  and SQLITE_OMIT_TRIGGER definitions.


Recommended Development Software
=====================================================================

Only high quality licensed expensive software was used for create the project.
It's recommended to use the next software:
- macOS High Sierra or newer for using quality software and make development environment;
- Coda, Nova (mac) for working with code|markup|styles and searching in the code;
- Tower (mac|win) for working with Git;
- Kaleidoscope (mac) for search differences in code;
- Transmit (mac) for working with FTP;
- SequelPro (mac) for working with MySQL databases;
- SQLiteFlow (mac) for working with SQLite databases;
- Neor Profile SQL (mac|win|linux) for debug SQL queries;
- Charles (mac|win|linux) for debug HTTP|S traffic and validate the markup;
- Proxie (mac) for debug HTTP|S traffic;
- Rested (mac) for debug HTTP traffic;
- StarUML (mac|win|linux) for working with UML diagrams;
- BoxySVG (mac|win) for working with SVG vector graphics;
- Monodraw (mac) for working with pseudo-graphics;
- ColorSnapper (mac) for pick the "right" colors;
- RegExRX (mac|win) for improve your regular expressions;
- VMWare Fusion Pro (mac) for working with virtual machines;
- Find Any Files (mac) for searching in the code (note: can not search is some cases);
- VisualGrep (mac) for searching in the code.


Incompatibility with third-party software
=====================================================================

The following software is incompatible:
- Kaspersky Internet Security browser plugin can insert to page
  some CSS with strings like "appearance: checkbox" and etc.
  This string can break your own styles of form elements like
  checkboxes, radios and other. Developers can not do anything
  against violating web standards from third side.
  Just ignore Kaspersky Internet Security.


Applications with subscription
=====================================================================

Our project condemns all software (not services) which distributed
only by subscription, without the possibility of buying a not time-limited
version. People are not cows which they can to milk! We urge you to
ignore such software!


Trademarks
=====================================================================

- Apache HTTP Server, Apache, and the Apache feather logo are trademarks of The Apache Software Foundation.
- Apple®, iPad®, iPad Air®, iPad mini™, iPad Pro®, iPhone®, macOS®, OS X®, Retina®, Retina HD®, Safari® are trademarks of Apple Inc., registered in the U.S. and other countries.
- BoxySVG is registered trademarks or trademarks of Jarosław Foksa.
- Charles is registered trademarks or trademarks of XK72.
- Coda, Nova, Transmit is registered trademarks or trademarks of Panic inc.
- ColorSnapper is registered trademarks or trademarks of Koole Sache.
- Find Any Files is registered trademarks or trademarks of Thomas Tempelmann.
- Firefox® is registered trademarks of Mozilla Foundation.
- FreeBSD is a registered trademark of The FreeBSD Foundation.
- Google™, Google Chrome™ are registered trademarks of Google Inc.
- iOS is a trademark or registered trademark of Cisco in the U.S. and other countries and is used by Apple under license.
- Kaleidoscope is registered trademarks or trademarks of Black Pixel LLC.
- Microsoft, Windows®, Internet Explorer®, Edge®, Internet Information Services (IIS) are registered trademarks or trademarks of Microsoft Corporation in the U.S. and/or other countries.
- Monodraw is registered trademarks or trademarks of Helftone Ltd.
- MySQL is registered trademarks or trademarks of Oracle Corporation and/or its affiliates.
- Neor Profile SQL is registered trademarks or trademarks of Neor LLC.
- NGINX is registered trademarks of Nginx Software Inc.
- Opera® is registered trademarks or trademarks of Opera Software AS in Norway, the European Union and other countries.
- Proxie is registered trademarks or trademarks of Proxie Team.
- RegExRX is registered trademarks or trademarks of MacTechnologies Consulting.
- Rested is registered trademarks or trademarks of Hello, Resolven Apps.
- SequelPro is registered trademarks or trademarks of Sequel Pro and CocoaMySQL team.
- SQLite is in the public domain and does not require a license.
- SQLiteFlow is registered trademarks or trademarks of HyperObjc.
- StarUML is registered trademarks or trademarks of MKLab.
- Tower is registered trademarks or trademarks of fournova Software GmbH.
- UNIX® is a registered trademark of The Open Group.
- VisualGrep is registered trademarks or trademarks of Davide Ficano.
- VMware Fusion® Pro is registered trademarks or trademarks of VMware.

