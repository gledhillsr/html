/*
 * Agency.java
 *
 * Created on December 12, 2002, 1:43 PM
 */

import java.sql.*;
import java.net.*;
import java.util.*;
import java.io.*;
import java.text.*;

/**
 *
 * @author  Gledhill
 */
public class Agency {
    static String homeDir = "c:\\web\\CSR_home\\";
    static boolean DEBUG = false;
    String ID;
    String Name;
    String Address;
    String Contact;
    String Phone;
    String Fax;
    String SecondContact;
    String agencyPluralName;
    private javax.swing.JFrame frame;
    private boolean exceptionError;
/**** database fields *********************************/    
// agencyID  -  text 50
// agencyName  - text 50
// Address - text 255
/*************************************/    
    
    /** Creates a new instance of Agency */
    public Agency() {
        ID      = "";
        Name    = "";
        Address = "";
        Contact = "";
        Phone = "";
        Fax = "";
        SecondContact = "";
        agencyPluralName = "";
        exceptionError = false;
    }
    public Agency(String id, String name, String address,String contact, String phone, String fax, String secondContact, String pName) {
        ID = id;
        Name = name;
        agencyPluralName = pName;
        Address = address;
        Contact = contact;
        Phone = phone;
        Fax = fax;
        SecondContact = secondContact;
        exceptionError = false;
    }
        
    public boolean read(ResultSet results) {
	exceptionError = false;
        ID = DBAccess.readString(results,"agencyID",exceptionError);
        Name = DBAccess.readString(results,"agencyName",exceptionError);
        Address = DBAccess.readString(results,"Address",exceptionError);
        Contact = DBAccess.readString(results,"Contact",exceptionError);
        Phone = DBAccess.readString(results,"Phone",exceptionError);
        Fax = DBAccess.readString(results,"Fax",exceptionError);
        SecondContact = DBAccess.readString(results,"SecondContact",exceptionError);
        agencyPluralName = DBAccess.readString(results,"agencyPluralName",exceptionError);
        return exceptionError;
    }
    public String getID() {        return ID;    }
    public String getName() {      return Name;    }
    public String getPluralName() {  return agencyPluralName;    }
    public String getAddress() {   return Address;  }
    public String getContact() {   return Contact;  }
    public String getPhone() {   return Phone;  }
    public String getFax() {   return Fax;  }
    public String get2ndContact() {   return SecondContact;  }

   public boolean writeIndexHTML(javax.swing.JFrame frm) {
       frame = frm;
       if(makeSureOutputDirExists()) {
           String szIndexFile = homeDir+ID+"\\index.php";
           File newOutFile = new File(szIndexFile);
           RandomAccessFile sumOutFile;
           String str = "<?\n" +
                "header(\"Location: ../index.php?agent="+ID+"\");	/* Redirect browser */\n" +
                "exit;\n" +
                "?>";
            try {
                sumOutFile = new RandomAccessFile(newOutFile, "rw");
                sumOutFile.setLength(0);
                sumOutFile.writeBytes(str);
                sumOutFile.close();
            } catch (Exception e) {
                new MessageDialog(frame,"Error creating file "+szIndexFile+".","OK");            
                return false;
            }
        }
       return true;
   }
    private boolean makeSureOutputDirExists() {
        String szHtmlDir = homeDir+ID;
        File dir = new File(szHtmlDir);
        if(dir.exists())
            return dir.isDirectory();
        //does not exist
        try {
            if(dir.mkdir())
                return true;
        } catch (Exception e) { /* fall thru on error */  }
        new MessageDialog(frame,"Error creating directory "+szHtmlDir+".","OK");            
        return false;
    }
//-----------------------------------------------------
// getSQLUpdateString
//-----------------------------------------------------
    public String getSQLUpdateString() {
    String qryString = "UPDATE agency SET " +
        " agencyName=\"" + Name + 
        "\", Address=\"" + Address +
        "\", Contact=\"" + Contact +
        "\", Phone=\"" + Phone +
        "\", Fax=\"" + Fax +
        "\", SecondContact=\"" + SecondContact +
        "\", agencyPluralName=\"" + agencyPluralName +
        "\" WHERE agencyID=\"" + ID + "\"";
    if(DEBUG)
        System.out.println(qryString);
    return qryString;
    }

//-----------------------------------------------------
// getSQLResetString
//-----------------------------------------------------
    public static String getSQLResetString() {
        String qryString= "SELECT * FROM agency ORDER BY agencyID"; //sort by default key
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }
    
//-----------------------------------------------------
// getSQLInsertString
//-----------------------------------------------------
    public String getSQLInsertString() {
        String qryString = "INSERT INTO agency "+
        " Values(\""+ ID + 
        "\",\"" + Name + 
        "\",\"" + Address + 
        "\",\"" + Contact +
        "\",\"" + Phone +
        "\",\"" + Fax +
        "\",\"" + SecondContact +
        "\",\"" + agencyPluralName +
        "\")" ;
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }

//-----------------------------------------------------
// getSQLDeleteString
//-----------------------------------------------------
    public String getSQLDeleteString() {
        int i;
        String qryString = "DELETE FROM agency WHERE agencyID=\""+ID+"\"";
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }

    public String toString() {
        return Name;
    }
}
