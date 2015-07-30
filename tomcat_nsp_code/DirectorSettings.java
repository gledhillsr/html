/**
 * Title:        DirectorSettings<p>
 * Description:  <p>
 * Copyright:    Copyright (c) 2001, 2002<p>
 * Company:      <p>
 * @author       Steve Gledhill
 * @version 1.0
 */

import java.io.*;
import java.text.*;
import java.util.*;
import javax.servlet.*;
import javax.servlet.http.*;
import java.lang.*;
import java.sql.*;


public class DirectorSettings {

    //static data
    //format EventName Saturday_0, Saturday_1, World Cup_0 etc
    //format StartTime 08:00 (text format)
    //format EndTime 08:00 (text format)
    //format Count (int)
    final static String tags[]   = {    /* in correct order */
// String         yes/no          int             yes/no         Yes/No          Yes/No            Yes/No   0-3        dd/mm     dd/mm (mm=1-12)  0/1       dd-mm-yy         dd-mm-yy
"PatrolName","emailReminder","reminderDays","emailOnChanges","useTeams",
"directorsOnlyChange","emailAll","nameFormat","startDate","endDate",
"useBlackOut","startBlackOut","endBlackOut",
"lastSkiHistoryUpdate", "lastVoucherHistoryUpdate", "signinLockout",
"removeAccess"};
    final static int PATROL_NAME_INDEX      = 0;
    final static int SEND_REMINDER_INDEX    = 1;
    final static int REMINDER_DAYS_INDEX    = 2;
    final static int NOTIFY_CHANGES_INDEX   = 3;
    final static int USE_TEAMS_INDEX        = 4;
    final static int DIRECTORS_CHANGE_INDEX = 5;
    final static int EMAIL_ALL_INDEX        = 6;
    final static int NAME_FORMAT_INDEX      = 7;
    final static int START_DATE_INDEX       = 8;
    final static int END_DATE_INDEX         = 9;
    final static int USE_BLACKOUT_INDEX     = 10;
    final static int START_BLACKOUT_INDEX   = 11;
    final static int END_BLACKOUT_INDEX     = 12;
    final static int LAST_SKI_HISTORY_UPDATE     = 13;
    final static int LAST_VOUCHER_HISTORY_UPDATE = 14;
    final static int SIGNIN_LOCKOUT              = 15;
	final static int REMOVE_ACCESS_INDEX	     = 16;
 //name  format
    final static int FULL_NAME               = 0;
    final static int FIRST_INITIAL_LAST_NAME = 1;
    final static int FIRST_ONLY              = 2;
    final static int FIRST_NAME_LAST_INITIAL = 3;

    //instance data
    private String szPatrolName;            //0
    private String szSendReminder;          //1
    private int    nReminderDays;           //2
    private String szNotifyChanges;         //3
    private String szUseTeams;              //4
    private String szDirectorsOnlyChange;   //5
    private String szEMailAll;              //6
    private int    nNameFormat;             //7
    private String szStartDate;             //8
    private String szEndDate;               //9
    private int nUseBlackout;               //10
    private String szStartBlackout;         //11
    private String szEndBlackout;           //12
    private int    nRemoveAccess;           //13
    private String resort;                  //variable
//-----------------------------------------------------
// constructor - store bogus patroller ID in each field
//-----------------------------------------------------
    public DirectorSettings(String myResort) {
        szPatrolName = null;
        szSendReminder = null;
        nReminderDays = 0;
        szNotifyChanges = null;
        szUseTeams = null;
        szDirectorsOnlyChange = null;
        szEMailAll = null;
        nNameFormat = 0;
        szStartDate = null;
        szEndDate = null;
        nUseBlackout = 0;
        szStartBlackout = null;
        szEndBlackout = null;
        nRemoveAccess = 0;        //2pm
        resort = myResort;
    }

//-----------------------------------------------------
// read
//-----------------------------------------------------
    public boolean read(ResultSet resultSet) {
        int Start,End;
        try {
            szPatrolName = resultSet.getString(tags[PATROL_NAME_INDEX]);
            szSendReminder = resultSet.getString(tags[SEND_REMINDER_INDEX]);
            nReminderDays = resultSet.getInt(tags[REMINDER_DAYS_INDEX]);
            szNotifyChanges = resultSet.getString(tags[NOTIFY_CHANGES_INDEX]);
            szUseTeams = resultSet.getString(tags[USE_TEAMS_INDEX]);
            szDirectorsOnlyChange = resultSet.getString(tags[DIRECTORS_CHANGE_INDEX]);
            szEMailAll = resultSet.getString(tags[EMAIL_ALL_INDEX]);
            nNameFormat = resultSet.getInt(tags[NAME_FORMAT_INDEX]);
            szStartDate = resultSet.getString(tags[START_DATE_INDEX]);
            szEndDate = resultSet.getString(tags[END_DATE_INDEX]);
            nUseBlackout    = resultSet.getInt(tags[USE_BLACKOUT_INDEX]);
            szStartBlackout = resultSet.getString(tags[START_BLACKOUT_INDEX]);
            szEndBlackout       = resultSet.getString(tags[END_BLACKOUT_INDEX]);
//ignore skihistory update, voucher history update, and signinLogin
            nRemoveAccess     = resultSet.getInt(tags[REMOVE_ACCESS_INDEX]);
//System.out.println("Reading DirectorSettings: "+toString());
        }  catch (Exception e) {
            System.out.println("exception in Shifts:read e="+e);
            return false;
        } //end try
        return true;
    }


