

Security recommendations
=====================================================================

- You can get maximum protection from internal threats only if you used
  own server in isolated area with access control.
  If you host the server in a data center in a shared rack
  there is no warranty from external access to the ports or hard drives.
  It's recommended to use server hosting in European data centers.
- It's recommended to using hardware firewalls based on open firmware (e.g.
  NanoBSD and other). Remember that some hardware firewalls (not from Europe)
  may contain backdoors.
- It's not recommended to buy SSL certificates with pre-generated private keys
  from CA (Certificate Authority). There is no warranties that your private key
  will not fall into third hands. You can use CSR (Certificate Signing Request)
  procedure:
  - generate public and private keys with identifying information on your side;
  - send the public key with the identifying information to the CA
    for sign the public key.
- It's not recommended to using public email services for restoring passwords
  and sending important correspondence. It's recommended to using your own
  server for email services and using different servers for each
  service (if it's possible).
- It's recommended to using the secure hardware crypto-processor for storing the private keys.
- It's recommended to using two-factor authentication.
- It's recommended to using TLS v1.2 or higher.

