/**
 * Title:        Preferences<p>
 * Description:  Directors Preferences<p>
 * Copyright:    Copyright (c) 2001-2002<p>
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


public class Preferences extends HttpServlet {

    String szMyID;
    PrintWriter out;
    boolean onlyDirectors;
    int reminderDays;
    boolean emailPatrollers;
    boolean emailReminder;
//    boolean useTeams;
    boolean madeUpdate;
    boolean emailAll;
    int nameFormat;
    int startDay;
    int startMonth;
    int endDay;
    int endMonth;
    boolean nUseBlackOut;
    int blackOutStartDay;
    int blackOutStartMonth;
    int blackOutStartYear;
    int blackOutEndDay;
    int blackOutEndMonth;
    int blackOutEndYear;
	int removeAccess;


    private String resort;

    String szMonths[] = {"Error",
    "January", "February", "March", "April", "May", "June",
    "July", "August","September","October","November","December"
    };
//------------
// doGet
//------------
    public void doGet(HttpServletRequest request,
                      HttpServletResponse response)
        throws IOException, ServletException
    {
        response.setContentType("text/html");
synchronized (this) {
        out = response.getWriter();
        CookieID cookie = new CookieID(request,response,"Preferences",null);
        resort = request.getParameter("resort");
        if(cookie != null)
            szMyID = cookie.getID();
        readParameters(request);
        printTop();
        if(PatrolData.validResort(resort))
            printBody();
        else
            out.println("Invalid host resort.");
        printBottom();
} //end Syncronized
    }

//------------
// doPost
//------------
    public void doPost(HttpServletRequest request,
                      HttpServletResponse response)
        throws IOException, ServletException
    {
        doGet(request, response);
    }

//------------
// printTop
//------------
    public void printTop() {
        out.println("<html><head>");
        out.println("<meta http-equiv=\"Content-Language\" content=\"en-us\">");
        out.println("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1252\">");
        out.println("<title>Directors Preferences</title>");
//force page NOT to be cached
        out.println("<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">");
        out.println("<META HTTP-EQUIV=\"Expires\" CONTENT=\"-1\">");
//all JavaScript code
        out.println("<SCRIPT LANGUAGE=\"JavaScript\">");
//cancel button pressed
        out.println("function goHome() {");
        out.println("location.href = \""+PatrolData.SERVLET_URL+"Directors?resort="+resort+"&ID="+szMyID+"\"");
        out.println("}");

        out.println("</SCRIPT>");
//end JavaScript code

        out.println("<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">");
        out.println("<META HTTP-EQUIV=\"Expires\" CONTENT=\"-1\">");
        out.println("</head>");
        out.println("<body>");

    }

//------------
// readData
//------------
    private void readParameters(HttpServletRequest request) {
    PatrolData patrol = new PatrolData(PatrolData.FETCH_MIN_DATA,resort); //when reading members, read minimal data
    DirectorSettings ds = patrol.readDirectorSettings();
//System.out.println("Original settings: "+ds.toString());
    String temp = request.getParameter("XYZ"); //allways a non-null value when returning
    madeUpdate = (temp != null);
    if(!madeUpdate) {
        //read data from database
        onlyDirectors   = ds.getDirectorsOnlyChange();
        reminderDays    = ds.getReminderDays();
        emailPatrollers   = ds.getNotifyChanges();
        emailReminder = ds.getSendReminder();
//        useTeams = ds.getUseTeams();
        emailAll = ds.getEmailAll();
        nameFormat = ds.getNameFormat();
        startDay = ds.getStartDay();
        startMonth = ds.getStartMonth();
        endDay = ds.getEndDay();
        endMonth = ds.getEndMonth();

        nUseBlackOut        = ds.getUseBlackOut();
        blackOutStartDay    = ds.getBlackOutStartDay();
        blackOutStartMonth  = ds.getBlackOutStartMonth();
        blackOutStartYear   = ds.getBlackOutStartYear();
        blackOutEndDay      = ds.getBlackOutEndDay();
        blackOutEndMonth    = ds.getBlackOutEndMonth();
        blackOutEndYear     = ds.getBlackOutEndYear();

        removeAccess       = ds.getRemoveAccess();

    } else {
        //read data from parameters
        emailReminder   = (request.getParameter(                DirectorSettings.tags[DirectorSettings.SEND_REMINDER_INDEX]) != null);
//System.out.println("--"+DirectorSettings.tags[DirectorSettings.REMINDER_DAYS_INDEX])+"----"+request.getParameter( DirectorSettings.tags[DirectorSettings.REMINDER_DAYS_INDEX]) );
        String foo = request.getParameter( DirectorSettings.tags[DirectorSettings.REMINDER_DAYS_INDEX]);
        reminderDays = Integer.parseInt(foo.trim());
        emailPatrollers = (request.getParameter( DirectorSettings.tags[DirectorSettings.NOTIFY_CHANGES_INDEX]) != null);
//      useTeams    = (request.getParameter( DirectorSettings.tags[DirectorSettings.USE_TEAMS_INDEX]) != null);
        onlyDirectors = (request.getParameter( DirectorSettings.tags[DirectorSettings.DIRECTORS_CHANGE_INDEX]) != null);
        emailAll    = (request.getParameter( DirectorSettings.tags[DirectorSettings.EMAIL_ALL_INDEX]) != null);
//System.out.println("1) "+request.getParameter( DirectorSettings.tags[DirectorSettings.NAME_FORMAT_INDEX]));
//System.out.println("2) "+request.getParameter( "startDay"));
//System.out.println("3) "+request.getParameter( "startMonth"));
//System.out.println("4) "+request.getParameter( "endDay"));
//System.out.println("5) "+request.getParameter( "endMonth"));

        nameFormat = 0;
//      String str = request.getParameter( DirectorSettings.tags[DirectorSettings.NAME_FORMAT_INDEX]);
//      if(str.equals("V0")) nameFormat = 0;
//      else if(str.equals("V1")) nameFormat = 1;
//      else if(str.equals("V2")) nameFormat = 2;
//      else if(str.equals("V3")) nameFormat = 3;
        nUseBlackOut    = (request.getParameter( DirectorSettings.tags[DirectorSettings.USE_BLACKOUT_INDEX]) != null);
        try {
            startDay    = Integer.parseInt(request.getParameter( "startDay" ));
            startMonth  = Integer.parseInt(request.getParameter( "startMonth"));
            endDay      = Integer.parseInt(request.getParameter( "endDay" ));
            endMonth    = Integer.parseInt(request.getParameter( "endMonth"));

            blackOutStartDay    = Integer.parseInt(request.getParameter( "blackOutStartDay" ));
            blackOutStartMonth  = Integer.parseInt(request.getParameter( "blackOutStartMonth" ));
            blackOutStartYear   = Integer.parseInt(request.getParameter( "blackOutStartYear" ));
//System.out.println(":-) "+blackOutStartDay+"/"+blackOutStartMonth+"/"+blackOutStartYear);
            blackOutEndDay      = Integer.parseInt(request.getParameter( "blackOutEndDay" ));
            blackOutEndMonth    = Integer.parseInt(request.getParameter( "blackOutEndMonth" ));
            blackOutEndYear     = Integer.parseInt(request.getParameter( "blackOutEndYear" ));
//System.out.println(":-( "+blackOutEndDay+"/"+blackOutEndMonth+"/"+blackOutEndYear);
            removeAccess       = Integer.parseInt(request.getParameter( "removeAccess" ));

        } catch (Exception e) {
            return; //really should never happen
        }
        //write data to database
        ds.setDirectorsOnlyChange(onlyDirectors);
        ds.setReminderDays(reminderDays);
        ds.setSendReminder(emailReminder);
        ds.setNotifyChanges(emailPatrollers);
//        ds.setUseTeams(useTeams);
        ds.setEmailAll(emailAll);
        ds.setNameFormat(nameFormat);
        ds.setStartDay(startDay);
        ds.setStartMonth(startMonth);
        ds.setEndDay(endDay);
        ds.setEndMonth(endMonth);

        ds.setUseBlackOut(nUseBlackOut);
        ds.setBlackStartDay(blackOutStartDay);
        ds.setBlackStartMonth(blackOutStartMonth);
        ds.setBlackStartYear(blackOutStartYear);
        ds.setBlackEndDay(blackOutEndDay);
        ds.setBlackEndMonth(blackOutEndMonth);
        ds.setBlackEndYear(blackOutEndYear);

        ds.setRemoveAccess(removeAccess);

        patrol.writeDirectorSettings(ds);
    }

    patrol.close();
}

//------------
// printBottom
//------------
    private void printBottom() {
//      out.println("<p><br></p>");
//        out.println("As of: "+new java.util.Date());
        out.println("</body>");
//force page NOT to be cached
//        out.println("<HEAD>");
//        out.println("<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">");
//        out.println("<META HTTP-EQUIV=\"Expires\" CONTENT=\"-1\">");
//        out.println("</HEAD>");
        out.println("</html>");


    }

//------------
// getServletInfo
//------------
    public String getServletInfo() {
    return "Director Preferences Settings";
    }


//------------
// printBody
//------------

    public void printBody() {
        String tag;
        String readOnly = "";
        if(madeUpdate)
            readOnly = " DISABLED ";

        out.println("<h1 align=\"center\">Web Site Preferences for "+PatrolData.getResortFullName(resort)+"</h1>");
        out.println("<form name=myForm action=\""+PatrolData.SERVLET_URL+"Preferences\" method=POST>");
        out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"resort\" VALUE=\""+resort+"\">");
        out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"ID\" VALUE=\""+szMyID+"\">");
        out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"XYZ\" VALUE=\"XYZ\">");
//define table
        out.println("  <table border=1 cellpadding=0 cellspacing=0 style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=\"100%\">\n");
//--- row 1 E-MAIL
        out.println("    <tr>\n");
        out.println("      <td width=\"8%\" align=\"center\">E-mail<br><br></td>\n");
        out.println("      <td width=\"92%\">\n");
//Email patrollers when changes are made
        tag=DirectorSettings.tags[DirectorSettings.NOTIFY_CHANGES_INDEX];
        out.println("  <input type=\"checkbox\" "+readOnly+" name=\""+tag+"\" value=\"ON\" "+(emailPatrollers ? "checked" : "")+">");
        out.println("  E-mail affected patrollers <B>when shift assignments are made</B> on the <B>Calendar</B>.<br>");
//Send email reminder # days before shift
        tag=DirectorSettings.tags[DirectorSettings.SEND_REMINDER_INDEX];
        out.println("  <input type=\"checkbox\" "+readOnly+"name=\""+tag+"\" value=\"ON\" "+(emailReminder ? "checked" : "")+">");
//count number of days before email
        tag=DirectorSettings.tags[DirectorSettings.REMINDER_DAYS_INDEX];
        out.println("  Send E-mail <b>Reminder</b> <select "+readOnly+" size=\"1\" name=\""+tag+"\">");
        for(int i=1; i <= 3; ++i)
            out.println("    <option "+((i == reminderDays) ? "selected" : "") + ">"+i+"</option>");
        out.println("  </select> day(s) before scheduled shift assignment.&nbsp;&nbsp;<br>");
//Directors ONLY can email all
        tag=DirectorSettings.tags[DirectorSettings.EMAIL_ALL_INDEX];
        out.println("  <input type=\"checkbox\" name=\""+tag+"\" "+readOnly+" value=\"ON\" "+(emailAll ? "checked" : "")+">");
        out.println("  Allow all patrollers to &quot;email All patrollers&quot; from the <b>Member List</b> window.<br>");
        out.println("      </td>\n");
        out.println("    </tr>\n");
//---row 2 SEASON ---
        out.println("    <tr>\n");
        out.println("      <td width=\"8%\" align=\"center\">&nbsp;Season&nbsp;<br><br></td>\n");
        out.println("      <td width=\"92%\">\n");
//
        out.println("Season <b>Starts</b> on <select "+readOnly+" size=\"1\" name=\"startDay\">");
        for(int i=1; i <= 31; ++i)
            out.println("    <option "+((i == startDay) ? "selected" : "") + ">"+i+"</option>");
        out.println("  </select>&nbsp;&nbsp;<select "+readOnly+" size=\"1\" name=\"startMonth\">");
        for(int i=1; i <= 12; ++i)
            out.println("    <option value=\""+i+"\" "+((i == startMonth) ? "selected" : "") + ">"+szMonths[i]+"</option>");
        out.println("  </select>, ");
        out.println(" and <b>Ends</b> on <select "+readOnly+" size=\"1\" name=\"endDay\">");
        for(int i=1; i <= 31; ++i)
            out.println("    <option "+((i == endDay) ? "selected" : "") + ">"+i+"</option>");
        out.println("  </select>&nbsp;&nbsp;<select "+readOnly+" size=\"1\" name=\"endMonth\">");
        for(int i=1; i <= 12; ++i)
            out.println("    <option value=\""+i+"\" "+((i == endMonth) ? "selected" : "") + ">"+szMonths[i]+"</option>");
        out.println("  </select> <br>");
        out.println("&nbsp;&nbsp;Season dates are used so <b>no</b> shifts or assignments are displayed out-of-season.<br>");
        out.println("&nbsp;&nbsp;And so you can \"remove all of last season's assignments\"<br>");
        out.println("      </td>\n");
        out.println("    </tr>\n");
//---row 3 CALENDAR ---
        out.println("    <tr>\n");
        out.println("      <td width=\"8%\" align=\"center\">&nbsp;&nbsp;&nbsp;Calendar&nbsp;&nbsp;&nbsp;<br><br></td>\n");
        out.println("      <td width=\"92%\">\n");
//Directors ONLY can change calendar
        tag=DirectorSettings.tags[DirectorSettings.DIRECTORS_CHANGE_INDEX];
        out.println("  <input type=\"checkbox\" "+readOnly+" name=\""+tag+"\" value=\"ON\" "+(onlyDirectors ? "checked" : "")+">");
        out.println("  Only allow Directors to make shift assignment changes on the <B>Calendar</B>.<br>");


//When can Patrollers can REMOVE a name
        tag=DirectorSettings.tags[DirectorSettings.REMOVE_ACCESS_INDEX];
        out.println("When can patrollers <b>REMOVE</b> a name from the <B>Calendar</B>.&nbsp; ");
                out.println("<select size=1 name=\""+tag+"\">");
                out.println("<option value=0 "+((removeAccess==0?"selected":""))+">Anytime</option>");
                out.println("<option value=1 "+((removeAccess==1?"selected":""))+">more that 1 day BEFORE event</option>");
                out.println("<option value=2 "+((removeAccess==2?"selected":""))+">more that 2 days BEFORE event</option>");
                out.println("<option value=7 "+((removeAccess==7?"selected":""))+">more that 7 days BEFORE event</option>");
                out.println("<option value=14 "+((removeAccess==14?"selected":""))+">more that 14 days BEFORE event</option>");
                out.println("<option value=10000 "+((removeAccess==10000?"selected":""))+">Never.  REMOVE not allowed!</option>");  //10,000 days
                out.println("</select><br>&nbsp;&nbsp;&nbsp;(Assuming a patroller can modify the calendar)<br>");

//blackout dates
        tag = DirectorSettings.tags[DirectorSettings.USE_BLACKOUT_INDEX];
        out.println("  <input type=\"checkbox\" name=\""+tag+"\" "+readOnly+" value=\"ON\" "+(nUseBlackOut ? "checked" : "")+">");
        out.println("  Set a blackout period so patrollers <b>cannot</b> sign up for <b>weekends</b> in that time period.");
        out.println("<br>&nbsp;&nbsp;Blackout period <b>Starts</b> on <select "+readOnly+" size=\"1\" name=\"blackOutStartDay\">");
        for(int i=1; i <= 31; ++i)
            out.println("    <option "+((i == blackOutStartDay) ? "selected" : "") + ">"+i+"</option>");
        out.println("  </select>&nbsp;&nbsp;<select "+readOnly+" size=\"1\" name=\"blackOutStartMonth\">");
        for(int i=1; i <= 12; ++i)
            out.println("    <option value=\""+i+"\" "+((i == blackOutStartMonth) ? "selected" : "") + ">"+szMonths[i]+"</option>");
        out.println("  </select>&nbsp;&nbsp;<select "+readOnly+" size=\"1\" name=\"blackOutStartYear\">");

        Calendar cal = Calendar.getInstance();
        int year = cal.get(Calendar.YEAR);


        for(int i=year-1; i <= year+2; ++i)
            out.println("    <option value=\""+i+"\" "+((i == blackOutStartYear) ? "selected" : "") + ">"+i+"</option>");
        out.println("  </select>, <br>");
        out.println("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");

        out.println(" and <b>Ends</b> on <select "+readOnly+" size=\"1\" name=\"blackOutEndDay\">");
        for(int i=1; i <= 31; ++i)
            out.println("    <option "+((i == blackOutEndDay) ? "selected" : "") + ">"+i+"</option>");
        out.println("  </select>&nbsp;&nbsp;<select "+readOnly+" size=\"1\" name=\"blackOutEndMonth\">");
        for(int i=1; i <= 12; ++i)
            out.println("    <option value=\""+i+"\" "+((i == blackOutEndMonth) ? "selected" : "") + ">"+szMonths[i]+"</option>");
        out.println("  </select> ");
        out.println("  </select>&nbsp;&nbsp;<select "+readOnly+" size=\"1\" name=\"blackOutEndYear\">");
        for(int i=year-1; i <= year+2; ++i)
            out.println("    <option value=\""+i+"\" "+((i == blackOutEndYear) ? "selected" : "") + ">"+i+"</option>");
        out.println("  </select>");
        out.println("      </td>\n");
        out.println("    </tr>\n");

//end of table
        out.println("      </td>\n");
        out.println("    </tr>\n");
        out.println("  </table>\n");
//useTeams for shift assignments
//      tag=DirectorSettings.tags[DirectorSettings.USE_TEAMS_INDEX];
//        out.println("  <input type=\"checkbox\" name=\""+tag+"\" value=\"ON\" "+(useTeams ? "checked" : "")+">");
//        out.println("  Use <B>Teams</B> when making <b>Calendar</b> shift assignments. <B>(NOT IMPLEMENTED YET!)</B><br>");

//How should names appear on calendar
//        tag=DirectorSettings.tags[DirectorSettings.NAME_FORMAT_INDEX];
//        int nameFormat = 0;
//        out.println("  <p>How do you want names to appear on the Calendar&nbsp;&nbsp;<B>(NOT IMPLEMENTED YET!)</B><br>");
//        String c0 = (nameFormat == 0) ? "checked" : "";
//        String c1 = (nameFormat == 1) ? "checked" : "";
//        String c2 = (nameFormat == 2) ? "checked" : "";
//        String c3 = (nameFormat == 3) ? "checked" : "";
//        out.println("  &nbsp;&nbsp;&nbsp; <input type=\"radio\" value=\"V0\" "+c0+" name=\""+tag+"\"> George");
//        out.println("  Bush&nbsp;&nbsp; (Full Name)<br>");
//        out.println("  &nbsp;&nbsp;&nbsp; <input type=\"radio\" value=\"V1\" "+c1+" name=\""+tag+"\"> G");
//        out.println("  Bush&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (1st");
//        out.println("  Initial and Last Name)<br>");
//        out.println("  &nbsp;&nbsp;&nbsp; <input type=\"radio\" value=\"V2\" "+c2+" name=\""+tag+"\">");
//        out.println("  George&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
//        out.println("  (First Name)<br>");
//        out.println("  &nbsp;&nbsp;&nbsp; <input type=\"radio\" value=\"V3\" "+c3+" name=\""+tag+"\"> George");
//        out.println("  B&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (First Name and Last Initial)</p>");

        if(madeUpdate) {
            out.println("<br>&nbsp;&nbsp;&nbsp;&nbsp;<font size=4><b>Changes Saved.</b></font>");
            out.println("&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;  <input type=\"button\" value=\"Return\" name=\"B3\" onClick=\"goHome()\"></p>");
        } else {
            out.println("  <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type=\"submit\" value=\"Save Changes\" name=\"B1\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
            out.println("  <input type=\"button\" value=\"Cancel\" name=\"B3\" onClick=\"goHome()\"></p>");
        }
        out.println("</form>");
//        out.println("<p>&nbsp;</p>");
    } //end printBody
}


