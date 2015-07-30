/*
 * Order.java
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
	String option2;
	String option3;
	String option4;
	String option5;
	String option6;
	String option7;
	String option8;
	String fullDescription;
	String status;
	int statusChange;
	String totalRevenue;
	String notes;
	String projManager;
	int dateModified;
	String dateEntered;
	String dueDate;
	String orderNumber;
	String notifyAgent;
	String fileName;
	String expiration;
	String alsoEmail;
	int onHoldFreq;
	int criticalDate;
	int critStart;
	String critFreq;
	String secondManager;
    int index;
/*************************************/

    /** Creates a new instance of Order */
    public Order() {
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
		option2 = "";
		option3 = "";
		option4 = "";
		option5 = "";
		option6 = "";
		option7 = "";
		option8 = "";
		fullDescription = "";
		status = "";
		statusChange = 0;
		totalRevenue = "";
		notes = "";
		projManager = "";
		dateModified = 0;
		dateEntered = "";
		dueDate = "";
		orderNumber = "";
		notifyAgent = "";
		fileName = "";
		expiration = "";
		alsoEmail = "";
		onHoldFreq = 0;
		criticalDate = 0;
		critStart = 0;
		critFreq = "";
		secondManager = "";
        index = 0;
        exceptionError = false;
    }
    public boolean read(ResultSet results) {
	exceptionError = false;
		name = DBAccess.readString(results,"name",exceptionError);
		address = DBAccess.readString(results,"address",exceptionError);
		city = DBAccess.readString(results,"city",exceptionError);
		state = DBAccess.readString(results,"state",exceptionError);
		zipCode = DBAccess.readString(results,"zipCode",exceptionError);
		phone = DBAccess.readString(results,"phone",exceptionError);
		contact = DBAccess.readString(results,"contact",exceptionError);
		contactPhone = DBAccess.readString(results,"contactPhone",exceptionError);
		primeAgent = DBAccess.readString(results,"primeAgent",exceptionError);
		vendor = DBAccess.readString(results,"vendor",exceptionError);
		service = DBAccess.readString(results,"service",exceptionError);
		option1 = DBAccess.readString(results,"option1",exceptionError);
		option2 = DBAccess.readString(results,"option2",exceptionError);
		option3 = DBAccess.readString(results,"option3",exceptionError);
		option4 = DBAccess.readString(results,"option4",exceptionError);
		option5 = DBAccess.readString(results,"option5",exceptionError);
		option6 = DBAccess.readString(results,"option6",exceptionError);
		option7 = DBAccess.readString(results,"option7",exceptionError);
		option8 = DBAccess.readString(results,"option8",exceptionError);
		fullDescription = DBAccess.readString(results,"fullDescription",exceptionError);
		status = DBAccess.readString(results,"status",exceptionError);
		statusChange = DBAccess.readInt(results,"statusChange",exceptionError);
		totalRevenue = DBAccess.readString(results,"totalRevenue",exceptionError);
		notes = DBAccess.readString(results,"notes",exceptionError);
		projManager = DBAccess.readString(results,"projManager",exceptionError);
		dateModified = DBAccess.readInt(results,"dateModified",exceptionError);
		dateEntered = DBAccess.readString(results,"dateEntered",exceptionError);
		dueDate = DBAccess.readString(results,"dueDate",exceptionError);
		orderNumber = DBAccess.readString(results,"orderNumber",exceptionError);
		notifyAgent = DBAccess.readString(results,"notifyAgent",exceptionError);
		fileName = DBAccess.readString(results,"fileName",exceptionError);
		expiration = DBAccess.readString(results,"expiration",exceptionError);
		alsoEmail = DBAccess.readString(results,"alsoEmail",exceptionError);
		onHoldFreq = DBAccess.readInt(results,"onHoldFreq",exceptionError);
		criticalDate = DBAccess.readInt(results,"criticalDate",exceptionError);
		critStart = DBAccess.readInt(results,"critStart",exceptionError);
		critFreq = DBAccess.readString(results,"critFreq",exceptionError);
		secondManager = DBAccess.readString(results,"secondManager",exceptionError);
		index = DBAccess.readInt(results,"index",exceptionError);
        return exceptionError;
    }
