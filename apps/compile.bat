rem javac -g -classpath c:\web\jars\mysql-connector-java-2.0.14-bin.jar;AbsoluteLayout.jar;c:\web\jars\mail\mail.jar;c:\web\jars\mail\activation.jar *.java
rem jar cfm CS_admin.jar Manifest.txt *.class	AbsoluteLayout.jar mysql-connector-java-2.0.14-bin.jar
javac -g -classpath c:\web\jars\mysql-connector-java-5.0.3-bin.jar;AbsoluteLayout.jar;c:\web\jars\mail\mail.jar;c:\web\jars\mail\activation.jar *.java
jar cfm CS_admin.jar Manifest.txt *.class	AbsoluteLayout.jar mysql-connector-java-5.0.3-bin.jar