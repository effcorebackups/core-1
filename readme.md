

Server software requirements
---------------------------------------------------------------------

Operating System:
- UNIX (macOS, FreeBSD) [priority]
- Linux
- Windows
Architecture:
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


Apache 2.4 requires:
- enable module "php5_module"|"php7_module"
- enable module "rewrite_module"
- enable module "dir_module"
- enable module "mime_module"
If you got "500 Internal Server Error" see the Apache error log.


NGINX requires:
- in ".nginx" (at the www root) replace placeholders (%%...) to real values
- enable "php-fpm"
If you got "502 Bad Gateway" check if php-fpm is running.


IIS requires (for v7.5):
- enable option "IIS ⇨ WWW Services ⇨ Application Development Features ⇨ CGI" in "Turn Windows features on or off"
- enable option "IIS ⇨ WWW Services ⇨ Common HTTP Features ⇨ Static Content" in "Turn Windows features on or off"
- add new "Module Mapping" with parameters "Request Path = *.php", "Module = FastCgiModule", "Executable = {PHP_ROOT}\php-cgi.exe" in "IIS Manager ⇨ Handler Mappings"
- install the module "URL Rewrite" (from IIS official site)
If you got "500 Internal Server Error" check "web.config" in www root (section "rewrite").


PHP 5.6 requires:
- enable module "SPL"
- enable module "date"
- enable module "fileinfo"
- enable module "filter"
- enable module "pcre"
- enable module "standard"
- enable module "pdo_sqlite"|"pdo_mysql"
- enable module "curl" (for development)
- enable module "json" (for development)


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


Recommended software for development
---------------------------------------------------------------------

- macOS Sierra and higher for using quality software and make development environment.
- Coda (mac) for working with code|markup|styles and searching in the code.
- Tower (mac|win) for working git.
- Charles (mac|win|linux) for debug HTTP|S traffic and validate the markup.
- Rested (mac) for debug HTTP|S traffic.
- Kaleidoscope (mac) for search differences in code.
- SequelPro (mac) for working with MySQL databases.
- Aster SQLite Manager (mac) for working with SQLite databases.
- StarUML (mac|win|linux) for working with UML diagrams.
- BoxySVG (mac|win) for working with SVG vector graphics.
- Monodraw (mac) for working with pseudo-graphics.
- ColorSnapper (mac) for pick the "right" colors.
- RegExRX (mac|win) for improve your regular expressions.
- VMWare Fusion Pro (mac) for working with virtual machines.
- Find Any Files (mac) for searching in the code (p.s. Coda can search better).


Security recommendations
---------------------------------------------------------------------

- We recommend to using own servers located in rooms with access control.
  Deployment of your servers in non-European data centers does not guarantee your privacy.
  You can get an internal network threat from different sniffers.
- We recommend to using hardware firewalls based on open firmware (e.g. NanoBSD and other).
  Remember that some hardware firewalls (not from Europe) may contain backdoors.
- Do not buy SSL certificates with with pre-generated private keys from CA (Certificate Authority).
  There is no warranties that your private key will not fall into third hands.
  You can use CSR (Certificate Signing Request) procedure:
  generate public and private keys with identifying information on your side
  and send the public key with the identifying information
  to the CA for sign the public key.
- We do not recommend to using public email services for restoring passwords
  and sending important correspondence.
  We recommend to using your own dedicated server for email services
  and using different servers for each service.
- We recommend to using the secure hardware crypto-processor for storing the private keys.
- We recommend to using two-factor authentication.
- We recommend to using TLS v1.2 or higher.

