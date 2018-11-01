

Security recommendations
=====================================================================

- You can get protection form internal threats only if you used
  own server in isolated area with access control.
  If you host the server in a data center in a shared rack
  there is no warranty from external access to the ports or hard drives.
  We recommend hosting the server in European data centers.
- We recommend to using hardware firewalls based on open firmware (e.g. NanoBSD and other).
  Remember that some hardware firewalls (not from Europe) may contain backdoors.
- Do not buy SSL certificates with pre-generated private keys from CA (Certificate Authority).
  There is no warranties that your private key will not fall into third hands.
  You can use CSR (Certificate Signing Request) procedure:
  generate public and private keys with identifying information on your side
  and then send the public key with the identifying information
  to the CA for sign the public key.
- We do not recommend to using public email services for restoring passwords
  and sending important correspondence.
  We recommend to using your own server for email services
  and using different servers for each service (if it's possible).
- We recommend to using the secure hardware crypto-processor for storing the private keys.
- We recommend to using two-factor authentication.
- We recommend to using TLS v1.2 or higher.

