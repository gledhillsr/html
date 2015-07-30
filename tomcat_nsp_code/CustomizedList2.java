import java.io.*;
import java.text.*;
import java.util.*;
import javax.servlet.*;
import javax.servlet.http.*;
import java.lang.*;
import java.sql.*;


public class CustomizedList2 extends HttpServlet {

	boolean debug = false;	//-----------

    PrintWriter out;
    String szMyID;
    boolean isDirector = false;
    String ePatrollerList = "";
    private String resort;
    private DirectorSettings ds;
    Vector classificationsToDisplay = null;
    int commitmentToDisplay = 0;
    boolean listDirector = false;
    boolean listAll = false;
    int instructorFlags = 0;
    PatrolData patrol = null;
    int totalCount = 0;
	int actualCount = 0;
    int textFontSize = 14;
    Hashtable hash;
    Vector members;
    int maxShiftCount;
    int StartDay;
    int StartMonth;
    int StartYear;
    int EndDay;
    int EndMonth;
    int EndYear;
    boolean useMinDays;
    int MinDays;

    boolean showClass;
    boolean showID;
    boolean showBlank;
    boolean showBlankWide;
    boolean firstNameFirst;
    boolean showSpouse;
    boolean showAddr;
    boolean showCity;
    boolean showState;
    boolean showZip;
    boolean showHome;
    boolean showWork;
    boolean showCell;
    boolean showPager;
    boolean showEmail;
    boolean showEmergency;
    boolean showSubsitute;
    boolean showCommit;
    boolean showInstructor;
    boolean showDirector;
    boolean showLastUpdated;
    boolean showComments;
//    boolean showOldCredits;
    boolean showCreditDate;
    boolean showNightCnt;
    boolean showDayCnt;
    boolean showSwingCnt;
    boolean showTrainingCnt;
    boolean showNightList;
    boolean showDayList;
    boolean showSwingList;
    boolean showTrainingList;
    boolean showTeamLead;
    boolean showMentoring;
    boolean showCreditsEarned;
//    boolean showCreditsUsed;
    boolean showCanEarnCredits;
    String Sort1;
    String Sort2;
    String Sort3;

    public void doGet(HttpServletRequest request, HttpServletResponse response)
        throws IOException, ServletException
    {
        response.setContentType("text/html");
synchronized (this) {
        out = response.getWriter();
        ds = null;
        CookieID cookie = new CookieID(request,response,"CustomizedList2",null);
        resort = request.getParameter("resort");

        szMyID = cookie.getID();
        if (szMyID != null)
            readData(request, szMyID);

		actualCount = 0;

        printTop();
        if(PatrolData.validResort(resort))
            printBody();
        else
            out.println("Invalid host resort.");
        printBottom();
} //END SYNCRONIZED
    }

    public void doPost(HttpServletRequest request,
                      HttpServletResponse response)
        throws IOException, ServletException
    {
        doGet(request, response);
    }