    private int getYear(String szDate)    {
        int year = 0;   //error
        if(szDate.length() >= 8) { //dd-mm
            try {
                year = Integer.parseInt(szDate.substring(6,8));
                year += 2000;
            } catch (Exception e) {}
        }
        return year;
    }
    private int getMonth(String szDate)    {
        int mon = 0;    //error
        if(szDate.length() >= 5) { //dd-mm
            try {
                mon = Integer.parseInt(szDate.substring(3,5));
            } catch (Exception e) {}
        }
        return mon;
    }
    private int getDay(String szDate)     {
        int day = 0;    //error
        if(szDate.length() >= 5) { //dd-mm
            try {
                day = Integer.parseInt(szDate.substring(0,2));
            } catch (Exception e) {}
        }
        return day;
    }
    private String setYear(String szDate, int yr) {
        if(yr < 2002 || yr > 2099)
            return szDate;
        String szYr = "";
        yr -= 2000;
        if(yr <= 9)
            szYr += "0";
        szYr += yr;
        //szDate dd-mm-yy
        szDate = szDate.substring(0,6) + szYr;
//System.out.println("in setYear("+yr+") new ="+szDate);
        return szDate;
    }

    private String setMonth(String szDate, int mon) {
        if(mon < 1 || mon > 12)
            return szDate;
        String szMon = "";
        if(mon <= 9)
            szMon += "0";
        szMon += mon;
        //szDate dd-mm-yy
        if(szDate.length() > 5)
            szDate = szDate.substring(0,3) + szMon + szDate.substring(5);
        else
            szDate = szDate.substring(0,3) + szMon;
//System.out.println("in setMonth("+mon+") new ="+szDate);
        return szDate;
    }
    public String setDay(String szDate, int day) {  //format dd/mm/yy
//System.out.println("in setDay("+day+") original date="+szDate);
        if(day < 1 || day > 31)
            return szDate;
        String str = "";
        if(day <= 9)
            str += "0";
        szDate = str + day + szDate.substring(2);
//System.out.println("   setDay("+day+") new ="+szDate);
        return szDate;
    }

//-----------------------------------------------------
// Getter methods
//-----------------------------------------------------
    public boolean getSendReminder()  { return szSendReminder.equals("1");  }
    public int     getReminderDays()  { return nReminderDays;    }
    public boolean getNotifyChanges() { return szNotifyChanges.equals("1");  }
    public boolean getUseTeams()      { return szUseTeams.equals("1");  }
    public boolean getDirectorsOnlyChange(){ return szDirectorsOnlyChange.equals("1");  }
    public boolean getEmailAll()      { return szEMailAll.equals("1");  }
    public int     getNameFormat()    { return nNameFormat;  }
    public int     getStartMonth()    { return getMonth(szStartDate); }
    public int     getStartDay()      { return getDay(szStartDate); }
    public int     getEndMonth()      { return getMonth(szEndDate); }
    public int     getEndDay()        { return getDay(szEndDate);   }

