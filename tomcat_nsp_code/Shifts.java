//
// file: Shifts.java
// written by Steve Gledhill
//
import java.io.*;
import java.text.*;
import java.util.*;
import javax.servlet.*;
import javax.servlet.http.*;
import java.lang.*;
import java.sql.*;


public class Shifts {

    //static data
    //format EventName Saturday_0, Saturday_1, World Cup_0 etc
    //format StartTime 08:00 (text format)
    //format EndTime 08:00 (text format)
    //format Count (int)
	final static String tags[]   = {"EventName","StartTime","EndTime","Count","ShiftType"};	//string on form
	final static int EVENT_NAME_INDEX 	= 0;
	final static int START_TIME_INDEX 	= 1;
	final static int END_TIME_INDEX 	= 2;
	final static int COUNT_INDEX 		= 3;
	final static int TYPE_INDEX  	    = 4;

    final static SimpleDateFormat timeFormatter = new SimpleDateFormat ("HH:mm");
    final static int MAX = 35;  //maximum # of different shifts on any single day

    //instance data
	String eventName;
	private String startTime;
	private String endTime;
	private int count;
	private int type;
//	final static int DAY = 0;
//	final static int SWING = 1;
//	final static int NIGHT = 2;

    boolean existed;

//-----------------------------------------------------
// constructor - store bogus patroller ID in each field
//-----------------------------------------------------
	public Shifts() {
		eventName = null;
		startTime = null;
		endTime = null;
		count = 0;
		type=0;	//day shift
        existed = false;
	}

	public Shifts(String name, String start, String end, int cnt, int typ) {
		eventName = name;
		startTime = start;
		endTime = end;
		count = cnt;
		type = typ;
		if(type < 0 || type >= Assignments.MAX_SHIFT_TYPES)
			type = 0;	//reset to default
        existed = false;
	}

    public boolean equals(Shifts other) {
        return other.eventName.equals(eventName) &&
              other.startTime.equals(startTime) &&
              other.endTime.equals(endTime) &&
              (other.count == count) &&
              (other.type == type);
    }
//-----------------------------------------------------
// read
//-----------------------------------------------------
	public boolean read(ResultSet shiftResults) {
        int Start,End;
		try {
    		eventName = shiftResults.getString(tags[EVENT_NAME_INDEX]);
            startTime = shiftResults.getString(tags[START_TIME_INDEX]);
            endTime = shiftResults.getString(tags[END_TIME_INDEX]);
	    	count = shiftResults.getInt(tags[COUNT_INDEX]);
	    	type = shiftResults.getInt(tags[TYPE_INDEX]);
			if(type < 0 || type >= Assignments.MAX_SHIFT_TYPES)
				type = 0;	//reset to default, if invalid
//System.out.println("read shift: "+toString());
            existed = true;
		}  catch (Exception e) {
            System.out.println("exception in Shifts:read e="+e);
			return false;
		} //end try
		return true;
	}

//-----------------------------------------------------
// exists
//-----------------------------------------------------
	public boolean exists() {
        return existed;
    }

//-----------------------------------------------------
// createShiftName
//-----------------------------------------------------
	public static String createShiftName(String name, int i) {
		return  name + "_" + PatrolData.IndexToString(i);
    }


//-----------------------------------------------------
// getEventIndex
//-----------------------------------------------------
	public int getEventIndex() {
        String temp =  eventName.substring(eventName.lastIndexOf("_")+1);
		return PatrolData.StringToIndex(temp);
    }

//-----------------------------------------------------
// parsedEventName
//-----------------------------------------------------
	public String parsedEventName() {
        return eventName.substring(0, eventName.lastIndexOf("_"));
    }

//-----------------------------------------------------
// getEventName
//-----------------------------------------------------
	public String getEventName() {
 	    return eventName;
    }
//-----------------------------------------------------
// getStartString
//-----------------------------------------------------
	public String getStartString() {
 	    return startTime;
    }
//-----------------------------------------------------
// getEndString
//-----------------------------------------------------
	public String getEndString() {
 	    return endTime;
    }
//-----------------------------------------------------
// getCount
//-----------------------------------------------------
	public int getCount() {
 	    return count;
    }

//-----------------------------------------------------
// getType
//-----------------------------------------------------
	public int getType() {
 	    return type;
    }

//-----------------------------------------------------
// getUpdateQueryString
//-----------------------------------------------------
	public String getUpdateQueryString() {
    	String qryString = "UPDATE shiftdefinitions SET " +
            " " + tags[START_TIME_INDEX] +"='" + startTime +
            "', " + tags[END_TIME_INDEX] +"='" + endTime +
            "', " + tags[COUNT_INDEX] +"='" + count +
            "', " + tags[TYPE_INDEX] +"='" + type +
            "' WHERE "+tags[EVENT_NAME_INDEX]+"= '" + eventName + "'";
System.out.println(qryString);
		return qryString;
	}

//-----------------------------------------------------
// getInsertQueryString
//-----------------------------------------------------
	public String getInsertQueryString() {
//        int start = startTime.get(Calendar.HOUR)*startTime.get(Calendar.MINUTE);
//        int end = endTime.get(Calendar.HOUR)*endTime.get(Calendar.MINUTE);
		String qryString = "INSERT INTO shiftdefinitions "+
                " Values('"+eventName + "','" + startTime + "','" + endTime + "','" + count + "'," + type+")" ;
System.out.println(qryString);
		return qryString;
	}

//-----------------------------------------------------
// getDeleteSQLString
//-----------------------------------------------------
	public String getDeleteSQLString() {
		int i;
		String qryString = "DELETE FROM shiftdefinitions WHERE "+tags[EVENT_NAME_INDEX]+" = '"+eventName+"'";
System.out.println(qryString);
		return qryString;
	}

//-----------------------------------------------------
// toString
//-----------------------------------------------------
	public String toString() {
//        int start = startTime.get(Calendar.HOUR)*startTime.get(Calendar.MINUTE);
//        int end = endTime.get(Calendar.HOUR)*endTime.get(Calendar.MINUTE);
		return eventName + " starts: "+startTime+" ends: "+endTime+" count="+count+" type="+Assignments.szShiftTypes[type];
	}
} //end MemberData class

