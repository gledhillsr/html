Instructions for when Steve forgets ;-)
  1) config.php is in .gitignore  (a copy without passwords is saved in config_secure.php)
  2) editing directory is under $HOME, after editing and commit.  Then copy to execution directory
     sudo cp /Users/brighton/nspOnline/html/screenshots/* /Library/WebServer/Documents/
  3) phpMyAdmin is NOT saved in the git repo


Mac Execution directory
/Library/WebServer/Documents

Mac 'git' editing directory
~/nspOnline/html/screenshots

remember to do a "git pull" before starting to update