    public int     getBlackOutStartDay()      { return getDay(szStartBlackout); }
    public int     getBlackOutStartMonth()    { return getMonth(szStartBlackout); }
    public int     getBlackOutStartYear()     { return getYear(szStartBlackout); }
    public int     getBlackOutEndDay()        { return getDay(szEndBlackout);   }
    public int     getBlackOutEndMonth()      { return getMonth(szEndBlackout); }
    public int     getBlackOutEndYear()       { return getYear(szEndBlackout); }
    public boolean getUseBlackOut()           { return (nUseBlackout > 0);  }

    public int getRemoveAccess()          { return nRemoveAccess; }
    public String     getResort()    { return resort;  }
 
    public String secondsToTime(int sec) {
        int min = ((sec % 3600)/60);
        return (sec / 3600) + ":" + ((min < 10) ? "0" : "") +min;
    }
//-----------------------------------------------------
// Setter methods
//-----------------------------------------------------
    public void setSendReminder(boolean flag)  { szSendReminder  = (flag ? "1" : "0");  }
    public void setReminderDays(int days)      { nReminderDays = days;    }
    public void setNotifyChanges(boolean flag) { szNotifyChanges = (flag ? "1" : "0");  }
    public void setUseTeams(boolean flag)      { szUseTeams = (flag ? "1" : "0");  }
    public void setDirectorsOnlyChange(boolean flag) { szDirectorsOnlyChange = (flag ? "1" : "0");  }
    public void setEmailAll(boolean flag)      { szEMailAll      = (flag ? "1" : "0");  }
    public void setNameFormat(int fmt)         { nNameFormat = fmt;  }
    public void setStartDay(int day)           { szStartDate = setDay(szStartDate, day); }
    public void setStartMonth(int mon)         { szStartDate = setMonth(szStartDate, mon); }
    public void setEndDay(int day)             { szEndDate = setDay(szEndDate, day); }
    public void setEndMonth(int mon)           { szEndDate = setMonth(szEndDate, mon); }

    public void setUseBlackOut(boolean bo)          { nUseBlackout = bo ? 1 : 0; }
    public void setBlackStartDay(int day)       { szStartBlackout = setDay(szStartBlackout, day); }
    public void setBlackStartMonth(int mon)     { szStartBlackout = setMonth(szStartBlackout, mon); }
    public void setBlackStartYear(int yr)       { szStartBlackout = setYear(szStartBlackout, yr); }
    public void setBlackEndDay(int day)         { szEndBlackout = setDay(szEndBlackout, day); }
    public void setBlackEndMonth(int mon)       { szEndBlackout = setMonth(szEndBlackout, mon); }
    public void setBlackEndYear(int yr)         { szEndBlackout = setYear(szEndBlackout, yr); }

    public void setRemoveAccess(int access) 	{ nRemoveAccess = access;  }

