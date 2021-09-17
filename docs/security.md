

Security recommendations
=====================================================================

- Many web hosting services are still using software that is no longer supported.
  For example: PHP before version 7 or MySQL before version 5.6.
  This is bad for security and such services need to be updated.
  Learn more about supported PHP versions: http://php.net/supported-versions.php
  Review the Oracle Lifetime Support Policy to determine the lifecycle of MySQL.
- You can get maximum protection against internal threats if you use your
  own server in an isolated area with access control.
  If you are hosting a server in a data center in a shared rack,
  there is no guarantee against external access to ports or hard drives.
  It is recommended to use hosting services with servers located in
  European data centers.
- It is recommended to use hardware firewalls based on open source firmware (e.g.
  NanoBSD and other). Be aware that some hardware firewalls may contain backdoors.
- It is not recommended to buy SSL certificates with pre-generated
  private keys from a CA (Certificate Authority).
  There is no warranties that your private key will not fall into third hands.
  You can use CSR procedure (Certificate Signing Request):
  - generate public and private keys with identifying information on your side;
  - send a public key with identifying information to the CA to sign the public key.
- It is not recommended to use public email services to recover passwords
  and send important mail. It is recommended that you use your own server for mail
  services and use a different server for each service (if possible).
- It is recommended to using the secure hardware crypto-processor
  for storing the private keys.
- It is recommended to using two-factor authentication.
- It is recommended to using TLS v1.2 or higher.

