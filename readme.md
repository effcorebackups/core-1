

Server software requirements
---------------------------------------------------------------------

Operating System:
- UNIX (macOS, FreeBSD) [priority]
- Linux
- Windows
Platform:
- x86
- x64
Web Server:
- Apache v2.4+ [priority]
- NGINX v1.10+ [priority]
- Internet Information Services (IIS) v7.5+
Server-side scripting language:
- PHP v.5.6+
- PHP v.7.1+
Storage system:
- MySQL v5.6+
- SQLite v3+


Apache 2.4 requires the following modules:
- php5_module|php7_module
- rewrite_module
- dir_module
- mime_module
If you got "500 Internal Server Error" see the Apache error log.


Internet Information Services (IIS) v7.5+ requires:
- install module "URL Rewrite" from IIS official site
- in "Turn Windows features on or off" set option "IIS / WWW Services / Application Development Features / CGI"
- in "Turn Windows features on or off" set option "IIS / WWW Services / Common HTTP Features / Static Content"
- in "IIS Manager / Handler Mappings" add new "Module Mapping" with parameters: "Request Path = *.php", "Module = FastCgiModule", "Executable = {PHP_ROOT}\php-cgi.exe"
If you got "500 Internal Server Error" check web.config (section "rewrite").


PHP 5.6 requires the following modules:
- php_pdo_sqlite|php_pdo_mysql


Git on Windows requires:
- run the command "git config --global core.autocrlf false"


Recommendations for improving performance
---------------------------------------------------------------------

Highly recommend to use the Solid State Drives (SSD).
Recommend to use the PHP OPcache.


Web clients support
---------------------------------------------------------------------

- Apple Safari v10+;
- Apple Mobile Safari iOS v8+;
- Opera modern versions;
- Mozilla Firefox modern versions;
- Google Chrome modern versions;
- Microsoft Internet Explorer 9+;
- Microsoft Edge.


Recommendation software for development
---------------------------------------------------------------------

- Coda (mac) for working with code|markup|styles.
- Tower (mac|win) for working git.
- Charles (mac|win|linux) for debug HTTP|S traffic and validate the markup.
- Kaleidoscope (mac) for search differences in code.
- SequelPro (mac) for working with MySQL databases.
- Aster SQLite Manager (mac) for working with SQLite databases.
- StarUML (mac|win|linux) for working with UML diagrams.
- BoxySVG (mac|win) for working with SVG vector graphics.
- Monodraw (mac) for working with pseudo-graphics.
- ColorSnapper (mac) for pick the "right" colors.


Security recommendations
---------------------------------------------------------------------

- We recommend to using own servers located in rooms with access control.
  Deployment of your servers in non-European data centers does not guarantee your privacy.
  You can get an internal network threat from different sniffers.
- We recommend to using hardware firewalls based on open firmware (e.g. NanoBSD and other).
  Remember that some hardware firewalls (not from Europe) may contain backdoors.
- We do not recommend to using non-European Certification authority (CA).
  There is no warranties that your private key will not fall into third hands.
- We do not recommend to using public email services for restoring passwords
  and sending important correspondence.
  We recommend to using your own dedicated server for email services
  and using different servers for each service.
- We recommend to using the secure hardware crypto-processor for storing the private keys.
- We recommend to using two-factor authentication.
- We recommend to using TLS v1.1|1.2 or higher.