    public int timeToSeconds(String szTime) {
        int pos;
        int seconds = 28800; //default of 8:00
        String tmp;
        if(szTime == null)
            return seconds;

        if((pos = szTime.indexOf(':')) == -1) //-1 is NOT found
            return seconds;
        try {
            tmp = szTime.substring(0,pos);
            seconds = Integer.parseInt(tmp) * 3600;
//System.out.println("---h="+tmp);
            tmp = szTime.substring(pos+1);
//System.out.println("---m="+tmp);
            seconds += Integer.parseInt(tmp) * 60;
        } catch (Exception e) {
        }
        return seconds; //hack
    }

//-----------------------------------------------------
// getUpdateQueryString
//-----------------------------------------------------
    public String getUpdateQueryString() {
        String qryString = "UPDATE DirectorSettings SET " +
            " " + tags[SEND_REMINDER_INDEX] +"='" + szSendReminder +
            "', " + tags[REMINDER_DAYS_INDEX] +"='" + nReminderDays +
            "', " + tags[NOTIFY_CHANGES_INDEX] +"='" + szNotifyChanges +
            "', " + tags[USE_TEAMS_INDEX] +"='" + szUseTeams +
            "', " + tags[DIRECTORS_CHANGE_INDEX] +"='" + szDirectorsOnlyChange +
            "', " + tags[EMAIL_ALL_INDEX] +"='" + szEMailAll +
            "', " + tags[NAME_FORMAT_INDEX] +"='" + nNameFormat +
            "', " + tags[START_DATE_INDEX] +"='" + szStartDate +
            "', " + tags[END_DATE_INDEX] +"='" + szEndDate +
            "', " + tags[USE_BLACKOUT_INDEX] +"='" + nUseBlackout +
            "', " + tags[START_BLACKOUT_INDEX] +"='" + szStartBlackout +
            "', " + tags[END_BLACKOUT_INDEX] +"='" + szEndBlackout +
            "', " + tags[REMOVE_ACCESS_INDEX] +"='" + nRemoveAccess +
            "' WHERE "+tags[PATROL_NAME_INDEX]+"= '" + szPatrolName + "'";
System.out.println(qryString);
        return qryString;
    }

//-----------------------------------------------------
// getInsertQueryString
//-----------------------------------------------------
//  public String getInsertQueryString() {
//      String qryString = "INSERT INTO DirectorSettings "+
//                " Values('"+resort + "','" + szSendReminder + "','" +
//                    nReminderDays + "'," + szNotifyChanges+ "'," +
//                    nNameFormat+ "'," + szDirectorsOnlyChange+ "'," +
//                    eMailAll+")" ;
//System.out.println(qryString);
//      return qryString;
//  }

//-----------------------------------------------------
// getDeleteSQLString
//-----------------------------------------------------
//  public String getDeleteSQLString() {
//      int i;
//      String qryString = "DELETE FROM DirectorSettings WHERE \""+tags[PATROL_NAME_INDEX]+"\" = '"+resort+"'";
//System.out.println(qryString);
//      return qryString;
//  }

//-----------------------------------------------------
// write
//-----------------------------------------------------
    public boolean write(Connection connection) {
    String qryString;
System.out.println("write DirectorSettings for resort("+resort+"): "+this);
    qryString = getUpdateQueryString();
System.out.println(qryString);
    try {
        PreparedStatement sAssign = connection.prepareStatement(qryString);
        ResultSet rs = sAssign.executeQuery();
    } catch (Exception e) {
        System.out.println("Cannot load the driver, reason:"+e.toString());
        System.out.println("Most likely the Java class path is incorrect.");
        return true;
    }
    return false;
}
//---------------
// reset
//---------------
    static public ResultSet reset(Connection connection) {
        PreparedStatement directorStatement;
        try {
          directorStatement = connection.prepareStatement("SELECT * FROM DirectorSettings ORDER BY \""+DirectorSettings.tags[PATROL_NAME_INDEX]+"\""); //sort by default key
          return directorStatement.executeQuery();
        }  catch (Exception e) {
            System.out.println("Error reseting DirectorSettings table query:"+e.getMessage());
        } //end try
        return null;
    }
//-----------------------------------------------------
// toString
//-----------------------------------------------------
    public String toString() {
        return szPatrolName +
            " "+tags[SEND_REMINDER_INDEX]   +"="+szSendReminder+
            " "+tags[REMINDER_DAYS_INDEX]   +"="+nReminderDays+
            " "+tags[NOTIFY_CHANGES_INDEX]  +"="+szNotifyChanges+
            " "+tags[USE_TEAMS_INDEX]       +"="+szUseTeams+
            " "+tags[DIRECTORS_CHANGE_INDEX]+"="+szDirectorsOnlyChange+
            " "+tags[EMAIL_ALL_INDEX]       +"="+szEMailAll+
            " "+tags[NAME_FORMAT_INDEX]       +"="+nNameFormat+
            " "+tags[START_DATE_INDEX]       +"="+szStartDate+
            " "+tags[END_DATE_INDEX]       +"="+szEndDate+
            " "+tags[REMOVE_ACCESS_INDEX]  +"="+nRemoveAccess;
    }
} //end MemberData class
