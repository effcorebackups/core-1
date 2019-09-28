

Установка
=====================================================================

Когда используется архив и FTP-клиент
---------------------------------------------------------------------

Действия перед установкой:
- Когда Вы будете использовать MySQL для хранения данных, проверьте,
  есть ли у Вас учётные данные для этого (логин, пароль, имя базы данных).
  Если у Вас нет учётных данных, проверьте, есть ли у Вас база данных
  и учётная запись пользователя для неё через панель хостинга (например,
  DirectAdmin, ISPmanager, cPanel, Plesk и другие).
  Если у Вас нет базы данных, создайте одну, а также учётную запись
  пользователя для работы с ней.
- Check if you have credentials (hostname, login, password)
  for FTP service which running on hosting web server.
  If you do not have credentials, create new FTP user account
  or reset the password of exist account via hosting service panel.
- Check if you have FTP Client for transfer files
  to the web server. For example: Transmit (mac),
  Cyberduck (mac|win), FileZilla (mac|win|linux) and etc.  
- Check if you enabled visibility of hidden files
  in the archiver, operating system and FTP Client.

Actions for installations:
- Download the distribution kit of the system in archive from the
  official site of this project.
- Unpack archive of the system to some local directory.
  All files should be unpacked (including invisible files like ".htaccess" and other).
  If you can not see the invisible files in your archiver, make them visible.
  If you can not see the invisible files in your OS, make them visible.
- Establish a connection with the web server via FTP protocol.
  It's recommended to use the "SFTP" or "FTP with implicit SSL" or
  "FTP with TSL/SSL".
- Via FTP client go to the www root directory on the web server
  and copy all files of this system from the local directory
  into www root.
- Open a web browser and go to your domain (which you
  will use to deploy your site).
- If PHP is enabled you will see installation page of the system.
- If some required PHP modules are missing, you will receive
  a message about which modules should be enabled.
  Through the hosting service panel, you can enable the missing
  modules and continue the installation.
  At the first time also recommended to enable "display_errors",
  "log_errors" and set to "E_ALL" the "error_reporting".
- If you get the message "unknown mysql server host [::1]" then try
  to use the value "127.0.0.1" or "localhost" in the "Hostname" field.

