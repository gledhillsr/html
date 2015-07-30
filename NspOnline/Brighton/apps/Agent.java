/*
 * Agent.java
 *
 * Created on December 12, 2002, 1:43 PM
 */

import java.sql.*;
import java.net.*;

/**
 *
 * @author  Gledhill
 */
public class Agent {
    static boolean DEBUG = false;
    private boolean exceptionError;

/**** database fields (in order) ******/
	String firstName;
	String lastName;
	String type;
	String email;
	String commission;
	String loginID;
	String password;
	String isAdmin;
	String isSuperUser;
/*************************************/

//-----------------------------------------------------
// Agent constructor
//-----------------------------------------------------
    public Agent() {
		firstName = "";
		lastName = "";
		type = "";
		email = "";
		commission = "";
		loginID = "";
		password = "";
		isAdmin = "";
		isSuperUser = "";
        exceptionError = false;
    }

//-----------------------------------------------------
// read method
//-----------------------------------------------------
    public boolean read(ResultSet results) {
	exceptionError = false;
		firstName = DBAccess.readString(results,"firstName",exceptionError);
		lastName = DBAccess.readString(results,"lastName",exceptionError);
		type = DBAccess.readString(results,"type",exceptionError);
		email = DBAccess.readString(results,"email",exceptionError);
		commission = DBAccess.readString(results,"commission",exceptionError);
		loginID = DBAccess.readString(results,"loginID",exceptionError);
		password = DBAccess.readString(results,"password",exceptionError);
		isAdmin = DBAccess.readString(results,"isAdmin",exceptionError);
		isSuperUser = DBAccess.readString(results,"isSuperUser",exceptionError);
        return exceptionError;
    }
//-----------------------------------------------------
// getter methods
//-----------------------------------------------------
    public String getFirstName()  		{ return firstName;    }
    public String getLastName()			{ return lastName;    }
    public String getType()				{ return type;    }
    public String getEmail()			{ return email;    }
    public String getCommission() 		{ return commission;    }
    public String getLoginID()			{ return loginID;    }
    public String getPassword()			{ return password;    }
    public String getIsAdmin()			{ return isAdmin;    }
    public String getIsSuperUser()		{ return isSuperUser;    }

//-----------------------------------------------------
// setter methods
//-----------------------------------------------------
    public void setFirstName(String str)  		{ firstName = str; }
    public void setLastName(String str)  		 	{ lastName  = str; }
    public void setType(String str)				{ type = str; }
    public void setEmail(String str)				{ email = str; }
    public void setCommission(String str) 		{ commission = str; }
    public void setLoginID(String str)			{ loginID = str; }
    public void setPassword(String str)			{ password = str; }
    public void setIsAdmin(String str)		 	{ isAdmin = str; }
    public void setIsSuperUser(String str)	 	{ isSuperUser = str; }

//-----------------------------------------------------
// getSQLResetString
//-----------------------------------------------------
    public static String getSQLResetString() {
        String qryString = "SELECT * FROM `agent`";
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }

//-----------------------------------------------------
// getSQLSelectString
//-----------------------------------------------------
    public static String getSQLSelectString(String first, String last) {
        String qryString = "SELECT * FROM `agent` where firstName=\""+first+"\" AND lastName=\""+last+"\"";
		
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }



    public String toString() {
        return "firstName=" + firstName + ", lastName=" + lastName + ", type=" + type +
		", email=" + email + ", loginID=" + loginID +	", password=" + password;
    }
}
