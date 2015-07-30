/*
 * User.java
 *
 * Created on December 12, 2002, 1:43 PM
 */

import java.sql.*;
import java.net.*;

/**
 *
 * @author  Gledhill
 */
public class User {
    static boolean DEBUG = false;
    static int NO = 0;      //index positions for Allow Archive Access combo box
    static int YES = 1;

/**** database fields *********************************/    
// LoginName, Password, CustomerID, UserName, Administrator, NotifyAboutLogins, 
//    NotifyAboutChanges, Department, Email, Phone, Mobil,AccessCount    
/*************************************/    
    String LoginName;
    String Password;
    String CustomerID;
    String UserName;
    int Administrator;
    int NotifyAboutLogins;
    int NotifyAboutChanges;
    String Department;
    String Email;
    String Phone; 
    String Mobil;
    int AccessCount;    

    int CurrentBtnCount;    //not an item in the database
    private boolean exceptionError;
    
    /** Creates a new instance of User */
    public User() {
        LoginName  = "";
        Password  = "";
        CustomerID  = "";
        UserName  = "";
        Administrator  = NO;
        NotifyAboutLogins  = NO;
        NotifyAboutChanges  = NO;
        Department  = "";
        Email  = "";
        Phone  = "";
        Mobil  = "";
        AccessCount = 0;
        exceptionError = false;
    }
    public User(String loginName, String password, String customerID, String userName, int admin, int notifyLogin,
                int notifyChange, String dept, String email, String phone, String mobile, int count) {
        LoginName  = loginName;
        Password  = password;
        CustomerID  = customerID;
        UserName  = userName;
        Administrator  = admin;
        NotifyAboutLogins  = notifyLogin;
        NotifyAboutChanges  = notifyChange;
        Department  = dept;
        Email  = email;
        Phone  = phone;
        Mobil  = mobile;
        AccessCount = count;
        exceptionError = false;
    }

    public boolean read(ResultSet results) {
/**** database fields *********************************/    
// LoginName, Password, CustomerID, UserName, Administrator, NotifyAboutLogins, 
//    NotifyAboutChanges, Department, Email, Phone, Mobil,AccessCount    
/*************************************/    
	exceptionError = false;
        LoginName  = DBAccess.readString(results,"LoginName",exceptionError);
        Password  = DBAccess.readString(results,"Password",exceptionError);
        CustomerID  = DBAccess.readString(results,"CustomerID",exceptionError);
        UserName  = DBAccess.readString(results,"UserName",exceptionError);
        Administrator  = DBAccess.readInt(results,"Administrator",exceptionError);
        NotifyAboutLogins  = DBAccess.readInt(results,"NotifyAboutLogins",exceptionError);
        NotifyAboutChanges  = DBAccess.readInt(results,"NotifyAboutChanges",exceptionError);
        Department  = DBAccess.readString(results,"Department",exceptionError);
        Email  = DBAccess.readString(results,"Email",exceptionError);
        Phone  = DBAccess.readString(results,"Phone",exceptionError);
        Mobil  = DBAccess.readString(results,"Mobil",exceptionError);
        AccessCount = DBAccess.readInt(results,"AccessCount",exceptionError);
        return exceptionError;
    }
    public String getLoginName()        {   return LoginName;   }
    public String getPassword()         {   return Password;   }
    public String getCustomerID()       {   return CustomerID;   }
    public String getUserName()         {   return UserName;   }
    public int getAdministrator()       {   return Administrator;   }
    public int getNotifyAboutLogins()   {   return NotifyAboutLogins;   }
    public int getNotifyAboutChanges()  {   return NotifyAboutChanges;   }
    public String getDepartment()       {   return Department;   }
    public String getEmail()            {   return Email;   }
    public String getPhone()            {   return Phone;   }
    public String getMobil()            {   return Mobil;   }
    public int getAccessCount()         {   return AccessCount;   }

//-----------------------------------------------------
// getSQLUpdateString
//-----------------------------------------------------
    public String getSQLUpdateString() {
// LoginName, Password, CustomerID, UserName, Administrator, NotifyAboutLogins, 
//    NotifyAboutChanges, Department, Email, Phone, Mobil,AccessCount    
    String qryString = "UPDATE user SET " +
        " Password=\"" + Password + 
        "\",  CustomerID=\"" + CustomerID + 
        "\",  UserName=\"" + UserName + 
        "\",  Administrator=\"" + Administrator + 
        "\",  NotifyAboutLogins=\"" + NotifyAboutLogins + 
        "\",  NotifyAboutChanges=\"" + NotifyAboutChanges + 
        "\",  Department=\"" + Department + 
        "\",  Email=\"" + Email + 
        "\",  Phone=\"" + Phone + 
        "\",  Mobil=\"" + Mobil + 
        "\",  AccessCount=\"" + AccessCount + 
        "\" WHERE LoginName=\"" + LoginName + "\"";
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }

//-----------------------------------------------------
// getSQLResetString
//-----------------------------------------------------
    public static String getSQLResetString() {
        String qryString= "SELECT * FROM user ORDER BY LoginName"; //sort by default key
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }
    
//-----------------------------------------------------
// getSQLFindString
//-----------------------------------------------------
    public static String getSQLFindString(String id) {
        String qryString= "SELECT * FROM user Where LoginName='" + id + "'"; //sort by default key
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }
    
//-----------------------------------------------------
// getSQLInsertString
//-----------------------------------------------------
    public String getSQLInsertString() {
// LoginName, Password, CustomerID, UserName, Administrator, NotifyAboutLogins, 
//    NotifyAboutChanges, Department, Email, Phone, Mobil,AccessCount    
        String qryString = "INSERT INTO user "+
        " Values(\""+ LoginName + 
        "\",\"" + Password + 
        "\",\"" + CustomerID + 
        "\",\"" + UserName + 
        "\",\"" + Administrator + 
        "\",\"" + NotifyAboutLogins + 
        "\",\"" + NotifyAboutChanges + 
        "\",\"" + Department + 
        "\",\"" + Email + 
        "\",\"" + Phone + 
        "\",\"" + Mobil + 
        "\",\"" + AccessCount + 
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
        String qryString = "DELETE FROM user WHERE LoginName=\""+LoginName+"\"";
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }

    public String toString() {
        return LoginName;
    }
}
