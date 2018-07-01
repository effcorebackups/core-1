

SERVER SOFTWARE REQUIREMENTS
---------------------------------------------------------------------

Priority Operating System:
- Unix (FreeBSD)
Priority Web Server:
- Apache v2.2+
- nginx v1.10+
Server-side scripting language:
- PHP v.5.6+
- PHP v.7.1+
Storage system:
- MySQL v5.6+
- SQLite v3+


Apache 2.4 requires the following modules:
- access_compat_module
- authz_core_module
- dir_module
- log_config_module
- mime_module
- rewrite_module
- php5_module|php7_module

Otherwise you get "500 Internal Server Error".
See Apache error log.


PHP 5.6 requires the following modules:
- php_pdo_sqlite|php_pdo_mysql


HARDWARE REQUIREMENTS FOR SERVERS WITH THE BEST PERFORMANCE
---------------------------------------------------------------------

Memory: DDR4 ECC or newer
Hard drive: SSD RAID on PCI-Express or newer
CPU: Intel Xeon/i7/i9 or newer


WEB CLIENTS SUPPORT
---------------------------------------------------------------------

- Safari (macOS, iOS 8+);
- Opera;
- Firefox;
- Chrome;
- MS IE 9+;
- MS Edge;
- other modern browsers.


RECOMMENDATION SOFTWARE FOR DEVELOPMENT
---------------------------------------------------------------------

- Coda (mac) for working with code|markup|styles.
- Tower (mac|win) for working git.
- Charles (mac|win|linux) for debug HTTP/S traffic and validate the markup.
- Kaleidoscope (mac) for search differences in code.
- SequelPro (mac) for working with MySQL databases.
- Aster SQLite Manager (mac) for working with SQLite databases.
- StarUML (mac|win|linux) for working with UML diagrams.
- BoxySVG (mac|win) for working with SVG vector graphics.
- Monodraw (mac) for working with pseudo-graphics.
- ColorSnapper (mac) for pick the "right" colors.


SECURITY RECOMMENDATIONS
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
- We recommend to using TLS v1.1/1.2 or higher.

