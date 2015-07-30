ASSUME using Windows 2000 or XP, on a NTFS formatted drive (more secure than FAT)
ASSUME everything is to be installed into c:\web

---assumed already done---
set the command completion character in regedit (not necessary, but nice)
run regit, search for "CompletionChar" and change the "0" to a "9" (tab key)

copied everything from backup CD <drive>:\web to c:\web


--- First (WinZip) ---
install the Winzip from C:\web\AppsToInstall\WinZip\winzip80.exe
use the default location "C:\Program Files\WinZip"

--- Second (Java)---
install jdk1.4 from "C:\web\AppsToInstall\JDK1.4\j2sdk-1_4_0_02-windows-i586.exe"
to location c:\jdk1.4
main jar is c:\jdk1.4\lib\rt.jar

--- Forth (environment) ---
Fixup enviroment variables
right click on "My Computer", select "properties",
click on Advanced Tab, click on "Enviroment Variables..."
the set the following as System Variables

CATALINA_HOME = C:\web\tomcat       (tomcat will be installed later)
CLASSPATH = .;c:\jdk1.4.2_04\jre\lib\rt.jar;
            c:\web\tomcat\common\lib\servlet.jar;
            c:\web\tomcat\common\lib\mail.jar;
            c:\web\tomcat\common\lib\activation.jar
PATH = ;c:\jdk1.4.2_02\bin;             (append this)
JAVA_HOME = c:\jdk1.4.2_02

--- Fith (PHP) ---
copy the PHP stuff
make sure c:\php is copied
copy my.ini, php.ini, c:\php\php4apache2.dll c:\php\php4ts.dll to c:\windows
edit my.ini & php.ini to fix any path problems
ie. set:
    register_globals = On
    extension_dir = "c:\php"
    extension=phpchartdir421.dll
    extension=libpdf_php.dll


--- Sixth (Apache 2) ---
install c:\web\AppsToInstall\apache_2.0.45-win32-x86-no_ssl.msi
 set the install directory for Apache to c:\  ( It will install to to c:\Apache2 )
Now, you should now be able to run a browser and see the default page when
  you type in "localhost" for the URL
merge in changes from \web\AppsToInstall\Apache\httpd.conf" into \Apache2\conf\httpd.conf
use WinDiff to see the differences
Restart Apache
Now, you should get my static Web Pages  :-)

--- Seventh (Tomcat) ---
install Jakarta Tomcat 4.0.1 to "c:\web\tomcat" (***NOTE: this is NOT the default location ***)
   from C:\web\AppsToInstall\Jakarta-tomcat\jakarta-tomcat-4.0.1.exe
ALSO click to install as a servive (NT/2k/XP)
install to c:\web\tomcat4.0
go to the "services" dialog, and start "Apache Tomcat"
  test, within a browser, goto http://localhost:8080/
<*** Ski Patrol ONLY,  edit server.xml, and add the nspCode context.
Copy the Examples context and change the "Examples", to "nspCode" ***>
rename webapps/nspCode to webapps/nspCode1
copy everything from webapps/examples into webapps/nspCode
copy webapps/nspCode1 over webapps/nspCode
delete webapps/nspCode1
<<Ski Patrol, you should now be able to go to C:\web\tomcat\webapps\nspCode\WEB-INF\classes
and compile all the java classes using "javac *.java">>

--- Eighth (MySQL)---
install MySql
unzip mysql-4.0.15-win.zip into c:\tmp and run setup
install into default (c:\mysql)  ... the directories c:\mysql, c:\ibdata, c:\iblogs do NOT yet exist ...
create new (empty) c:\iblogs
copy old c:\ibdata
copy old c:\mysql\data
cd c:\mysql\bin
(c:\windows\my.ini already coppied)
start for the first time
  mysqld --console
now kill window
mysqld --install
start the service
restart computer, just to be safe

--- Ninth (Jsync)--- (not required, but nice)
Copy the c:\utils folder(without subdirectories is ok),
 and the c:\cw32 folder (with subdirectories) from one of the other computers
Right click My Computer
Advanced tab, Environment Variables
Under system variables, append ;c:\utils;c:\cw32;
Click OK to close all the boxes
All the dos prompts opened from now on should work

===========================
now Everything should Work  :-)
===========================
for the ski patrol web site
copy \web\jars\mysql-connector-java-2.0.14-bin.jar to
     \web\tomcat\webapps\nspCode\WEB-INF\lib\mysql-connector-java-2.0.14-bin.jar
copy

