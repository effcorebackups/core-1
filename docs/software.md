

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
- enable module `php7_module`;
- enable module `rewrite_module`;
- enable module `dir_module`;
- enable module `mime_module`.

If you got "500 Internal Server Error" see the Apache error log.


NGINX
---------------------------------------------------------------------

To enable NGINX, do the following (example for `/etc/nginx/`):
- make "SETUP ACTIONS" from `.nginx`;
- run `php-fpm`.

If you got "502 Bad Gateway" check if php-fpm is running.


Internet Information Services (IIS)
---------------------------------------------------------------------

To enable IIS (for v7.5), do the following:
- in "Turn Windows features on or off" enable option _Internet Information Services → Web Management Tools → IIS Management Console_;
- in "Turn Windows features on or off" enable option _Internet Information Services → World Wide Web Services → Application Development Features → CGI_;
- in "Turn Windows features on or off" enable option _Internet Information Services → World Wide Web Services → Common HTTP Features → Static Content_;
- in _IIS Manager → Handler Mappings_ select "Add Module Mapping" and fill in the following fields:
  - `Request Path = *.php`
  - `Module = FastCgiModule`
  - `Executable = {PHP_ROOT}\php-cgi.exe`
  - `Name = PHP`
- install the module "URL Rewrite Module 2.1" or newer
  from "The Official Microsoft IIS Site".

If you got "500 Internal Server Error" check `web.config`
in web root (section `rewrite`).


PHP
---------------------------------------------------------------------

The following extensions are used:

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

The following parameters are recommended for optimal performance:
- `memory_limit: 256M`
- `max_file_uploads: 20`
- `upload_max_filesize: 1G`
- `post_max_size: 1G`
- `max_input_time: 60`
- `max_execution_time: 60`


SQLite
---------------------------------------------------------------------

SQLite support foreign keys when:
- it is not older than v3.6.19 and
- it was compiled without `SQLITE_OMIT_FOREIGN_KEY`
  and `SQLITE_OMIT_TRIGGER` definitions.


Recommended Development Software
=====================================================================

Only high quality licensed expensive software was used for create the project.
It is recommended to use the next software:
- **macOS** for using quality software and make development environment;
- **Nova + Intelephense** (mac) for working with code|markup|styles and searching in the code;
- **PHPStorm** (mac|win|linux) for working with code|markup|styles and searching in the code;
- **Textastic** (mac) for working with huge text files;
- **Hex Fiend** (mac) for working with hex files;
- **Find Any Files** (mac) for searching in the code;
- **VisualGrep** (mac) for searching in the code;
- **RegExRX** (mac|win) for improve your regular expressions;
- **Tower** (mac|win) for working with Git;
- **Kaleidoscope** (mac) for search differences in code;
- **Transmit** (mac) for working with FTP;
- **SequelPro** / **Sequel Ace** (mac) for working with MySQL databases;
- **SQLiteFlow** (mac) for working with SQLite databases;
- **Neor Profile SQL** (mac|win|linux) for debug MySQL queries;
- **Charles** (mac|win|linux) for debug HTTP|S traffic and validate the markup;
- **Proxie** (mac) for debug HTTP|S traffic;
- **Rested** (mac) for debug HTTP traffic;
- **BoxySVG** (mac|win) for working with SVG vector graphics;
- **Monodraw** (mac) for working with pseudo-graphics and diagrams;
- **StarUML** (mac|win|linux) for working with UML diagrams;
- **OmniGraffle** (mac) for working with diagrams;
- **WebToLayers** (mac) for take screenshots of web pages;
- **ColorSnapper** (mac) for pick the "right" colors;
- **Paletter** (mac) for generating a color palette;
- **MetaImage** (mac) for working with images metadata;
- **VMWare Fusion Pro** (mac) for working with virtual machines.


Applications with subscription
---------------------------------------------------------------------

Our project condemns all software (not services) which distributed
only by subscription, without the possibility of buying a not time-limited
version. People are not cows which they can to milk! We urge you to
ignore such software!


Trademarks
=====================================================================

- Apache HTTP Server, Apache, and the Apache feather logo are trademarks of The Apache Software Foundation.
- Apple®, iPad®, iPad Air®, iPad mini™, iPad Pro®, iPhone®, macOS®, OS X®, Retina®, Retina HD®, Safari® are trademarks of Apple Inc., registered in the U.S. and other countries.
- BoxySVG is registered trademark or trademarks of Jarosław Foksa.
- Charles is registered trademark or trademarks of XK72.
- ColorSnapper is registered trademark or trademarks of Koole Sache.
- Find Any Files is registered trademark or trademarks of Thomas Tempelmann.
- Firefox® is registered trademark of Mozilla Foundation.
- FreeBSD® is registered trademark of The FreeBSD Foundation.
- Google™, Google Chrome™ are registered trademarks of Google Inc.
- Hex Fiend is registered trademark of ridiculous_fish.
- iOS is trademark or registered trademark of Cisco in the U.S. and other countries and is used by Apple under license.
- Kaleidoscope is registered trademark or trademarks of Leitmotif GmbH.
- Microsoft, Windows®, Internet Explorer®, Edge®, Internet Information Services (IIS) are registered trademarks or trademarks of Microsoft Corporation in the U.S. and/or other countries.
- Monodraw is registered trademark or trademarks of Helftone Ltd.
- MySQL™ is registered trademark or trademarks of Oracle Corporation and/or its affiliates.
- Neor Profile SQL is registered trademark or trademarks of Neor LLC.
- NGINX® is registered trademark of Nginx Software Inc.
- Nova, Transmit is registered trademark or trademarks of Panic inc.
- OmniGraffle is registered trademark of The Omni Group.
- Opera® is registered trademark or trademarks of Opera Software AS.
- Paletter is registered trademark or trademarks of Toys, Inc.
- PHP is registered trademark or trademarks of PHP Group.
- PHPStorm is registered trademark of JetBrains s.r.o.
- Proxie is registered trademark or trademarks of Proxie Team.
- RegExRX is registered trademark or trademarks of MacTechnologies Consulting.
- Rested is registered trademark or trademarks of Hello, Resolven Apps.
- SequelPro is registered trademark or trademarks of Sequel Pro and CocoaMySQL team.
- SQLite is in the public domain and does not require a license.
- SQLiteFlow is registered trademark or trademarks of HyperObjc.
- StarUML is registered trademark or trademarks of MKLab.
- Textastic is registered trademark of Alexander Blach.
- Tower is registered trademark or trademarks of fournova Software GmbH.
- UNIX® is registered trademark of The Open Group.
- VisualGrep is registered trademark or trademarks of Davide Ficano.
- VMware Fusion® Pro is registered trademark or trademarks of VMware, Inc.
- WebToLayers, MetaImage is registered trademark or trademarks of NeededApps.

