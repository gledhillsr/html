/*
 * BTN.java
 *
 * Created on December 12, 2002, 1:43 PM
 */

import java.sql.*;
import java.net.*;

/**
 *
 * @author  Gledhill
 */
public class BTN {
    boolean DEBUG = false;
	static boolean DEBUG_ALL = false;
    String btn;
    long LastUpdated;    
    String Location;
    String Division;
    String Department;
    String AMC;
    String Description;
    long ContractExpDate;
    double Cost;
    String Review;
    String CustomerID;
    String ReviewNotes;
	double prevCost;
	long   prevUpdated;
    double MonthlySavings;
    int    Product;
    double MRC;
    double MonthlyService;
    double ServiceOrders;

    private boolean exceptionError;
/**** database fields *********************************/    
/*************************************/    
    
    /** Creates a new instance of BTN */
    public BTN() {
	    DEBUG = false;
        btn = "";
        LastUpdated = 0;    
        Location = "";
        Division = "";
        Department = "";
        AMC = "000000000000";
        Description = "";
        ContractExpDate = 0;
        Cost = 0.0;
        Review = "";
        CustomerID = "";
        ReviewNotes = "";
//		Savings = 0.0;
//		lineType = 0;
//		lineCost = 0.0
        prevCost = 0.0;
        prevUpdated = 0;
        MonthlySavings = 0.0;
        Product = 0;
        MRC = 0.0;
        MonthlyService = 0.0;
        ServiceOrders = 0.0;
        exceptionError = false;
    }
    public BTN(String Btn, String cust, String loc, long lastUpdated,double cost) {
    	DEBUG = false;
        btn = Btn;
        LastUpdated = lastUpdated;    
        Location = loc;
        Division = "";
        Department = "";
        AMC = "000000000000";
        Description = "";
        ContractExpDate = 0;
        Cost = cost;
        Review = "";
        CustomerID = cust;
        prevCost = 0.0;
        prevUpdated = 0;
        MonthlySavings = 0.0;
        Product = 0;
        MRC = 0.0;
        MonthlyService = 0.0;
        ServiceOrders = 0.0;
        exceptionError = false;
    }
    public boolean read(ResultSet results) {
	exceptionError = false;
        btn = DBAccess.readString(results,"BTN",exceptionError);
        String str = DBAccess.readString(results,"LastUpdated",exceptionError);
        LastUpdated = 0;
        try {
            LastUpdated = Long.parseLong(str);
        } catch (Exception e) { }
        Location = DBAccess.readString(results,"Location",exceptionError);
        Division = DBAccess.readString(results,"Division",exceptionError);
        Department = DBAccess.readString(results,"Department",exceptionError);
        AMC = DBAccess.readString(results,"AMC",exceptionError);
        Description = DBAccess.readString(results,"Description",exceptionError);
        ContractExpDate = 0;
        str = DBAccess.readString(results,"ContractExpDate",exceptionError);
        try {
            ContractExpDate = Long.parseLong(str);
        } catch (Exception e) { }
        Cost = DBAccess.readDouble(results,"Cost",exceptionError);
        Review = DBAccess.readString(results,"Review",exceptionError);
        CustomerID = DBAccess.readString(results,"CustomerID",exceptionError);
        ReviewNotes = DBAccess.readString(results,"ReviewNotes",exceptionError);

//new
        prevCost 		= DBAccess.readDouble(results,"prevCost",exceptionError);
        prevUpdated 	= DBAccess.readLong(results,"prevUpdated",exceptionError);

        MonthlySavings 	= DBAccess.readDouble(results,"MonthlySavings",exceptionError);
        Product 		= DBAccess.readInt(results,"Product",exceptionError);
        MRC 			= DBAccess.readDouble(results,"MRC",exceptionError);

        MonthlyService 		= DBAccess.readDouble(results,"MonthlyService",exceptionError);
        ServiceOrders 		= DBAccess.readDouble(results,"ServiceOrders",exceptionError);
        return exceptionError;
    }
    public String getBTN()          {        return btn;    }
    public long getLastUpdated()    {      return LastUpdated;    }
    public String getLocation()     {      return Location;    }
    public String getDivision()     {      return Division;    }
    public String getDepartment()   {      return Department;    }
    public String getAMC()          {      return AMC;    }
    public String getDescription()  {      return Description;    }
    public long getContractExpDate(){   return ContractExpDate;    }
    public double getCost()         {      return Cost;    }
    public String getReview()       {      return Review;    }
    public String getCustomerID()   {      return CustomerID;    }
    public double getPrevCost()     {      return prevCost;    }
    public double getMonthlyService()     {      return MonthlyService;    }
    public double getServiceorders()     {      return ServiceOrders;    }
    public long getPrevUpdated()    {      return prevUpdated;    }

