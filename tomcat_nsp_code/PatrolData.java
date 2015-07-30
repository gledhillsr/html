import java.awt.*;
import java.awt.event.*;
import javax.swing.*;
import java.io.*;
import java.io.OutputStream;
import java.net.*;
import java.util.*;
import java.lang.*;
import javax.servlet.*;
import javax.servlet.http.*;
import java.sql.*;

public class PatrolData {
    //static variables
//old    final static String JDBC_DRIVER = "JData2_0.sql.$Driver";
    final static String JDBC_DRIVER = "org.gjt.mm.mysql.Driver";

            static String jdbcURL = "jdbc:mysql://localhost/xyzzy";
//            static String jdbcURL = "jdbc:mysql://localhost/brighton";
//            String jdbcURL = "jdbc:JDataConnect://"+IP_ADDRESS+"/CSRDATA:root:my_password";
//            if(DEBUG)
//                System.out.println("creating Connection");
//            connection =java.sql.DriverManager.getConnection(jdbcURL);
//good            connection =java.sql.DriverManager.getConnection(jdbcURL,"root","my_password");

    // create a Mountain Standard Time time zone
    final static String[] ids = TimeZone.getAvailableIDs(-7 * 60 * 60 * 1000);
    final static SimpleTimeZone MDT = new SimpleTimeZone(-7 * 60 * 60 * 1000, ids[0]);
    final static String newShiftStyle = "--New Shift Style--";


    // set up rules for daylight savings time
static {
    MDT.setStartRule(Calendar.APRIL, 1, Calendar.SUNDAY, 2 * 60 * 60 * 1000);
    MDT.setEndRule(Calendar.OCTOBER, -1, Calendar.SUNDAY, 2 * 60 * 60 * 1000);
}

/* ----- uncomment the following to run from the Internet ------ */
//  final static String IP_ADDRESS = "64.32.145.130";
/* ----- uncomment the following to run local ------*/
//    final static String IP_ADDRESS = "127.0.0.1";
/*----- end local declarations ------*/

/*************** start back door login stuff (works with ANY resort, and does NOT send any email confermations)*****************/
    final static String bdLogin = "sgled57";
    final static String bdPass  = "XXXXXXX";
    final static String bdFirst = "System";
    final static String bdLast  = "Administrator";
    final static String bdEmail = "Steve@Gledhills.com";
/*************** end back door login stuff *****************/

    final static int MAX_PATROLLERS = 400;
//    final static String SERVLET_URL = "http://"+IP_ADDRESS+":8080/nspCode/";

//    final static String SERVLET_URL = "http://127.0.0.1/nspCode/";
    final static String SERVLET_URL = "http://www.gledhills.com/nspCode/";
//    final static String SERVLET_URL = "";
//    final static String SERVLET_SHORT_URL = "/nspCode/";
/*----- end local declarations ------*/



/* ----- uncomment the following to run local ------ */

    final static int iDaysInMonth[] = {31,28,31,30,31,30,31,31,30,31,30,31};
    static final boolean FETCH_MIN_DATA = false;
    static final boolean FETCH_ALL_DATA = true;
/* -----end local declaration-----s */

    //all the folowing instance variables must be initialized in the constructor
    private Driver drv;
//  private Connection connection;
    Connection connection;
    private PreparedStatement rosterStatement;
    private ResultSet rosterResults;

    private PreparedStatement assignmentsStatement;
    private ResultSet assignmentResults;

    private PreparedStatement shiftStatement;
    private ResultSet shiftResults;

//    private PreparedStatement directorStatement;
//    private ResultSet directorResults;

//  MemberData member = null;
//  String szID = null;
//  Assignments assignments = null;
    private Calendar calendar;
    private boolean fetchFullData;
    private String localResort;