//-----------------------------------------------------
// getter methods
//-----------------------------------------------------
    public String getName()				{ return name;    }
    public String getAddress()			{ return address;    }
    public String getCity()				{ return city;    }
    public String getState()			{ return state;    }
    public String getZipCode()			{ return zipCode;    }
    public String getPhone()			{ return phone;    }
    public String getContact()			{ return contact;    }
    public String getContactPhone()		{ return contactPhone;    }
    public String getPrimeAgent()		{ return primeAgent;    }
    public String getVendor()			{ return vendor;    }
    public String getService()			{ return service;    }
    public String getOption1()			{ return option1;    }
    public String getFullDescription()	{ return fullDescription;    }
    public String getStatus()			{ return status;    }
    public int getStatusChange()		{ return statusChange;    }
    public String getTotalRevenue()		{ return totalRevenue;    }
    public String getNotes()			{ return notes;    }
    public String getProjManager()		{ return projManager;    }
    public int getDateModified()		{ return dateModified;    }
    public String getDateEntered()		{ return dateEntered;    }
    public String getDueDate()			{ return dueDate;    }
    public String getOrderNumber()		{ return orderNumber;    }
    public String getNotifyAgent()		{ return notifyAgent;    }
    public String getFileName()			{ return fileName;    }
    public String getExpiration()		{ return expiration;    }
    public String getAlsoEmail()		{ return alsoEmail;    }
    public int getOnHoldFreq()			{ return onHoldFreq;    }
    public int getCriticalDate()		{ return criticalDate;    }
    public int getCritStart()			{ return critStart;    }
    public String getCritFreq()			{ return critFreq;    }
    public String getSecondManager()	{ return secondManager;    }
    public int getIndex()               { return index;    }

//-----------------------------------------------------
// setter methods
//-----------------------------------------------------
    public void setName(String str)				{ name = str; }
    public void setAddress(String str)	   	 	{ address  = str; }
    public void setCity(String str)  			{ city = str; }
    public void setState(String str) 	  		{ state = str; }
    public void setZipCode(String str)   		{ zipCode = str; }
    public void setPhone(String str)   			{ phone = str; }
    public void setContact(String str)   		{ contact = str; }
    public void setContactPhone(String str)	 	{ contactPhone = str; }
    public void setPrimeAgent(String str)  	 	{ primeAgent = str; }
    public void setVendor(String str)   		{ vendor = str; }
    public void setService(String str)  		{ service = str; }
    public void setOption1(String str)  		{ option1 = str; }
    public void setFullDescription(String str)  { fullDescription = str; }
    public void setStatus(String str)   		{ status = str; }
    public void setStatusChange(int i)   		{ statusChange = i; }
    public void setTotalRevenue(String str)   	{ totalRevenue = str; }
    public void setNote(String str)   			{ notes = str; }
    public void setProjManager(String str)   	{ projManager = str; }
    public void setDateModified(int dt)   			{ dateModified = dt; }
    public void setDateEntered(String str)   	{ dateEntered = str; }
    public void setDueDate(String str)   		{ dueDate = str; }
    public void setOrderNumber(String str)   	{ orderNumber = str; }
    public void setNotifyAgent(String str)   	{ notifyAgent = str; }
    public void setFileName(String str)   		{ fileName = str; }
    public void setExpiration(String str)   	{ expiration = str; }
    public void setAlsoEmail(String str)   		{ alsoEmail = str; }
    public void setOnHoldFreq(int days)  		{ onHoldFreq = days; }
    public void setCriticalDate(int str)	   	{ criticalDate = str; }
    public void setCritStart(int str)   		{ critStart = str; }
    public void setCritFreq(String str)   		{ critFreq = str; }
    public void setSecondManager(String str)   	{ secondManager = str; }

//-----------------------------------------------------
// getSQLUpdateString
//-----------------------------------------------------
    public String getSQLUpdateString() {
    String qryString = "UPDATE `order` SET " +
        " critStart=\"" + critStart +
        "\" statusChange=\"" + statusChange +
        "\", dateModified=\"" + dateModified +
        "\" WHERE dateEntered=\"" + dateEntered + "\"";
//    if(DEBUG)
        System.out.println(qryString);
    return qryString;
    }

//-----------------------------------------------------
// getSQLResetString
//-----------------------------------------------------
    public static String getSQLResetString() {
        String qryString = "SELECT * FROM `order`";
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }

//-----------------------------------------------------
// getSQLInsertString
//-----------------------------------------------------

				//wont use yet, so not modifying
/*    public String getSQLInsertString() {
        String qryString = "INSERT INTO `order` "+
        " Values(\""+ Place +
        "\",\"" + State +
        "\",\"" + NPANXX +
        "\",\"" + Tier +
 "\")" ;
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }
*/

//-----------------------------------------------------
// getSQLDeleteString
//-----------------------------------------------------

				//wont use yet, so not modifying
/*    public String getSQLDeleteString() {
        int i;
        String qryString = "DELETE FROM npanxx WHERE NPANXX=\""+NPANXX+"\"";
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }
*/

    public String toString() {
        return "name=" + name + ", criticalDate=" + criticalDate + ", critStart=" + critStart +
		", criticalDate=" + criticalDate + ", critFreq=" + critFreq +	", dateModified="+dateModified+", dateEntered=" +
		dateEntered + ", status=" + status + ", statusChange=" + statusChange + ", onHoldFreq=" + onHoldFreq;
    }
}
