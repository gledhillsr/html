/*
 * Npanxx.java
 *
 * Created on December 12, 2002, 1:43 PM
 */

import java.sql.*;
import java.net.*;

/**
 *
 * @author  Gledhill
 */
public class Order {
    static boolean DEBUG = false;
    private boolean exceptionError;
	
/**** database fields (in order) ******/    
	String name;
	String address;
	String city;
	String state;  
	String zipCode;
	String phone;  
	String contact;
	String contactPhone;
	String primeAgent;
	String vendor; 
	String service;  
	String option1;  
	String fullDescription;
	String status;
	String totalRevenue;
	String notes; 
	String projManager; 
	String date;
	String dateEntered; 
	String dueDate;
	String orderNumber; 
	String notifyAgent; 
	String fileName; 
	String expiration;  
	String alsoEmail;  
	String onHoldFreq;   
	String criticalDate;
	String critStart; 
	String critFreq; 
	String secondManager;
/*************************************/    
    
    /** Creates a new instance of Npanxx */
    public Npanxx() {
		name = "";
		address = "";
		city = "";
		state = "";  
		zipCode = "";
		phone = "";  
		contact = "";
		contactPhone = "";
		primeAgent = "";
		vendor = ""; 
		service = "";  
		option1 = "";  
		fullDescription = "";
		status = "";
		totalRevenue = "";
		notes = ""; 
		projManager = ""; 
		date = "";
		dateEntered = ""; 
		dueDate = "";
		orderNumber = ""; 
		notifyAgent = ""; 
		fileName = ""; 
		expiration = "";  
		alsoEmail = "";  
		onHoldFreq = "";   
		criticalDate = "";
		critStart = ""; 
		critFreq = ""; 
		secondManager = "";		
        exceptionError = false;
    }
    public boolean read(ResultSet results) {
	exceptionError = false;
        Place = DBAccess.readString(results,"Place",exceptionError);
        State = DBAccess.readString(results,"State",exceptionError);
        NPANXX = DBAccess.readString(results,"NPANXX",exceptionError);
        Tier = DBAccess.readString(results,"Tier",exceptionError);
        return exceptionError;
    }
//-----------------------------------------------------
// getter methods
//-----------------------------------------------------
    public String getNPANXX()	{ return NPANXX;    }
    public String getPlace() 	{ return Place;    }
    public String getState() 	{ return State;    }
    public String getTier()  	{ return Tier;    }

//-----------------------------------------------------
// setter methods
//-----------------------------------------------------
    public void setNPANXX(String str)   { NPANXX = str; }
    public void setPlace(String str) 	{ Place	 = str; }
    public void setState(String str) 	{ State	 = str; }
    public void setTier(String str)  	{ Tier 	 = str; }

//-----------------------------------------------------
// getSQLUpdateString
//-----------------------------------------------------
    public String getSQLUpdateString() {
    String qryString = "UPDATE npanxx SET " +
        " Place=\"" + Place +
        "\", State=\"" + State +
        "\", Tier=\"" + Tier +
        "\" WHERE NPANXX=\"" + NPANXX + "\"";
    if(DEBUG)
        System.out.println(qryString);
    return qryString;
    }

//-----------------------------------------------------
// getSQLResetString
//-----------------------------------------------------
    public static String getSQLResetString() {
        String qryString = "SELECT * FROM npanxx";
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }

//-----------------------------------------------------
// getSQLInsertString
//-----------------------------------------------------
    public String getSQLInsertString() {
        String qryString = "INSERT INTO npanxx "+
        " Values(\""+ Place + 
        "\",\"" + State +
        "\",\"" + NPANXX + 
        "\",\"" + Tier + 
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
        String qryString = "DELETE FROM npanxx WHERE NPANXX=\""+NPANXX+"\"";
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }

    public String toString() {
        return NPANXX + ", state=" + State + ", Place=" + Place + ", Tier="+Tier;
    }
}