 //---------------
 // Constructor
 //---------------
    public PatrolData(boolean readAllData, String myResort) {
    jdbcURL = getJDBC_URL(myResort);
//  if(myResort.equalsIgnoreCase("UOP"))                jdbcURL = "jdbc:mysql://localhost/uop";
//  else if(myResort.equalsIgnoreCase("SoldierHollow")) jdbcURL = "jdbc:mysql://localhost/SoldierHollow";
//  else if(myResort.equalsIgnoreCase("Brighton"))      jdbcURL = "jdbc:mysql://localhost/brighton";
//  else if(myResort.equalsIgnoreCase("PebbleCreek"))   jdbcURL = "jdbc:mysql://localhost/PebbleCreek";
//  else if(myResort.equalsIgnoreCase("KellyCanyon"))   jdbcURL = "jdbc:mysql://localhost/KellyCanyon";
//  else if(myResort.equalsIgnoreCase("Sample"))        jdbcURL = "jdbc:mysql://localhost/Sample";
//  else
//      System.out.println("**** Error, unknown resort (" + myResort + ")");

//System.out.println("**11** database--jdbcURL (" + jdbcURL + ") myResort="+myResort);

    PreparedStatement rosterStatement = null;
    rosterResults = null;
    assignmentsStatement = null;
    assignmentResults = null;
    shiftStatement = null;
    shiftResults = null;
    localResort = myResort;

      // create a Mountain Standard Time time zone
      fetchFullData = readAllData;
      // create a GregorianCalendar with the Pacific Daylight time zone
      // and the current date and time
      calendar = new GregorianCalendar(MDT);

    try  {
////------- the following line works for an applet, but not for a servlet -----
      drv = (Driver) Class.forName(JDBC_DRIVER).newInstance();
    }
    catch (Exception e) {
        System.out.println("Cannot load the driver, reason:"+e.toString());
        System.out.println("Most likely the Java class path is incorrect.");
        return;
    }
// Try to connect to the database
    try {
      // Change MyDSN, myUsername and myPassword to your specific DSN
//PatrolData.jdbcURL,"root","my_password"
//System.out.println("-----localResort="+localResort);
//System.out.println("-----getJDBC_URL(localResort)="+getJDBC_URL(localResort));
      connection =java.sql.DriverManager.getConnection(getJDBC_URL(localResort),"root","my_password");
//..      connection =java.sql.DriverManager.getConnection(getJDBC_URL(localResort));
//System.out.println("-----------------------------");
//System.out.println("PatrolData.connection created");
//System.out.println("-----------------------------");

//prepare SQL for roster
      resetRoster();

//    connection.close(); // close MUST ba called explicity
    }  catch (Exception e) {
      System.out.println("Error connecting or reading table on open:"+e.getMessage());
      Thread.currentThread().dumpStack();
    } //end try
    } //end PatrolData constructor

//---------------
// resetAssignments
//---------------
    public void resetAssignments() {
        try {
          assignmentsStatement = connection.prepareStatement("SELECT * FROM Assignments ORDER BY \""+Assignments.tag[0]+"\"");   //sort by default key
          assignmentResults = assignmentsStatement.executeQuery();
        }  catch (Exception e) {
            System.out.println("Error reseting Assignments table query:"+e.getMessage());
        } //end try
    }

//---------------
// readNextAssignment
//---------------
    public Assignments readNextAssignment(){
        Assignments ns = null;
        try {
            if(assignmentResults.next()) {
            ns = new Assignments();
            ns.read(assignmentResults);
          }
        } catch (Exception e) {
            System.out.println("Cannot read Assignment, reason:"+e.toString());
            return null;
        }
        return ns;
    }


//---------------
// resetRoster
//---------------
    public void resetRoster() {
        try {
          rosterStatement = connection.prepareStatement("SELECT * FROM Roster ORDER BY LastName, FirstName");
          rosterResults = rosterStatement.executeQuery();
        }  catch (Exception e) {
            System.out.println("Error reseting Roster table query:"+e.getMessage());
        } //end try
    }

//---------------
// resetRoster
//---------------
    public void resetRoster(String sort) {
        try {
          rosterStatement = connection.prepareStatement("SELECT * FROM Roster ORDER BY "+sort);
          rosterResults = rosterStatement.executeQuery();
        }  catch (Exception e) {
            System.out.println("Error reseting Roster table query:"+e.getMessage());
        } //end try
    }

//---------------
// resetShifts
//---------------
    public void resetShifts() {
        try {
          shiftStatement = connection.prepareStatement("SELECT * FROM shiftdefinitions ORDER BY \""+Shifts.tags[0]+"\""); //sort by default key
          shiftResults = shiftStatement.executeQuery();
        }  catch (Exception e) {
            System.out.println("Error reseting Shifts table query:"+e.getMessage());
        } //end try
    }

//---------------
// resetDirectorSettings
//---------------
//  public void resetDirectorSettings() {
//      directorResults = DirectorSettings.reset(connection);
//  }

//---------------------------------------------------------------------
//     writeDirectorSettings - WRITE director settings
//---------------------------------------------------------------------
    public boolean writeDirectorSettings(DirectorSettings ds) {
        return ds.write(connection);
    }

//---------------
// readDirectorSettings
//---------------
    public DirectorSettings readDirectorSettings(){
        ResultSet directorResults;

        directorResults = DirectorSettings.reset(connection);
        DirectorSettings ds = null;
        try {
            if(directorResults.next()) {
              ds = new DirectorSettings(localResort);
              ds.read(directorResults);
            } else {
	          System.out.println("ERROR: directorResults.next() failed for resort: "+ds.getResort());
			}
        } catch (Exception e) {
            System.out.println("Cannot read DirectorSettings for resort "+ds.getResort()+", reason:"+e.toString());
            return null;
        }
        return ds;
    }



//---------------
// readNextShift
//---------------
    public Shifts readNextShift(){
        Shifts ns = null;
        try {
            if(shiftResults.next()) {
            ns = new Shifts();
            ns.read(shiftResults);
          }
        } catch (Exception e) {
            System.out.println("Cannot read Shift, reason:"+e.toString());
            return null;
        }
        return ns;
    }

//---------------------------------------------------------------------
//     decrementShift -
//---------------------------------------------------------------------
public void decrementShift(Shifts ns) {
System.out.println("decrement shift:"+ns);
    int i = ns.getEventIndex();
System.out.println("event index ="+i);

    if(i == 0)
        return;
    deleteShift(ns);
	String qry2String = Assignments.createAssignmentName(ns.parsedEventName(),i-1);
    ns.eventName = qry2String;
    writeShift(ns);
}
//---------------------------------------------------------------------
//     deleteShift - DELETE Shift assignment for a specified date and index
//---------------------------------------------------------------------
public void deleteShift(Shifts ns) {
//System.out.println("delete shift:"+ns);
    String qryString = ns.getDeleteSQLString();
System.out.println("qryString="+qryString);
    try {
        PreparedStatement sAssign = connection.prepareStatement(qryString);
        ResultSet rs = sAssign.executeQuery();
        ns.existed = false;
    } catch (Exception e) {
        System.out.println("Cannot delete Shift, reason:"+e.toString());
    }
}
//---------------------------------------------------------------------
//     writeShift - WRITE Shift assignment for a specified date and index
//---------------------------------------------------------------------
    public boolean writeShift(Shifts ns) {
        String qryString;
//System.out.println("write shift:"+ns);
        if(ns.exists()) {
            qryString = ns.getUpdateQueryString();
        } else {
            qryString = ns.getInsertQueryString();
        }
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
// close
//---------------
    public void close() {
        try {
//System.out.println("******************");
//System.out.println("PatrolData.close()");
//System.out.println("******************");
          connection.close(); //let it close in finalizer ??
        }  catch (Exception e) {
            System.out.println("Error connecting or reading table on close:"+e.getMessage());
            Thread.currentThread().dumpStack();
        } //end try
    } // end close

//---------------
// anyoneCanEdit
//---------------
//  static public boolean anyoneCanEdit() {
//        return allPatrollersCanEdit;
//    }

//---------------
// nextMember - enumerate through roster and get the next
//---------------
    public MemberData nextMember(String defaultString) {
        MemberData member = null;
        try {
            if(rosterResults.next()) {
                member = new MemberData();  //"&nbsp;" is the default
                member.readFullFromRoster(rosterResults,defaultString);
            } //end if
        }  catch (Exception e) {
            member = null;
            System.out.println("Error connecting or reading table on nextMember:"+e.getMessage());
            Thread.currentThread().dumpStack();
        } //end try

        return member;
    } //end nextMember

//-----------------------------------------------------------------
//  getMemberByID - given an ID, return the members data record
//-----------------------------------------------------------------
    public MemberData getMemberByID(String szMemberID) {
        MemberData member = null;
//  String str="SELECT * FROM Roster WHERE \"IDNumber\" = '"+szMemberID+"'";
  String str="SELECT * FROM Roster WHERE IDNumber ="+szMemberID;
//          String str="SELECT * FROM Roster";
        if(szMemberID == null || szMemberID.length() <= 3) {
            return null;
        }
        else if(szMemberID.equals(bdLogin)){
            member = new MemberData();  //"&nbsp;" is the default
            if(member != null) {
                member.setLast(bdLast);
                member.setFirst(bdFirst);
                member.setEmail(bdEmail);
                member.setID("000000");
                member.setDirector("yes");
            }
            return member;
        }
        try {
            rosterStatement = connection.prepareStatement(str);
            rosterResults = rosterStatement.executeQuery();
            while(rosterResults.next()) {
                int id=rosterResults.getInt("IDNumber");
                String str1 = id + "";
                if(str1.equals(szMemberID)) {
                    member = new MemberData();  //"&nbsp;" is the default
                    if(member == null)
                        return null;
                    if(fetchFullData)
                        member.readFullFromRoster(rosterResults, "");
                    else
                        member.readPartialFromRoster(rosterResults, "");
                    return member;
                }
            } //end while
        }  catch (Exception e) {
            member = null;
            System.out.println("Error in getMemberByID("+szMemberID+"): "+e.getMessage());
System.out.println("ERROR in PatrolData:getMemberByID("+szMemberID+") maybe a close was already done?");
Thread.currentThread().dumpStack();
        } //end try
        return member;
    } //end getMemberByID

//-----------------------------------------------------------------
//  getMemberByEmail - given an ID, return the members data record
//-----------------------------------------------------------------
    public MemberData getMemberByEmail(String szEmail) {
        MemberData member = null;
        String str="SELECT * FROM Roster WHERE email =\""+szEmail + "\"";
//System.out.println(str);
        try {
            rosterStatement = connection.prepareStatement(str);
            rosterResults = rosterStatement.executeQuery();
            if(rosterResults.next()) {
                member = new MemberData();  //"&nbsp;" is the default
                if(fetchFullData)
                    member.readFullFromRoster(rosterResults, "");
                else
                    member.readPartialFromRoster(rosterResults, "");
                return member;
            } //end while
        }  catch (Exception e) {
            member = null;
            System.out.println("Error in getMemberByEmail("+szEmail+"): "+e.getMessage());
        } //end try
        return member;
    } //end getMemberByID

//-------------------------------------------------------------------------
// getMemberByName - given a full name, return the members data record
//-------------------------------------------------------------------------
    public MemberData getMemberByName(String szFullName) {
        MemberData member = null;
        String str="SELECT * FROM Roster";
        try {
            rosterStatement = connection.prepareStatement(str);
            rosterResults = rosterStatement.executeQuery();
            while(rosterResults.next()) {
                int id=rosterResults.getInt("IDNumber");
                String str1 = rosterResults.getString("FirstName") + " " +
                              rosterResults.getString("LastName");
                if(str1.equals(szFullName)) {
                    member = new MemberData();  //"&nbsp;" is the default
                    if(fetchFullData)
                        member.readFullFromRoster(rosterResults, "");
                    else
                        member.readPartialFromRoster(rosterResults, "");
                    return member;
                }
            } //end while
        }  catch (Exception e) {
            member = null;
            System.out.println("Error reading table in getMemberByName("+szFullName+"):"+e.getMessage());
System.out.println("ERROR in PatrolData:getMemberByName("+szFullName+") maybe a close was already done?");
Thread.currentThread().dumpStack();
        } //end try
        return member;
    } //end getMemberByName

//-------------------------------------------------------------------------
// getMemberByName - given a full name, return the members data record
//-------------------------------------------------------------------------
    public MemberData getMemberByName2(String szFullName) {
    	return getMemberByLastNameFirstName(szFullName);
    } //end getMemberByName

//-------------------------------------------------------------------------
// getMemberByLastNameFirstName - given a full name, return the members data record
//-------------------------------------------------------------------------
    public MemberData getMemberByLastNameFirstName(String szFullName) {
        MemberData member = null;
        String str="SELECT * FROM Roster";
        try {
            rosterStatement = connection.prepareStatement(str);
            rosterResults = rosterStatement.executeQuery();
            while(rosterResults.next()) {
                int id=rosterResults.getInt("IDNumber");
                String str1 = rosterResults.getString("LastName") + ", " +
                              rosterResults.getString("FirstName");
                if(str1.equals(szFullName)) {
                    member = new MemberData();  //"&nbsp;" is the default
                    if(fetchFullData)
                        member.readFullFromRoster(rosterResults, "");
                    else
                        member.readPartialFromRoster(rosterResults, "");
                    return member;
                }
            } //end while
        }  catch (Exception e) {
            member = null;
            System.out.println("Error reading table in getMemberByName("+szFullName+"):"+e.getMessage());
System.out.println("ERROR in PatrolData:getMemberByName("+szFullName+") maybe a close was already done?");
Thread.currentThread().dumpStack();
        } //end try
        return member;
    } //end getMemberByName

//-------------------------------------------------------------------------
//     StringToIndex - 
//-------------------------------------------------------------------------
	public static int StringToIndex(String temp) {
		int i;
System.out.println("StringToIndex of "+temp);
		try {
	        i = Integer.parseInt(temp);
		} catch (Exception e) {
System.out.println("scanning for index of "+temp);
			char ch = temp.charAt(0);		
System.out.println("  char= "+ch);
			i = ch - 'A' + 10;
System.out.println("  index= "+i);
		}
        return i;
	}
//-------------------------------------------------------------------------
//     IndexToString - 
//-------------------------------------------------------------------------
	public static String IndexToString(int i) {
System.out.println("IndexToString of "+i);
		String val;
		if(i < 10)
			val = i + "";	 //force automatic conversion of integer to string
		else 
			val = String.valueOf((char)('A' + i - 10));
System.out.println("  value= "+val);
		return val;
	}
//-------------------------------------------------------------------------
//     readAssignments - given a date, return all night ski assignments
//-------------------------------------------------------------------------
    public Assignments readAssignment(String myDate) { //formmat yyyy-mm-dd_p
        Integer temp;
        Assignments ns = null;
        try {
            assignmentsStatement = connection.prepareStatement("SELECT * FROM Assignments WHERE Date=\'"+myDate+"\'");
            assignmentResults = assignmentsStatement.executeQuery();
            ns = readNextAssignment();
        } catch (Exception e) {
            System.out.println("Cannot load the driver, reason:"+e.toString());
            System.out.println("Most likely the Java class path is incorrect.");
            return null;
        }
        return ns;
    }

//---------------------------------------------------------------------
//  setValidDate - convert yyyy/mm/dd to string format in database
//---------------------------------------------------------------------
    String setValidDate(int currYear, int currMonth, int currDay) {
        String lastValidDate = currYear+"-";
        if(currMonth+1 < 10)
            lastValidDate += "0";
        lastValidDate += (currMonth+1)+"-";
        if(currDay < 10)
            lastValidDate += "0";
        lastValidDate += currDay;
        return lastValidDate;
    }

//---------------------------------------------------------------------
//     readAssignment - given a date, return all night ski assignments
//---------------------------------------------------------------------
    public Assignments readAssignment(int year, int month, int date) { //was readNightSki
        String szDate = setValidDate(year,month-1,date); //month should be 0 based
        return readAssignment(szDate);
    }

//---------------------------------------------------------------------
//     writeAssignment - WRITE all night ski assignments for a specified date
//---------------------------------------------------------------------
    public boolean writeAssignment(Assignments ns) { //was writeNightSki
        String qryString;
//System.out.println("in writeAssignment:"+ns.toString());
        if(ns.exists()) {
            qryString = ns.getUpdateQueryString();
        } else {
            qryString = ns.getInsertQueryString();
        }
//System.out.println(qryString);
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
//---------------------------------------------------------------------
//     decrementAssignment -
//---------------------------------------------------------------------
public void decrementAssignment(Assignments ns) {
System.out.println("decrement Assignment:"+ns);
//    int i = Integer.parseInt(ns.getDatePos()); //1 based
    int i = ns.getDatePos(); //1 based

    if(i <= 1)	//#'s are 1 based, can't decrement pos 1
        return;	
    deleteAssignment(ns);
	String qry2String = Shifts.createShiftName(ns.getDateOnly(),i-1);

    ns.setDate(qry2String);
    writeAssignment(ns);
}
//---------------------------------------------------------------------
//     deleteShift - DELETE Shift assignment for a specified date and index
//---------------------------------------------------------------------
public void deleteAssignment(Assignments ns) {
System.out.println("delete Assignment:"+ns);
    String qryString = ns.getDeleteSQLString();
    try {
        PreparedStatement sAssign = connection.prepareStatement(qryString);
        ResultSet rs = sAssign.executeQuery();
        ns.setExisted(false);
    } catch (Exception e) {
        System.out.println("Cannot delete Shift, reason:"+e.toString());
    }
}

//--------------------
// AddShiftsToDropDown
//--------------------
    static public void AddShiftsToDropDown(PrintWriter out, Vector shifts, String selectedShift) {
        String lastName = "";
        String parsedName;
        String selected = "";

        if(selectedShift == null)
            selected = " selected";
        out.println("                    <option"+selected+">"+newShiftStyle+"</option>");
        for(int i=0; i < shifts.size(); ++i) {
            Shifts data = (Shifts)shifts.get(i);
            parsedName = data.parsedEventName();
            if(parsedName.equals(selectedShift))
                selected = " selected";
            else
                selected = "";
            if(!parsedName.equals(lastName)) {
                out.println("<option"+selected+">"+parsedName+"</option>");
                lastName = parsedName;
            }
        }
    }
//--------------------
// countDropDown
//--------------------
   static private void countDropDown(PrintWriter out, String szName, int value) {
        out.println("<select size=\"1\" name=\""+szName+"\">");
        for(int i = Math.min(1,value); i <= Assignments.MAX; ++i) {
            if(i == value)
                out.println("<option selected>"+i+"</option>");
            else
                out.println("<option>"+i+"</option>");
        }
        out.println("                  </select>");
    }
//--------------------
// AddShiftsToTable
//--------------------
    static public void AddShiftsToTable(PrintWriter out, Vector shifts, String selectedShift) {
        int validShifts = 0;
        for(int i=0; i < shifts.size(); ++i) {
            Shifts data = (Shifts)shifts.get(i);
            String parsedName = data.parsedEventName();
            if(parsedName.equals(selectedShift)) {
//name is if the format of startTime_0, endTime_0, count_0, startTime_1, endTime_1, count_1, etc
// delete_0, delete_1
//shiftCount
                out.println("<tr>");
//delete button
//              out.println("<td width=\"103\"><input onClick=\"DeleteBtn()\" type=\"button\" value=\"Delete\" name=\"delete_"+validShifts+"\"></td>");
                out.println("<td><input type=\"submit\" value=\"Delete\" name=\"delete_"+validShifts+"\"></td>");
                out.println("<td>Start: <input type=\"text\" onKeyDown=\"javascript:return captureEnter(event.keyCode)\" name=\"startTime_"+validShifts+"\" size=\"7\" value=\""+data.getStartString()+"\"></td>");
                out.println("<td>End: <input type=\"text\" onKeyDown=\"javascript:return captureEnter(event.keyCode)\" name=\"endTime_"+validShifts+"\" size=\"7\" value=\""+data.getEndString()+"\"></td>");
                out.println("<td>Patroller&nbsp;Count:&nbsp;");
//                out.println("<input type=\"text\" name=\"count_"+validShifts+"\" size=\"4\" value=\""+data.getCount()+"\">");
                  countDropDown(out, "count_"+validShifts,data.getCount());
                out.println("</td>");
//add Day/Seing/Night shift
                out.println("<td>&nbsp;");
                out.println("<select size=1 name=\"shift_"+validShifts+"\">");
//System.out.println("in AddShiftsToTable, data.getType()="+data.getType());
				for(int j=0; j < Assignments.MAX_SHIFT_TYPES; ++j) {
					String sel = (data.getType() == j) ? "selected" : "";
    	            out.println("<option "+sel+">"+Assignments.szShiftTypes[j]+"</option>");
				}
                out.println("</select>");
                out.println("</td>");
                out.println("</tr>");
                ++validShifts;
            }
        }
        out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"shiftCount\" VALUE=\""+validShifts+"\">");
}

//--------------------
// AddAssignmentsToTable
//--------------------
    static public void AddAssignmentsToTable(PrintWriter out, Vector assignments) {
        int validShifts = 0;
        for(int i=0; i < assignments.size(); ++i) {
            Assignments data = (Assignments)assignments.elementAt(i);
            String parsedName = data.getEventName();
			int useCount = data.getUseCount();	//get # of patrollers actually assigned to this shift (warn if deleteing!)
//            if(parsedName.equals(selectedShift)) {
//name is if the format of startTime_0, endTime_0, count_0, startTime_1, endTime_1, count_1, etc
// delete_0, delete_1
//shiftCount
                out.println("<tr>");
//delete button
//              out.println("<td width=\"103\"><input onClick=\"DeleteBtn()\" type=\"button\" value=\"Delete\" name=\"delete_"+validShifts+"\"></td>");
                out.println("<td><input type=\"submit\" value=\"Delete\" onclick=\"return confirmShiftDelete("+useCount+")\" name=\"delete_"+validShifts+"\"></td>");
                out.println("<td>Start: <input type=\"text\" name=\"startTime_"+validShifts+"\" onKeyDown=\"javascript:return captureEnter(event.keyCode)\" size=\"7\" value=\""+data.getStartingTimeString()+"\"></td>");
                out.println("<td>End: <input type=\"text\" name=\"endTime_"+validShifts+"\" onKeyDown=\"javascript:return captureEnter(event.keyCode)\" size=\"7\" value=\""+data.getEndingTimeString()+"\"></td>");
                out.println("<td>Patroller&nbsp;Count:&nbsp;");
//                out.println("<input type=\"text\" name=\"count_"+validShifts+"\" size=\"4\" value=\""+data.getCount()+"\">");
                  countDropDown(out, "count_"+validShifts,data.getCount());
                out.println("</td>");
//add Day/Seing/Night shift
                out.println("<td>&nbsp;&nbsp; ");
                out.println("<select size=1 name=\"shift_"+validShifts+"\">");
//System.out.println("in AddAssignmentsToTable, data.getType()="+data.getType());
				for(int j=0; j < Assignments.MAX_SHIFT_TYPES; ++j) {
					String sel = (data.getType() == j) ? "selected" : "";
    	            out.println("<option "+sel+">"+Assignments.szShiftTypes[j]+"</option>");
				}
                out.println("</select>");
                out.println("</td>");

                out.println("</tr>");
                ++validShifts;
//            }
        }
        out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"shiftCount\" VALUE=\""+validShifts+"\">");
}
//--------------------
// validResort
//--------------------
    static public boolean validResort(String resort) {
    if(resort.equals("UOP"))                    return true;
    else if (resort.equals("SoldierHollow"))    return true;
    else if (resort.equals("Brighton"))         return true;
    else if (resort.equals("PebbleCreek"))      return true;
    else if (resort.equals("KellyCanyon"))      return true;
    else if (resort.equals("Afton"))            return true;
    else if (resort.equals("PineCreek"))        return true;
    else if (resort.equals("Pomerelle"))        return true;
    else if (resort.equals("GrandTarghee"))     return true;
    else if (resort.equals("Sample"))           return true;
    else if (resort.equals("WhitePine"))        return true;
    else if (resort.equals("RMSP")) 	        return true;
    else if (resort.equals("SnowKing"))         return true;
    else if (resort.equals("SnowBird"))         return true;  //hosts
    else if (resort.equals("scouts"))           return true;
    return false;
}
//--------------------
// resortFullName
//--------------------
    static public String getResortFullName(String resort) {
    if(resort.equals("UOP"))                    return "The Utah Olympic Park";
    else if (resort.equals("SoldierHollow"))    return "Soldier Hollow";
    else if (resort.equals("Brighton"))         return "Brighton";
    else if (resort.equals("PebbleCreek"))      return "Pebble Creek";
    else if (resort.equals("KellyCanyon"))      return "Kelly Canyon";
    else if (resort.equals("Afton"))            return "Afton Alps";
    else if (resort.equals("PineCreek"))        return "Pine Creek";
    else if (resort.equals("Pomerelle"))        return "Pomerelle";
    else if (resort.equals("GrandTarghee"))     return "Grand Targhee";
    else if (resort.equals("WhitePine"))        return "White Pine";
    else if (resort.equals("RMSP"))	 	        return "Ragged Mountain";
    else if (resort.equals("SnowKing"))         return "Snow King";
    else if (resort.equals("SnowBird"))         return "SnowBird";
    else if (resort.equals("Sample"))           return "Sample Resort";
    else if (resort.equals("scouts"))           return "Troop 316 Calendar";

    System.out.println("**** Error, unknown resort (" + resort + ")");
    return "Error, invalid resort";
}
//--------------------
// getJDBC_URL
//--------------------
    static public String getJDBC_URL(String resort) {
    if(resort.equals("UOP"))                    return "jdbc:mysql://127.0.0.1/uop";
    else if (resort.equals("SoldierHollow"))    return "jdbc:mysql://127.0.0.1/SoldierHollow";
    else if (resort.equals("Brighton"))         return "jdbc:mysql://127.0.0.1/brighton";
    else if (resort.equals("PebbleCreek"))      return "jdbc:mysql://127.0.0.1/PebbleCreek";
    else if (resort.equals("KellyCanyon"))      return "jdbc:mysql://127.0.0.1/KellyCanyon";
    else if (resort.equals("Afton"))            return "jdbc:mysql://127.0.0.1/Afton";
    else if (resort.equals("PineCreek"))        return "jdbc:mysql://127.0.0.1/PineCreek";
    else if (resort.equals("Pomerelle"))        return "jdbc:mysql://127.0.0.1/Pomerelle";
    else if (resort.equals("GrandTarghee"))     return "jdbc:mysql://127.0.0.1/GrandTarghee";
    else if (resort.equals("WhitePine"))        return "jdbc:mysql://127.0.0.1/WhitePine";
    else if (resort.equals("RMSP"))        	 	return "jdbc:mysql://127.0.0.1/RMSP";
    else if (resort.equals("SnowKing"))         return "jdbc:mysql://127.0.0.1/SnowKing";
    else if (resort.equals("SnowBird"))         return "jdbc:mysql://127.0.0.1/snowbird";
    else if (resort.equals("Sample"))           return "jdbc:mysql://127.0.0.1/Sample";
    else if (resort.equals("scouts"))           return "jdbc:mysql://127.0.0.1/scouts";

    System.out.println("**** Error, unknown resort (" + resort + ")");
    return "invalidResort";
}
} //end class PatrolData

