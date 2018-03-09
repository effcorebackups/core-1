

SERVER SOFTWARE REQUIREMENTS
---------------------------------------------------------------------

Оperating system:
- Unix (FreeBSD)
- Linux
Web server:
- Apache v2.2+
- Apache v2.2+ with nginx v1.10+
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
See apache error log.


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


SECURITY RECOMENDATIONS
---------------------------------------------------------------------

- We recommend to using own servers located in rooms with access control.
  Deployment of your servers in USA/Russia/China data centers does not guarantee your privacy.
  You can get an internal network threat from different sniffers.
- We recomend to using hardrare firewalls based on open firmware (e.g. NanoBSD and other).
  Remember that some hardware firewalls (еspecially from the USA) may contain backdoors.
- We not recomend to using Certification authority (CA) hosted in USA/Russia/China.
  There is no warranties that your private key will not fall into the hands of
  special government services.
- We not recoment to using public email services for restoring passwords
  and sending important correspondence.
  We recommend to using your own dedicated server for email services
  and using different servers for each service.
- We recommend to using hardware secure cryptoprocessor for storing the private keys.
- We recommended to using two-factor authentication.
- We recommended to using TLS v1.1/1.2 or higher.

