/*
 * AMC.java
 *
 * Created on December 12, 2002, 1:43 PM
 */

import java.sql.*;
import java.net.*;

/**
 *
 * @author  Gledhill
 */
public class AMC {
    static boolean DEBUG = false;
    String BTN;
    String Service;    
    String RequestedBy;
    String RequestDate;
    String Instructions;
    String Response;
    String OrderNum;
    String RemindDate;
    String Status;
    String LastModified;
    String CustomerID;
    String UsersNotified;

    private boolean exceptionError;
/**** database fields *********************************/    
/*************************************/    
    
    /** Creates a new instance of AMC */
    public AMC() {
        BTN = "";
        Service = "";    
        RequestedBy = "";
        RequestDate = "";
        Instructions = "";
        Response = "";
        OrderNum = "";
        RemindDate = "";
        Status = "";
        LastModified = "";
        CustomerID = "";
        UsersNotified = "";
        exceptionError = false;
    }
    public boolean read(ResultSet results) {
	exceptionError = false;
        BTN = DBAccess.readString(results,"BTN",exceptionError);
        Service = DBAccess.readString(results,"Service",exceptionError);
        RequestedBy = DBAccess.readString(results,"RequestedBy",exceptionError);
        RequestDate = DBAccess.readString(results,"RequestDate",exceptionError);
        Instructions = DBAccess.readString(results,"Instructions",exceptionError);
        Response = DBAccess.readString(results,"Response",exceptionError);
        OrderNum = DBAccess.readString(results,"OrderNum",exceptionError);
        RemindDate = DBAccess.readString(results,"RemindDate",exceptionError);
        Status = DBAccess.readString(results,"Status",exceptionError);
        LastModified = DBAccess.readString(results,"LastModified",exceptionError);
        CustomerID = DBAccess.readString(results,"CustomerID",exceptionError);
        UsersNotified = DBAccess.readString(results,"UsersNotified",exceptionError);
        return exceptionError;
    }
    public String getBTN()          {      return BTN;    }
    public String getService()      {      return Service;    }
    public String getRequestedBy()  {      return RequestedBy;    }
    public String getRequestDate()  {      return RequestDate;    }
    public String getInstructions() {      return Instructions;    }
    public String getResponse()     {      return Response;    }
    public String getOrderNum()     {      return OrderNum;    }
    public String getRemindDate()      {      return RemindDate;    }
    public String getStatus()       {      return Status;    }
    public String getLastModified() {      return LastModified;    }
    public String getCustomerID()   {      return CustomerID;    }
    public String getUsersNotified(){      return UsersNotified;  }
    
    public void setRemindDate(String str)   {        RemindDate = str;    }

//-----------------------------------------------------
// getSQLUpdateString
//-----------------------------------------------------
    public String getSQLUpdateString() {
    String qryString = "UPDATE AMC SET " +
        " Service=\"" + Service +
        "\", RequestedBy=\"" + RequestedBy +
        "\", Instructions=\"" + Instructions +
        "\", Response=\"" + Response +
        "\", OrderNum=\"" + OrderNum +
        "\", RemindDate=\"" + RemindDate +
        "\", Status=\"" + Status +
        "\", LastModified=\"" + LastModified +
        "\", CustomerID=\"" + CustomerID +
        "\", UsersNotified=\"" + UsersNotified +
        "\" WHERE BTN=\"" + BTN + "\" AND RequestDate=\"" + RequestDate + "\"";
    if(DEBUG)
        System.out.println(qryString);
    return qryString;
    }

//-----------------------------------------------------
// getSQLResetString
//-----------------------------------------------------
    public static String getSQLResetString(String filterBTN) {
        String qryString = "SELECT * FROM AMC";
        if(filterBTN != "")
            qryString += " WHERE BTN=\"" + filterBTN + "\""; //limit to btn
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }

//-----------------------------------------------------
// getSQLPastDueString
//-----------------------------------------------------
    public static String getSQLPastDueString() {
        String qryString = "SELECT * FROM AMC WHERE RemindDate != 0 AND RemindDate <= UNIX_TIMESTAMP( CURDATE( ) ) ORDER BY CustomerID ASC";
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }
//-----------------------------------------------------
// getSQLResetString
//-----------------------------------------------------
    public static String buildStatusString(int hi, int med, int lo) {
        String str;
        if(hi >= 0 && hi <= 9)          str = "00"+hi;
        else if(hi >= 10 && hi <= 99)   str = "0"+hi;
        else if(hi >= 100 && hi <= 999) str = ""+hi;
        else                            str = "000";
        
        if(med >= 0 && med <= 9)          str += "00"+med;
        else if(med >= 10 && med <= 99)   str += "0"+med;
        else if(med >= 100 && med <= 999) str += ""+med;
        else                            str += "000";
        
        if(lo >= 0 && lo <= 9)          str += "00"+lo;
        else if(lo >= 10 && lo <= 99)   str += "0"+lo;
        else if(lo >= 100 && lo <= 999) str += ""+lo;
        else                            str += "000";
        
        str += "000";   //for archive (if ever used)
        
        return str;
    }
//-----------------------------------------------------
// getSQLInsertString
//-----------------------------------------------------
/**** not ported yet    
    public String getSQLInsertString() {
        String qryString = "INSERT INTO AMC "+
        " Values(\""+ AMC + 
        "\",\"" + LastUpdated +
        "\",\"" + Location + 
        "\",\"" + Division +
        "\",\"" + Department +
        "\",\"" + AMC +
        "\",\"" + Description +
        "\",\"" + ContractExpDate +
        "\",\"" + Cost +
        "\",\"" + Review +
        "\",\"" + CustomerID + 
        "\",\"" + UsersNotified + 
 "\")" ;
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }
****/
//-----------------------------------------------------
// getSQLDeleteString
//-----------------------------------------------------
/*** not ported yet    
    public String getSQLDeleteString() {
        int i;
        String qryString = "DELETE FROM AMC WHERE AMC=\""+AMC+"\"";
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }
***/
    public String toString() {
        return BTN;
    }
}