    public void readData(HttpServletRequest request,String IDOfEditor) {
//System.out.println("readData");
    firstNameFirst  = true;
    String szName = request.getParameter("NAME");
    if(szName != null && szName.equals("LAST"))
        firstNameFirst = false;
    showClass       = request.getParameter("CLASS") != null;
    showID          = request.getParameter("SHOW_ID") != null;
    showBlank       = request.getParameter("SHOW_BLANK") != null;
    showBlankWide   = request.getParameter("SHOW_BLANK2") != null;
    showSpouse      = request.getParameter("SPOUSE") != null;
    showAddr        = request.getParameter("ADDR") != null;
    showCity        = request.getParameter("CITY") != null;
    showState       = request.getParameter("STATE") != null;
    showZip         = request.getParameter("ZIP") != null;
    showHome        = request.getParameter("HOME") != null;
    showWork        = request.getParameter("WORK") != null;
    showCell        = request.getParameter("CELL") != null;
    showPager       = request.getParameter("PAGER") != null;
    showEmail       = request.getParameter("EMAIL") != null;
    showEmergency   = request.getParameter("EMERGENCY") != null;
    showSubsitute   = request.getParameter("SUBSITUTE") != null;
    showCommit      = request.getParameter("COMMIT") != null;
    showInstructor  = request.getParameter("INSTRUCTOR") != null;
    showDirector    = request.getParameter("DIRECTOR") != null;
    showLastUpdated = request.getParameter("LAST_UPDATED") != null;
    showComments    = request.getParameter("COMMENTS") != null;
//    showOldCredits    = request.getParameter("CARRY_OVER_CREDITS") != null;
    showCreditDate = request.getParameter("LAST_CREDIT_UPDATE") != null;
    showNightCnt    = request.getParameter("NIGHT_CNT") != null;
    showDayCnt      = request.getParameter("DAY_CNT") != null;
    showSwingCnt      = request.getParameter("SWING_CNT") != null;
    showTrainingCnt      = request.getParameter("TRAINING_CNT") != null;
    showNightList   = request.getParameter("NIGHT_DETAILS") != null;
    showSwingList   = request.getParameter("SWING_DETAILS") != null;
    showDayList     = request.getParameter("DAY_DETAILS") != null;
    showTrainingList     = request.getParameter("TRAINING_DETAILS") != null;
    showTeamLead        = request.getParameter("TEAM_LEAD") != null;
    showMentoring       = request.getParameter("MENTORING") != null;
    showCreditsEarned   = request.getParameter("CREDITS_EARNED") != null;
//    showCreditsUsed     = request.getParameter("CREDITS_USED") != null;
    showCanEarnCredits  = request.getParameter("CAN_EARN_CREDITS") != null;

    StartDay    = cvtToInt(request.getParameter("StartDay"));
    StartMonth  = cvtToInt(request.getParameter("StartMonth"));
    StartYear   = cvtToInt(request.getParameter("StartYear"));
    EndDay      = cvtToInt(request.getParameter("EndDay"));
    EndMonth    = cvtToInt(request.getParameter("EndMonth"));
    EndYear     = cvtToInt(request.getParameter("EndYear"));
    useMinDays  = request.getParameter("MIN_DAYS") != null;
    MinDays     = cvtToInt(request.getParameter("MinDays"));


    Sort1 = request.getParameter("FirstSort");
    Sort2 = request.getParameter("SecondSort");
    Sort3 = request.getParameter("ThirdSort");

    textFontSize = cvtToInt(request.getParameter("FontSize"));
if(debug) {
System.out.println("Sort1="+Sort1);
System.out.println("Sort2="+Sort2);
System.out.println("Sort3="+Sort3);

System.out.println("showClass="+showClass);
System.out.println("showID="+showID);
System.out.println("showBlank="+showBlank);
System.out.println("showBlankWide="+showBlankWide);
System.out.println("showSpouse="+showSpouse);
System.out.println("showAddr="+showAddr);
System.out.println("showCity="+showCity);
System.out.println("showState="+showState);
System.out.println("showZip="+showZip);
System.out.println("showHome="+showHome);
System.out.println("showWork="+showWork);
System.out.println("showCell="+showCell);
System.out.println("showPager="+showPager);
System.out.println("showEmail="+showEmail);
System.out.println("showEmergency="+showEmergency);
//System.out.println("showNight="+showNight);
System.out.println("showCommit="+showCommit);
System.out.println("showInstructor="+showInstructor);
System.out.println("showDirector="+showDirector);
System.out.println("showLastUpdated="+showLastUpdated);
System.out.println("showComments="+showComments);
//System.out.println("showOldCredits="+showOldCredits);
System.out.println("showCreditDate="+showCreditDate);
System.out.println("showNightCnt="+showNightCnt);
System.out.println("showDayCnt="+showDayCnt);
System.out.println("showSwingCnt="+showSwingCnt);
System.out.println("showTrainingCnt="+showTrainingCnt);
System.out.println("showNightList="+showNightList);
System.out.println("showDayList="+showDayList);
System.out.println("showSwingList="+showSwingList);
System.out.println("showTrainingList="+showTrainingList);
System.out.println("useMinDays="+useMinDays);
System.out.println("MinDays="+MinDays);
}
    String[] incList= {"BAS","INA","SR","SRA","ALM","PRO","AUX","TRA","CAN","OTH"};
    classificationsToDisplay = new Vector();
    commitmentToDisplay = 0;
//classification
    for(int i=0; i < incList.length; ++i) {
        String str = request.getParameter(incList[i]);
        if(str != null) {
            classificationsToDisplay.add(incList[i]);
//System.out.println(i+") "+incList[i]+" found");
        } else {
//System.out.println(i+") "+incList[i]+" skipped");
        }
    }
//commitment
    if( request.getParameter("FullTime") != null)   commitmentToDisplay += 4;
    if( request.getParameter("PartTime") != null)   commitmentToDisplay += 2;
    if( request.getParameter("Inactive") != null)   commitmentToDisplay += 1;
//System.out.println("commitment= "+commitmentToDisplay);

//instructor/director flags
    listDirector = false;
    listAll = false;
    instructorFlags = 0;
    if( request.getParameter("ALL") != null)        listAll = true;
    if( request.getParameter("ListDirector") != null)   listDirector = true;
    if( request.getParameter("OEC") != null)        instructorFlags += 1;
    if( request.getParameter("CPR") != null)        instructorFlags += 2;
    if( request.getParameter("Ski") != null)        instructorFlags += 4;
    if( request.getParameter("Toboggan") != null)   instructorFlags += 8;
//System.out.println("listAll= "+listAll);
//System.out.println("listDirector= "+listDirector);
//System.out.println("instructorFlags= "+instructorFlags);

    patrol = new PatrolData(PatrolData.FETCH_ALL_DATA,resort);

	//read assignments within a range and get shift count
    readAssignments(patrol); //must read for other code to work


    ds = patrol.readDirectorSettings();
    String sortString = getSortString();
//System.out.println("sortString="+sortString);
    patrol.resetRoster(sortString);
    ePatrollerList = "";
    MemberData member = patrol.nextMember("&nbsp;");
//      MemberData member = patrol.nextMember("");
//int xx=0;
    while(member != null) {
//System.out.println(++xx);
        if(member.okToDisplay(false, false, listAll, classificationsToDisplay, commitmentToDisplay, listDirector, instructorFlags, 0)) {
            String em = member.getEmail();
            //check for valid email
            if( em != null && em.length() > 6 && em.indexOf('@') > 0 && em.indexOf('.') > 0) {
                if(ePatrollerList.length() > 2)
                    ePatrollerList += ",";
                ePatrollerList += em;
            }
        }
//else System.out.println("NOT OK to display "+member);
        member = patrol.nextMember("");
    }
//System.out.println("length of email string = "+ePatrollerList.length());
    MemberData editor = patrol.getMemberByID(IDOfEditor); //ID from cookie
//      patrol.close(); //must close connection!
    if(editor != null)
        isDirector=editor.isDirector();
    else
        isDirector = false;
    }

