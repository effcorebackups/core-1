

Security
---------------------------------------------------------------------

Security is an important factor in the system.

The following attack vectors were reviewed:

- An attacker can try to get access to SQLite data, system keys.
- An attacker can try to get access to files such as ".htaccess", ".nginx", "web.config",
  directories "/.git/", "/dynamic/tmp/.git_restore-system/" and others.
- An attacker can try to find a weak point in the directives in the web server
  settings file (".htaccess", ".nginx", "web.config") if there are many directives and/or
  they are written incorrectly and/or they do not predict all possible situations and/or
  after a software update the rules for formatting directives in such files have changed.
- An attacker can try to access files outside the web root directory by manipulating such
  combinations as "./", "../", "~/", "//" and others.
- An attacker can try to enter data for SQL injection into form fields.
- An attacker can try to spoof the session identifier.
- An attacker can try to spoof the form validation identifier.
- An attacker can try to submit a pre-filled form multiple times (authentication form
  "form_login", new user registration form "form_registration", password recovery
  form "form_recovery") in order to brute-force the email address and/or username
  and/or password or bypassing the CAPTCHA.
- An attacker can try to unblock blocked fields on a form using a browser.
- An attacker can try to send a larger field value than allowed by the
  attributes "maxlength", "max", "step", "min", "max" and others.
- An attacker can try to make GET/POST requests with characters that are not
  allowed by RFC standards.
- An attacker can try to make GET/POST requests in which the dimensions of the
  transmitted arrays or their indices may not correspond to the acceptable ones.
- An attacker can try to substitute invalid arguments in the
  URL request (http://domain/path?QUERY).
- An attacker can try to insert malicious code (JavaScript) into the form fields.
- An attacker can try to upload an image with malicious content.
- An attacker can try to gain access to a user profile with temporary access
  to his workplace.


Implemented security solutions
---------------------------------------------------------------------

File vector:

- Web server settings files (".htaccess", ".nginx", "web.config") contain directives
  that prohibit user agent access to directories "/dynamic/cache/", "/dynamic/data/",
  "/dynamic/logs/". An attacker will not be able to access SQLite data, system keys.
- Web server configuration files (".htaccess", ".nginx", "web.config") contain a directive
  that prohibits user agent access to any files or directories whose name begins with
  the "." at any nesting level. An attacker will not be able to access such files as ".htaccess",
  ".nginx", directories "/.git/", "/dynamic/tmp/.git_restore-system/" and others.
- Web server configuration files (".htaccess", ".nginx", "web.config") contain a directive
  that prohibits user agent access to the "web.config". An attacker would be unable
  to access the "web.config" file.
- Web server configuration files (".htaccess", ".nginx", "web.config") contain a directive
  that provides a single entry point to the "index.php" file, which guarantees a single and
  consistent approach to protecting any file on the system.
- Additionally, at the PHP level, the system provides restriction of user agent access
  to the web server file system outside the web root directory.
- Additionally, at the PHP level, the system provides filtering in URL requests that
  contain such combinations as "./", "../", "~/", "//".
- Additionally, at the PHP level, the system provides user agent access only to the
  actually existing file (except for the "kind: virtual" type).
  In this case, the PHP script must have the right to read the requested file.
- At the PHP level, the system restricts access to files whose type is
  set as "protected" ("kind: protected").
- At the PHP level, the system can organize additional restrictions on access to any type
  of file (at the request of the developer through the "on_load" event handler).

Vector DB:

- Before executing any SQL query, it is prepared, which excludes
  the possibility of SQL injection.

Session vector:

- The session identifier "session_id" is signed with the "settings/user/keys/session" key
  located on the web server side, which makes it impossible to forge.
- The session identifier can contain the name of the user agent and its IP address (during
  authentication, the user himself determines whether to bind his session to his IP address)
  which makes hijacking the session identifier a meaningless procedure — the attacker's
  request from a different IP address will be ignored.
- The session identifier may have a short-term validity (during authentication, the user
  himself determines whether his session is short-lived or not).
- The session identifier is not cross-domain by default, i.e. not transferred
  to third party domains.

HTTP request vector:

- The form validation identifier "validation_id" is signed with
  the "settings/user/keys/form_validation" key located on the web server side, which makes
  it impossible to forge.
- The validation identifier of the form contains the name of the user agent and its IP address,
  which makes intercepting the identifier a meaningless procedure — an attacker's request
  from a different IP address will be ignored.
  The lifetime of the validation identifier is limited in time.
- The data validation process is performed on the web server side and an attempt to forge
  them on the client side is pointless (for example, trying to unlock locked fields
  on a form, or trying to fill in invalid data).
- The data of the GET/POST request is checked for compliance with RFC standards and an attempt to
  call a URL with invalid characters will be processed correctly.
- The data of the GET/POST request is checked for the correspondence of the dimensions
  of the arrays and their indices.
- URL query arguments (http://domain/path?QUERY) are filtered.
- The basic module CAPTCHA minimizes the possibility of brute-force email address and password
  in the authentication form "form_login", brute-force email and/or username in the
  new user registration form "form_registration", brute-force email address in the access
  recovery form "form_recovery" and registration of spam robots in the new user registration
  form "form_registration".
- Input filtering prevents malicious code (JavaScript) from entering to the system.
- Output filtering prevents malicious code (JavaScript) from being displayed.
- Image filtering eliminates the possibility of displaying images with malicious
  content to users.

Organizational vector:

- The system of permissions and roles provides the issuance of extended rights
  only to authorized persons.
- Only administrators have access to the profiles of other users (for example,
  users with the "Administrators" role).
- All email addresses of users are hidden from other system participants and it is
  not possible to calculate the correspondence between the user's name and his
  email address. Thus, it will be impossible to remotely reset the password of
  another user or try to guess the password for his account.
- It is possible to make changes to the user's registration data only if the
  password from his account is known — this minimizes the intra-system
  threat (security threat from personnel side).

Functional vector:

- Implemented the ability to work without JavaScript.
- Implemented the ability to get the "Sequence hash" and "Data hash" in the system console.
- Determinism in the system work — with the same input parameters, the same result is
  returned regardless of the platform and as a result — complete rejection of functions
  that depend on the environment (for example, "setlocale" and others).
- Using in code the identity operator '===' instead of simple equality '==', as a result,
  is excluded a dangerous situation such as: (0 == 'some_text') === true;
- In the code in the "foreach" loops, the exclusion of references to the "key" and/or "value"
  variables with the subsequent modification of the array structure using these variables,
  which could lead to a skew of the array structure and destruction of the "key + value"
  relationship: foreach ($array as $key => &$value) if ($some) unset($value);


Security recommendations
---------------------------------------------------------------------

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

