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
public class Npanxx {
    static boolean DEBUG = false;
    private boolean exceptionError;
	
/**** database fields (in order) ******/    
    String Place;
    String State;    
    String NPANXX;
    String Tier;
/*************************************/    
    
    /** Creates a new instance of Npanxx */
    public Npanxx() {
        Place = "";    
        State = "";
        NPANXX = "";
        Tier = "";
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