    public void setLastUpdated(long millis) { LastUpdated = millis; }
    public void setCost(double amt)         { Cost = amt;    }
    public void setAMC(String str)          { AMC  = str;    }
    public void setPrevCost(double amt)     { prevCost = amt; }
    public void setMonthlyService(double amt) { MonthlyService = amt; }
    public void setServiceOrders(double amt){ ServiceOrders = amt; }
    public void setPrevUpdated(long millis) { prevUpdated = millis; }
//-----------------------------------------------------
// getSQLUpdateString
//-----------------------------------------------------
    public String getSQLUpdateString() {
    String qryString = "UPDATE btn SET " +
        " LastUpdated=\"" + LastUpdated +
        "\", Location=\"" + Location +
        "\", Division=\"" + Division +
        "\", Department=\"" + Department +
        "\", AMC=\"" + AMC +
        "\", Description=\"" + Description +
        "\", ContractExpDate=\"" + ContractExpDate +
        "\", Cost=\"" + Cost +
        "\", Review=\"" + Review +
        "\", CustomerID=\"" + CustomerID +
        "\", ReviewNotes=\"" + ReviewNotes +        
        "\", prevCost=\"" + prevCost +
        "\", prevUpdated=\"" + prevUpdated +
        "\", MonthlySavings=\"" + MonthlySavings +
        "\", Product=\"" + Product +
        "\", MRC=\"" + MRC +
        "\", MonthlyService=\"" + MonthlyService +
        "\", ServiceOrders=\"" + ServiceOrders +
        "\" WHERE BTN=\"" + btn + "\"";
    if(DEBUG)
        System.out.println(qryString);
    return qryString;
    }


//-----------------------------------------------------
// getSQLResetByCustomerIDString
//-----------------------------------------------------
    public static String getSQLResetByCustomerIDString(String CustomerID) {
        String qryString= "SELECT * FROM btn WHERE CustomerID=\""+CustomerID+"\""; //select 1 btn
        if(DEBUG_ALL)
            System.out.println(qryString);
        return qryString;
    }
    
//-----------------------------------------------------
// getSQLSelectBTNString
//-----------------------------------------------------
    public static String getSQLSelectBTNString(String btn) {
        String qryString= "SELECT * FROM btn WHERE BTN=\""+btn+"\""; //select 1 btn
        if(DEBUG_ALL)
            System.out.println(qryString);
        return qryString;
    }
    
//-----------------------------------------------------
// getSQLResetString
//-----------------------------------------------------
    public static String getSQLResetString() {
        String qryString= "SELECT * FROM btn ORDER BY BTN"; //sort by default key
        if(DEBUG_ALL)
            System.out.println(qryString);
        return qryString;
    }
    
//-----------------------------------------------------
// getSQLInsertString
//-----------------------------------------------------
    public String getSQLInsertString() {
        String qryString = "INSERT INTO btn "+
        " Values(\""+ btn + 
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
        "\",\"" + ReviewNotes + 
        "\",\"" + prevCost + 
        "\",\"" + prevUpdated + 
        "\",\"" + MonthlySavings + 
        "\",\"" + Product + 
        "\",\"" + MRC + 
        "\",\"" + MonthlyService + 
        "\",\"" + ServiceOrders + 

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
        String qryString = "DELETE FROM btn WHERE BTN=\""+btn+"\"";
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }

    public String toString() {
        return btn;
    }
}
