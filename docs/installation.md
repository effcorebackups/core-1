

Installation
=====================================================================

When using archive and FTP manager
---------------------------------------------------------------------

- Download the distribution kit of the system in "effcore-NNNN.zip"
  archive from the official site.
- Unpack archive to some local directory. All files should be
  unpacked (including invisible files like ".htaccess" and other).
  If you can not see the invisible files in your archiver, make them visible.
  If you can not see the invisible files in your OS, make them visible.
- Download utility like Cyberduck (mac|win), Transmit (mac),
  FileZilla (mac|win|linux) or other.
  Use them to establish a connection with the hosting server via FTP protocol.
  It's recommended to use the "SFTP" or "FTP with implicit SSL" or
  "FTP with TSL/SSL". In used utility enable the viewing of hidden files.
- Via utility go to the www root directory on the server and copy all
  files from the local directory into www root.
- Open a web browser and go to your domain (which you
  will use to deploy your site).
- If PHP is enabled you will see installation page of the system.
- If some required PHP modules are missing, you will receive
  a message about which modules should be enabled.
  Through the hosting panel, you can enable the missing
  modules and continue the installation.
  At the first time also recommended to enable "display_errors", "log_errors"
  and set to "E_ALL" the "error_reporting".
- If you will be using a MySQL database, make sure that you have
  one empty database and credentials for it.
  If you do not have a database, you can create a new database
  and user with known credentials for it through the hosting panel.
- If you get the message "unknown mysql server host [::1]" then try
  to use the value "127.0.0.1" in the "Hostname" field.

