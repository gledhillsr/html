/*
 * Agency.java
 *
 * Created on December 12, 2002, 1:43 PM
 */

import java.sql.*;
import java.net.*;

/**
 *
 * @author  Gledhill
 */
public class Customer {
    static boolean DEBUG = false;
    static int NO = 0;      //index positions for Allow Archive Access combo box
    static int YES = 1;
                                // *** DATABASE FIELDS*****
    String CustomerID;          // CustomerID 
    String Name;                // Name
    String Address;             // Address
    String AgencyID;            // AgencyID
    int AllowArchiveAccess;     // AllowArchiveAccess
    int BTNCount;
    int BTNStorage;
    int ArchiveBtnCount;
    int ArchiveBtnStorage;

    int CurrentBtnCount;    //not an item in the database
    private boolean exceptionError;
/**** database fields *********************************/    
// agencyID  -  text 50
// agencyName  - text 50
// Address - text 255
/*************************************/    
    
    /** Creates a new instance of Agency */
    public Customer() {
        CustomerID      = "";
        Name    = "";
        Address = "";
        AgencyID= "";
        AllowArchiveAccess = NO;
        CurrentBtnCount = 0;
        BTNCount = 0;
        BTNStorage = 0;
        ArchiveBtnCount = 0;
        ArchiveBtnStorage = 0;

        exceptionError = false;
    }
    public Customer(String customerid, String name, String address, String agentID, int archiveAccess,
        int btnCount, int btnStorage, int archiveCount, int archiveStorage) {
        CustomerID = customerid;
        Name = name;
        Address = address;
        AgencyID= agentID;
        AllowArchiveAccess = archiveAccess;
        BTNCount = btnCount;
        BTNStorage = btnStorage;
        ArchiveBtnCount = archiveCount;
        ArchiveBtnStorage = archiveStorage;
        
        exceptionError = false;
    }

    public boolean read(ResultSet results) {
// databese record is CustomerID, Name, Address, AgencyID, AllowArchiveAccess
	exceptionError = false;
        CustomerID = DBAccess.readString(results,"CustomerID",exceptionError);
        Name = DBAccess.readString(results,"Name",exceptionError);
        Address = DBAccess.readString(results,"Address",exceptionError);
        AgencyID = DBAccess.readString(results,"AgencyID",exceptionError);
        AllowArchiveAccess = DBAccess.readInt(results,"AllowArchiveAccess",exceptionError);
        BTNCount = DBAccess.readInt(results,"BTNCount",exceptionError);
        BTNStorage = DBAccess.readInt(results,"BTNStorage",exceptionError);
        ArchiveBtnCount = DBAccess.readInt(results,"ArchiveBtnCount",exceptionError);
        ArchiveBtnStorage = DBAccess.readInt(results,"ArchiveBtnStorage",exceptionError);
        return exceptionError;
    }
    
    public String getID()               {   return CustomerID;    }
    public String getName()             {   return Name;    }
    public String getAddress()          {   return Address;  }
    public String getAgencyID()         {   return AgencyID;    }
    public int getArchiveAccess()       {   return AllowArchiveAccess;    }
    public int getBTNCount()            {   return BTNCount;    }
    public int getBTNStorage()          {   return BTNStorage;    }
    public int getArchiveBtnCount()     {   return ArchiveBtnCount;    }
    public int getArchiveBtnStorage()   {   return ArchiveBtnStorage;    }

//-----------------------------------------------------
// getSQLUpdateString
//-----------------------------------------------------
    public String getSQLUpdateString() {
// databese record is CustomerID, Name, Address, AgencyID, AllowArchiveAccess
    String qryString = "UPDATE customer SET " +
        " Name='" + Name + 
        "', Address='" + Address +
        "', AgencyID='" + AgencyID +
        "', AllowArchiveAccess='" + AllowArchiveAccess +
        "', BTNCount='" + BTNCount +
        "', BTNStorage='" + BTNStorage +
        "', ArchiveBtnCount='" + ArchiveBtnCount +
        "', ArchiveBtnStorage='" + ArchiveBtnStorage +
        "' WHERE CustomerID='" + CustomerID + "'";
    if(DEBUG)
        System.out.println(qryString);
    return qryString;
    }

//-----------------------------------------------------
// getSQLResetString
//-----------------------------------------------------
    public static String getSQLResetString() {
        String qryString= "SELECT * FROM customer ORDER BY CustomerID"; //sort by default key
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }
    
//-----------------------------------------------------
// getSQLFindString
//-----------------------------------------------------
    public static String getSQLFindString(String id) {
        String qryString= "SELECT * FROM customer WHERE CustomerID='" + id + "'"; 
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }
    
//-----------------------------------------------------
// getSQLInsertString
//-----------------------------------------------------
    public String getSQLInsertString() {
        String qryString = "INSERT INTO customer "+
        " Values('"+ CustomerID + 
        "','" + Name + 
        "','" + Address + 
        "','" + AgencyID +
        "','" + AllowArchiveAccess + 
        "','" + BTNCount + 
        "','" + BTNStorage + 
        "','" + ArchiveBtnCount + 
        "','" + ArchiveBtnStorage + 
        "')" ;
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }

//-----------------------------------------------------
// getSQLDeleteString
//-----------------------------------------------------
    public String getSQLDeleteString() {
        int i;
        String qryString = "DELETE FROM customer WHERE CustomerID='"+CustomerID+"'";
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }

    public String toString() {
        return Name;
    }
}