    /************/
    /* printTop */
    /************/
    public void printTop() {

        int headerFontSize = textFontSize+2; //adjust me ?

        out.println("<html><head>");
        out.println("<meta http-equiv=\"Content-Language\" content=\"en-us\">");
        out.println("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1252\">");
        out.println("<title>"+PatrolData.getResortFullName(resort)+" Ski Patrollers</title>");
        out.println("<style type=\"text/css\">");
        out.println("<!-- ");
        out.println("body  {font-size:10; color: #000000; background-color: #ffffff}");
        out.println("table {border-width:1px; border-color:#000000; border-style:solid; border-collapse:collapse; border-spacing:0}");
        out.println("th    {font-size:"+textFontSize+"; font-weight: bold; color: #000000; background-color: #ffffff; border-width:1px; border-color:#000000; border-style:solid; padding:2px}");
        out.println("td    {font-size:"+textFontSize+"; color: #000000; background-color: #ffffff; border-width:1px; border-color:#000000; border-style:solid; padding:1px}");
        out.println("//-->");
        out.println("</style>");
        out.println("<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">");
        out.println("<META HTTP-EQUIV=\"Expires\" CONTENT=\"-1\">");
        out.println("</head><body>");

        out.println("<script>");
        out.println("function printWindow(){");
        out.println("   bV = parseInt(navigator.appVersion)");
        out.println("   if (bV >= 4) window.print()");
        out.println("}");
        out.println("</script>");

        out.println("<p><Center><h2>Members of the "+PatrolData.getResortFullName(resort)+" Ski Patrol</h2></Center></p>");

        if(isDirector || (ds != null && ds.getEmailAll())) {
            out.println("<p><font size=\"3\"><Bold>");
            String options="";
//classification
            for(int j=0; j < classificationsToDisplay.size(); ++j)
                options += "&" + classificationsToDisplay.elementAt(j) + "=1";

//commitment
            if( (commitmentToDisplay & 4) == 4) options+="&FullTime=1";
            if( (commitmentToDisplay & 2) == 2) options+="&PartTime=1";
            if( (commitmentToDisplay & 1) == 1) options+="&Inactive=1";
//Instructor
            if(listAll)                     options += "&ALL=1";
            if(listDirector)                options += "&ListDirector=1";
            if((instructorFlags & 1) == 1)  options += "&OEC=1";
            if((instructorFlags & 2) == 2)  options += "&CPR=1";
            if((instructorFlags & 4) == 4)  options += "&Ski=1";
            if((instructorFlags & 8) == 8)  options += "&Toboggan=1";
// day count (1/0), swing count (1/0), night count (1/0), Minimum Shifts (#)
//fix me ????
			if (useMinDays && (showDayCnt || showSwingCnt || showNightCnt)) {
			    options += "&MIN_DAYS=1&MinDays="+MinDays;
				if(showDayCnt) 			options += "&DAY_CNT=1";
				if(showNightCnt)   options += "&NIGHT_CNT=1";
				if(showSwingCnt)   options += "&SWING_CNT=1";
				if(showTrainingCnt) options += "&TRAINING_CNT=1";
				options += "&StartDay="+StartDay;
				options += "&StartMonth="+StartMonth;
				options += "&StartYear="+StartYear;
				options += "&EndDay="+EndDay;
				options += "&EndMonth="+EndMonth;
				options += "&EndYear="+EndYear;

			}
            String loc = "EmailForm?resort="+resort+"&ID="+ szMyID + options;
            out.println("<INPUT TYPE=\"button\" VALUE=\"e-mail THESE patrollers\" onClick=window.location=\""+loc+"\">");
            out.println("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");

            out.println("<a href=\"javascript:printWindow()\">Print This Page</a></font>");

            out.println("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
options += "&format=Excel";
//options += "&format=palm";
			
loc = "download?resort="+resort+"&ID="+ szMyID + options;

            out.println("<INPUT  TYPE=\"button\" VALUE=\"Download this table (under construction)\" onClick=window.location=\""+loc+"\"></p>");
        } //end email patrollers...
        out.println("<table  style=\"font-size: 10pt; face=\'Verdana, Arial, Helvetica\' \" border=\"1\" width=\"99%\" bordercolordark=\"#003366\" bordercolorlight=\"#C0C0C0\">");
        out.println(" <tr>");

        MemberData.addColumn(-1);
        MemberData.addColumn(firstNameFirst ? MemberData.FIRST : MemberData.LAST);
        if(showBlank)       MemberData.addColumn(MemberData.BLANK);
        if(showBlankWide)   MemberData.addColumn(MemberData.BLANK_WIDE);
        if(showClass)       MemberData.addColumn(MemberData.CLASSIFICATION);
        if(showID)          MemberData.addColumn(MemberData.ID_NUM);
        if(showSpouse)      MemberData.addColumn(MemberData.SPOUSE);
        if(showAddr)        MemberData.addColumn(MemberData.ADDRESS);
        if(showCity)        MemberData.addColumn(MemberData.CITY);
        if(showState)       MemberData.addColumn(MemberData.STATE);
        if(showZip)         MemberData.addColumn(MemberData.ZIPCODE);
        if(showHome)        MemberData.addColumn(MemberData.HOMEPHONE);
        if(showWork)        MemberData.addColumn(MemberData.WORKPHONE);
        if(showCell)        MemberData.addColumn(MemberData.CELLPHONE);
        if(showPager)       MemberData.addColumn(MemberData.PAGER);
        if(showEmail)       MemberData.addColumn(MemberData.EMAIL);
        if(showEmergency)   MemberData.addColumn(MemberData.EMERGENCY);
        if(showSubsitute)   MemberData.addColumn(MemberData.SUB);
        if(showCommit)      MemberData.addColumn(MemberData.COMMITMENT);
        if(showInstructor)  MemberData.addColumn(MemberData.INSTRUCTOR);
        if(showDirector)    MemberData.addColumn(MemberData.DIRECTOR);
        if(showLastUpdated) MemberData.addColumn(MemberData.LAST_UPDATED);
        if(showComments)    MemberData.addColumn(MemberData.COMMENTS);
        if(showCanEarnCredits)  MemberData.addColumn(MemberData.CAN_EARN_CREDITS);
//        if(showOldCredits)  MemberData.addColumn(MemberData.CARRY_OVER_CREDITS);
        if(showCreditsEarned)   MemberData.addColumn(MemberData.CREDITS_EARNED);
//        if(showCreditsUsed)     MemberData.addColumn(MemberData.CREDITS_USED);
        if(showCreditDate)  MemberData.addColumn(MemberData.LAST_CREDIT_UPDATE);
        if(showTeamLead)        MemberData.addColumn(MemberData.TEAM_LEAD);
        if(showMentoring)       MemberData.addColumn(MemberData.MENTORING);

        if(showDayCnt)      MemberData.addColumn(MemberData.SHOW_DAY_CNT);
        if(showDayList)     MemberData.addColumn(MemberData.SHOW_DAY_LIST);
        if(showSwingCnt)    MemberData.addColumn(MemberData.SHOW_SWING_CNT);
        if(showSwingList)   MemberData.addColumn(MemberData.SHOW_SWING_LIST);
        if(showNightCnt)    MemberData.addColumn(MemberData.SHOW_NIGHT_CNT);
        if(showNightList)   MemberData.addColumn(MemberData.SHOW_NIGHT_LIST);
        if(showTrainingCnt)  MemberData.addColumn(MemberData.SHOW_TRAINING_CNT);
        if(showTrainingList) MemberData.addColumn(MemberData.SHOW_TRAINING_LIST);

        MemberData.printMemberListRowHeading(out, resort);
        out.println(" </tr>");
    }

    /***************/
    /* printBottom */
    /**************/
    private void printBottom() {
        out.println("</table>");
        out.println("Total Patrollers Listed="+actualCount);

        out.println("<br>As of: "+new java.util.Date());
        out.println("</body></html>");
    }

    /******************/
    /* getServletInfo */
    /*****************/
    public String getServletInfo() {
    return "Create a list of all patrollers";
    }

    /*****************/
    /* getSortString */
    /****************/
    public String getSortString() {
    String sortString = "";
    if(Sort1.equals("Name") || Sort1.equals("shiftCnt"))
        sortString = firstNameFirst ? "FirstName,LastName" : "LastName,FirstName";
    else if(Sort1.equals("Class"))
        sortString = "ClassificationCode";
    else if(Sort1.equals("Comm"))
        sortString = "Commitment";
    else if(Sort1.equals("Updt"))
        sortString = "lastUpdated";
    else
        sortString = "FirstName,LastName";  //should not get hit

    if(Sort2.equals("Name") && !Sort1.equals("shiftCnt"))
        sortString += firstNameFirst ? ",FirstName,LastName" : ",LastName,FirstName";
    else if(Sort2.equals("Class"))
        sortString += ",ClassificationCode";
    else if(Sort2.equals("Comm"))
        sortString += ",Commitment";
//  else if(Sort1.equals("shiftCnt"))
//      sortString += "";
//  else if(Sort1.equals("DCnt"))
//      sortString += "";
    else if(Sort2.equals("Updt"))
        sortString += ",lastUpdated";

    if(Sort3.equals("Name"))
        sortString += firstNameFirst ? ",FirstName,LastName" : ",LastName,FirstName";
    else if(Sort3.equals("Class"))
        sortString += ",ClassificationCode";
    else if(Sort3.equals("Comm"))
        sortString += ",Commitment";
//  else if(Sort1.equals("shiftCnt"))
//      sortString += "";
//  else if(Sort1.equals("DCnt"))
//      sortString += "";
    else if(Sort3.equals("Updt"))
        sortString += ",lastUpdated";
//System.out.println("sortString="+sortString);
    return sortString;
    }

    /*************/
    /* printBody */
    /************/
    public void printBody() {
    String sortString = getSortString();
//  patrol.resetRoster(sortString);

//  MemberData member = patrol.nextMember("&nbsp;");
    MemberData member;
//System.out.println("printBody, sort1="+Sort1);
//System.out.println("sortString="+sortString);
//System.out.println("members.size()="+members.size());
    if(Sort1.equals("shiftCnt")) {
        if(useMinDays)
            maxShiftCount = MinDays-1;
        for(int i = maxShiftCount; i >= 0; --i) {	//loop from highest shift total back to 0
            for(totalCount = 0; totalCount < members.size(); totalCount++) { //loop through all members
                member = (MemberData)members.elementAt(totalCount);
				int totalAssignments = member.AssignmentCount[Assignments.DAY_TYPE] +
										member.AssignmentCount[Assignments.SWING_TYPE] +
										member.AssignmentCount[Assignments.TRAINING_TYPE] +
										member.AssignmentCount[Assignments.NIGHT_TYPE];
                if(totalAssignments == i) {
                    member.printMemberListRowData(out);
					actualCount++;	
                }
            }
        }
    } else {
        totalCount = 0;
        while(totalCount < members.size()) {
                member = (MemberData)members.elementAt(totalCount);
				int totalAssignments = member.AssignmentCount[Assignments.DAY_TYPE] +
										member.AssignmentCount[Assignments.SWING_TYPE] +
										member.AssignmentCount[Assignments.TRAINING_TYPE] +
										member.AssignmentCount[Assignments.NIGHT_TYPE];
                if(!useMinDays || totalAssignments < MinDays) {
                    member.printMemberListRowData(out);
	                actualCount++;
				}
                totalCount++;
        }
    }
    patrol.close(); //must close connection!
    } //end printBody


    /*******************/
    /* readAssignments */
    /*******************/
    public void readAssignments(PatrolData patrol) {
        Assignments ns;
        int i;
        String sortString = getSortString();
//System.out.println("readAssignments-sortString="+sortString);
        patrol.resetRoster(sortString);
//      patrol.resetRoster();
        MemberData member;

        maxShiftCount = 0;
        members = new Vector(PatrolData.MAX_PATROLLERS);
        hash = new Hashtable();
//int xx = 0;
        while((member = patrol.nextMember("&nbsp;")) != null) {
//System.out.println(++xx);
            if(member.okToDisplay(false, false, listAll, classificationsToDisplay, commitmentToDisplay, listDirector, instructorFlags, 0)) {
//              ++count;
                members.addElement(member);
                hash.put(member.getID() ,member);
            }
//else System.out.println("NOT ok to display "+member);
        }

        patrol.resetAssignments();
//        SimpleDateFormat normalDateFormatter = new SimpleDateFormat ("MM'/'dd'/'yyyy");
//System.out.println("StartYear="+StartYear+", StartMonth="+StartMonth+", StartDay="+StartDay);
//System.out.println("EndYear="+EndYear+", EndMonth="+EndMonth+", StartDay="+StartDay);
        GregorianCalendar date = new GregorianCalendar(StartYear,StartMonth,StartDay);
        long startMillis = 0;
        long endMillis = 99999999999999L;
        long currMillis;
        if(date != null && StartYear != 0)
            startMillis = date.getTimeInMillis();
        date = new GregorianCalendar(EndYear,EndMonth,EndDay);
        if(date != null && EndYear != 0)
            endMillis = date.getTimeInMillis();

        while((ns = patrol.readNextAssignment()) != null) {
            date = new GregorianCalendar(ns.getYear(),ns.getMonth(),ns.getDay());
            if(date != null)
                currMillis = date.getTimeInMillis();
            else
                currMillis = startMillis+1;
//System.out.print("start="+startMillis+"end="+endMillis+" curr="+currMillis+" "+ns.getYear()+" "+ns.getMonth()+" "+ns.getDay());
            if(startMillis <= currMillis && currMillis <= endMillis) {
//System.out.println(" ok");
                for(i =0; i < Assignments.MAX   ; ++i) {
        //              member = patrol.getMemberByID(ns.getPosID(i));
                    member = (MemberData)hash.get(ns.getPosID(i));
        //System.out.print(ns.getPosID(i) + " ");
                    if(member != null && member.okToDisplay(false, false, listAll, classificationsToDisplay,commitmentToDisplay, listDirector, instructorFlags, 0)) {
        //System.out.print("(y, ");
                        String tim = ns.getStartingTimeString();
                        if(showDayCnt && ns.isDayShift()) {
                            ++member.AssignmentCount[Assignments.DAY_TYPE];
                            if(maxShiftCount < member.AssignmentCount[Assignments.DAY_TYPE])
                                maxShiftCount = member.AssignmentCount[Assignments.DAY_TYPE];
                             member.szAssignments[Assignments.DAY_TYPE] += ns.getMyFormattedDate() + " ";
                        } 
                        if(showSwingCnt && ns.isSwingShift()) {
                            ++member.AssignmentCount[Assignments.SWING_TYPE];
                            if(maxShiftCount < member.AssignmentCount[Assignments.SWING_TYPE])
                                maxShiftCount = member.AssignmentCount[Assignments.SWING_TYPE];
                             member.szAssignments[Assignments.SWING_TYPE] += ns.getMyFormattedDate() + " ";
                        }
                        if(showNightCnt && ns.isNightShift()) {
                            ++member.AssignmentCount[Assignments.NIGHT_TYPE];
                            if(maxShiftCount < member.AssignmentCount[Assignments.NIGHT_TYPE])
                                maxShiftCount = member.AssignmentCount[Assignments.NIGHT_TYPE];
                             member.szAssignments[Assignments.NIGHT_TYPE] += ns.getMyFormattedDate() + " ";
                        }
                        if(showTrainingCnt && ns.isTrainingShift()) {
                            ++member.AssignmentCount[Assignments.TRAINING_TYPE];
                            if(maxShiftCount < member.AssignmentCount[Assignments.TRAINING_TYPE])
                                maxShiftCount = member.AssignmentCount[Assignments.TRAINING_TYPE];
                             member.szAssignments[Assignments.TRAINING_TYPE] += ns.getMyFormattedDate() + " ";
                        }
//zzzz
                    } //end if okToDisplay
                } //end for loop for shift
            } else { //end test for date
//System.out.println(" Skipped");
            }
//System.out.println();
        } //end while loop (all assignments)
    }

    int cvtToInt(String strNum) {
    int num = 0;
    try {
        if(strNum != null)
            num = Integer.parseInt(strNum);
    } catch (Exception e) {
    }
    return num;
    }
}


