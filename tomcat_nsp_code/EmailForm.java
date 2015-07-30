/*
 * reminder.java
 *
 * Created on January 29, 2003, 6:52 AM
 */

/**
 *
 * @author  Steve Gledhill
 */
import java.util.*;
import java.lang.*;
import java.io.*;
import java.text.DateFormat;
import java.net.*;
import java.util.*;
import java.lang.*;
import javax.servlet.*;
import javax.servlet.http.*;
import java.sql.*;
//import javax.swing.UIManager;

public class EmailForm extends HttpServlet {

    Vector ePatrollerList;
    Vector invalidPatrollerList;

	boolean debug = false;				//-------
    boolean isDirector = false;
    private DirectorSettings ds;
    Vector classificationsToDisplay = null;
    int commitmentToDisplay = 0;
    boolean EveryBody;
    boolean SubList;
    boolean listDirector = false;
    boolean listAll = false;
    int instructorFlags = 0;
    PatrolData patrol = null;
    int totalCount = 0;
    int textFontSize = 14;
    Hashtable hash;
    Vector members;
    String szMyID = null;
    PrintWriter out;
    String resort;
    boolean showDayCnt;
    boolean showSwingCnt;
    boolean showNightCnt;
    boolean showTrainingCnt;
    boolean showDayList;
    boolean showSwingList;
    boolean showNightList;
    boolean showTrainingList;
//    int maxShiftCount;

    int StartDay;
    int StartMonth;
    int StartYear;
    int EndDay;
    int EndMonth;
    int EndYear;
    boolean useMinDays;
    int MinDays;

//------
// doGet
//------
    public void doGet(HttpServletRequest request,
                      HttpServletResponse response)
        throws IOException, ServletException
    {

        synchronized (this) {
            response.setContentType("text/html");
            out = response.getWriter();

            if(debug) 
				System.out.println("Entering EmailForm...");

            CookieID cookie = new CookieID(request,response,"EmailForm",null);
            szMyID = cookie.getID();
            resort = request.getParameter("resort");
            if (szMyID != null) {
                readData(request);
                BuildLists(szMyID);
            }

            String Submit = request.getParameter("Submit");

            printTop(out, response, Submit);
            if(PatrolData.validResort(resort)) {
                if(Submit != null) {
System.out.println("resort "+resort+", sending emails");				
                    SendEmails(request,szMyID);
                    //SLOW OPERATION HERE
                } else {
                    printMiddle(out,resort,szMyID);
                }
            } else {
                out.println("Invalid host resort.");
            }
            printBottom(out);
        }
    }

//-------
// doPost
//-------
    public void doPost(HttpServletRequest request,
                      HttpServletResponse response)
        throws IOException, ServletException
    {
        doGet(request, response);
    }

//---------
// SendEmails
//---------
    public void SendEmails(HttpServletRequest request, String szMyID) {

        int max = cvtToInt(request.getParameter("patrollerCount"));
        String[] listName = request.getParameterValues("Patrollers");
        if(listName == null) {
            out.println("Error, No names selected<br>");
            out.println("<form><p>");
            out.println("<input type=\"button\" value=\"Try Again\" onClick=\"history.back()\">");
            out.println("</p>");
            out.println("</form>");
            return;
        }
        int max2 = listName.length;
        MemberData member;
        MemberData fromMember;
        fromMember = patrol.getMemberByID(szMyID);
        String subject = request.getParameter("subject");
        String message = request.getParameter("message");
//replace of word processing characters for '(146), "(147), and "(148) to normal characters
//for some weird reason, the String.replace() command did not work for char's > 127
char[] foo= message.toCharArray();
int j;
for(j=0; j < message.length(); ++j) {
  if(foo[j] < 128) continue;
  else if(foo[j] == 146) foo[j] = '\'';
  else if(foo[j] == 147) foo[j] = '"';
  else if(foo[j] == 148) foo[j] = '"';
 }
message = new String(foo);

        if(subject.length() < 2) {
            out.println("Error, Subject required<br>");
            out.println("<form><p>");
            out.println("<input type=\"button\" value=\"Try Again\" onClick=\"history.back()\">");
            out.println("</p>");
            out.println("</form>");
            return;
        }
        if(message.length() < 4) {
            out.println("Error, message must be at least 4 characters long<br>");
            out.println("<form><p>");
            out.println("<input type=\"button\" value=\"Try Again\" onClick=\"history.back()\">");
            out.println("</p>");
            out.println("</form>");
            return;
        }
        out.println("sending emails to " + max2 + " out of "+max+" patrollers who had valid email addresses. <br><br>");
        String fromEmail=fromMember.getEmail();
        boolean isValidReturn = true;
        if( fromEmail == null || fromEmail.length() <= 6 || fromEmail.indexOf('@') <= 0 || fromEmail.indexOf('.') <= 0) {
            fromEmail="Steve@Gledhills.com";
            isValidReturn = false;
        }
        out.println("from="+fromMember.getFullName()+ " &lt;<b>" + fromEmail + "&gt;</b><br><br>");
        out.println("Subject="+subject+"<br>");
        out.println("Message="+message+"<br><br>");

        String smtp="mail.gledhills.com";
        String from="steve@gledhills.com" ;

        if(isValidReturn)
            from = fromEmail;

        MailMan mail= new MailMan(smtp,from, fromMember.getFullName() );
        String newMessage = message;
        boolean subst = (message.indexOf("$pass$") != -1 ||
                         message.indexOf("$last$") != -1 ||
                         message.indexOf("$first$") != -1 ||
                         message.indexOf("$id$") != -1 ||
                         message.indexOf("$carryovercredits$") != -1 ||
						 message.indexOf("$credits$") != -1);

        //loop for each patroller
        for(int i=0; i < max2; ++i) {
            String id = listName[i];
            member = patrol.getMemberByID(id);
            String name = member.getFullName();
//System.out.println(i+") "+name);
            String email= member.getEmail();
            out.println((i+1) + ") Mailing: " + name + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;at&nbsp;&nbsp;" + email + "<br>");
	        newMessage = message;
            if(subst) {
                String pass = member.getPassword().trim();
                //if no password, then their last name is the password
                if(pass.equals(""))
                    pass = member.getLast();
                newMessage = message.replaceAll("\\$pass\\$", pass);
                newMessage = newMessage.replaceAll("\\$last\\$", member.getLast());
                newMessage = newMessage.replaceAll("\\$first\\$", member.getFirst());
                newMessage = newMessage.replaceAll("\\$id\\$", id);
                newMessage = newMessage.replaceAll("\\$carryovercredits\\$", member.getCarryOverCredits());
                newMessage = newMessage.replaceAll("\\$credits\\$", member.getCreditsEarned());
                newMessage = newMessage.replaceAll("\\$credit\\$", member.getCreditsEarned());
            }
//new message footer
String fullPatrolName = PatrolData.getResortFullName(resort);
			newMessage += "\n\n" +
"----------------------------------------------\n" +
"This message sent by " + fromMember.getFirst() + " " + fromMember.getLast() + "\n" +
"from " + fullPatrolName + "'s online scheduling web site.\n" +
"----------------------------------------------\n";
            if(debug) { //added for debugging
                out.println("hack, no mail being sent, message body is:<br>");
                out.println(newMessage+"<br>");
            } else if(mail == null) {
                System.out.println("Error: creating MailMan(\""+smtp+"\", \""+from+"\")");
            } else {
                if(!isValidReturn) {
                    newMessage +=  "\n\n\n--------------------------------------------------------\n" +
                                      "Please Don't respond to this email.  SEND any responses\n" +
                                      "to: ' " + from + " ' (that is " + name + "'s email address)\n\n" +
                                      "This was sent from the Ski Patrol Web Site Auto Mailer.\n" +
                                      "--------------------------------------------------------\n";
                }
                mailto(mail, member,subject,newMessage);
            }
        }
        out.println("<br>Done.<br>");
    }

//-------------------------
// mailto
//-------------------------
    private void mailto(MailMan mail, MemberData mbr,String subject, String message) {
//if(mbr != null) return;   //hack
        if(mbr == null)
            return;
        String recipient = mbr.getEmail();
        if(recipient != null && recipient.length() > 3 && recipient.indexOf('@') > 0) {
System.out.print("Sending mail to "+mbr.getFullName()+ " at "+recipient);   //no e-mail, JUST LOG IT
            try {
                mail.sendMessage(subject, message, recipient);
System.out.println("  mail was sucessfull");    //no e-mail, JUST LOG IT
            } catch ( MailManException ex) {
                System.out.println("  error "+ex);
                System.out.println("attempting to send mail to: "+recipient);   //no e-mail, JUST LOG IT
            }
        }
    } //end mailto


//---------
// printTop
//---------
    public void printTop(PrintWriter out, HttpServletResponse response, String Submit) {
//      if(Submit != null) {
//        response.setHeader("Expires", "Sat, 6 May 1995 12:00:00 GMT");        // Set to expire far in the past.
//        response.setHeader("Cache-Control", "no-store, no-cache, must-revalidate");// Set standard HTTP/1.1 no-cache headers.
//        response.addHeader("Cache-Control", "post-check=0, pre-check=0"); // Set IE extended HTTP/1.1 no-cache headers (use addHeader).
//        response.setHeader("Pragma", "no-cache");                         // Set standard HTTP/1.0 no-cache header.
//      }

        out.println("<html>");
        out.println("<HEAD>");
        out.println("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1252\">");
//      if(Submit != null) {
//          out.println("<meta http-equiv=\"refresh\" content=\"2\"> ");
//      }
        out.println("<title>Email Selected Patrollers</title>");
        out.println("<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">");
        out.println("<META HTTP-EQUIV=\"Expires\" CONTENT=\"-1\">");
        out.println("</HEAD>");
        out.println("<body>");
		if(Submit != null)
			out.println("<H2>Sending Emails, this may take a while.</H2>");
		else
			out.println("<H2>Prepare Emails.</H2>");
    }
//---------
// printBottom
//---------
    public void printBottom(PrintWriter out) {
        out.println("</body>");
        out.println("</html>");
    }

//---------
// printMiddle
//---------
    public void printMiddle(PrintWriter out, String resort, String szMyID) {
        out.println("<form name=\"form\" method=\"post\" action=\""+PatrolData.SERVLET_URL+"EmailForm\">");
        out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"resort\" VALUE=\""+resort+"\">");
        out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"ID\" VALUE=\""+szMyID+"\">");
//      out.println("<form action=\""+PatrolData.SERVLET_URL+"UpdateInfo\" method=POST id=form02 name=form02>");
        MemberData currentMember = patrol.getMemberByID(szMyID);
        String szName = "Invalid";
        if(currentMember != null) {
            szName = currentMember.getFullName();
            String em = currentMember.getEmail();
            //check for valid email
            if( em != null && em.length() > 6 && em.indexOf('@') > 0 && em.indexOf('.') > 0) {
                szName += " &lt;" + em + "&gt;";
            }


        }
        out.println("<p>From: <input type=\"text\" name=\"from\" size=\"40\" value=\"" + szName + "\" readonly></p>");

        int i;
        int max = ePatrollerList.size();
        out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"patrollerCount\" VALUE=\""+max+"\">");
        out.println("<Table>");
        out.println("<td valign=center>To:</td>");
        out.println("<td width=460>");
        out.println("Send Emails to selected (hilighted) patrollers.<br>");
        out.println("Use <b>&lt;CTRL&gt; Click</b>, to UNSELECT specific members.<br>");
        out.println("   <select multiple size=\"6\" name=\"Patrollers\" readonly>");
        MemberData member;

        for(i=0; i < max; ++i) {
            String id = (String)ePatrollerList.elementAt(i);
            member = patrol.getMemberByID(id);
            String name = member.getFullName2();
            out.println("   <option selected value="+id+">" + name + "</option>");

        }
        out.println("   </select>");
        out.println("<br>" + max + " Email's to send");
        out.println("</tr>  ");
        out.println("<td width=250>");
        out.println("   Patrollers with No Valid Email Address<br>");
        out.println("   <select size=\"5\" name=\"Patrollers2\" readonly>");
        max = invalidPatrollerList.size();
        for(i=0; i < max; ++i) {
            String id = (String)invalidPatrollerList.elementAt(i);
            member = patrol.getMemberByID(id);
            String name = member.getFullName2();
            out.println("   <option value="+id+">" + name + "</option>");
        }
        out.println("   </select>");
        out.println("<br>" + max + " Email's SKIPPED");
        out.println("</tr>  ");
        out.println("</table>");
        out.println("<br>");
        out.println("Subject: <input type=\"text\" name=\"subject\" size=60\"> (Required)<br>");
        out.println("<p><table><tr>");
        out.println("  <td valign=top>Message: <br>(Required)</td>");
        out.println("  <td valign=top width=100%><textarea  style=\"width: 100%;\" name=\"message\" rows=\"10\"></textarea></td>");
        out.println("</tr></table>");

        out.println("<p>");
        out.println("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
        if(resort.equals("Sample"))
            out.println("<input type=\"button\" value=\"Disabled in Demo\" onClick=\"history.back()\">");
        else
            out.println("<input type=\"submit\" name=\"Submit\" value=\"Send Mail\">&nbsp;&nbsp;&nbsp;&nbsp;");
        out.println("<input type=\"button\" value=\"Cancel\" onClick=\"history.back()\">");
        out.println("</p>");
        out.println("</form>");
        out.println("<table border=0 cellpadding=0 cellspacing=0 style=\"border-collapse: collapse\" width=600>\n");
        out.println("  <tr>\n");
        out.println("    <td colspan=2>\n");
        out.println("    <p align=left><b><font size=4>Special replacement codes for email Message</b> (must be lower case ONLY)</font></td>\n");
        out.println("  </tr>\n");
        out.println("  <tr>\n");
        out.println("    <td align=right>$first$</td>\n");
        out.println("    <td>&nbsp;-- replace with patrollers First Name</td>\n");
        out.println("  </tr>\n");
        out.println("  <tr>\n");
        out.println("    <td align=right>$last$</td>\n");
        out.println("    <td width=354>&nbsp;-- replace with patrollers Last Name</td>\n");
        out.println("  </tr>\n");
        out.println("  <tr>\n");
        out.println("    <td align=right>$id$</td>\n");
        out.println("    <td>&nbsp;-- replace with patrollers ID number</td>\n");
        out.println("  </tr>\n");
        out.println("  <tr>\n");
        out.println("    <td  align=right>$pass$</td>\n");
        out.println("    <td >&nbsp;-- replace with patrollers password</td>\n");
        out.println("  </tr>\n");
        if(resort.equalsIgnoreCase("Brighton")) {
//            out.println("  <tr>\n");
//            out.println("    <td align=right>$carryovercredits$</td>\n");
//            out.println("    <td>&nbsp;-- replace with number of 'Carry Over' credits on file</td>\n");
//            out.println("  </tr>\n");
            out.println("  <tr>\n");
            out.println("    <td align=right>$credits$</td>\n");
            out.println("    <td>&nbsp;-- replace with number of credits available (as of last update)</td>\n");
            out.println("  </tr>\n");
        }
        out.println("</table>\n");
    }

    /***************/
    /* readData */
    /***************/
    public void readData(HttpServletRequest request) {
    // parameters are:
    //   classification: "BAS","INA","SR","SRA","ALM","PRO","AUX","TRA","CAN"
    //   commitment:     "fulltime", "PartTime", "Inactive"
    //   Instructor:     "ALL", ListDirector", "OEC" "CPR", "Ski", "Toboggan"

    String str = request.getParameter("EveryBody");
    if(str != null) {
        EveryBody = true;
        return;
    }
    EveryBody = false;

    str = request.getParameter("SubList");
    if(str != null) {
        SubList = true;
        return;
    }
    SubList = false;

    showDayCnt      = request.getParameter("DAY_CNT") != null;
    showSwingCnt      = request.getParameter("SWING_CNT") != null;
    showNightCnt    = request.getParameter("NIGHT_CNT") != null;
    showTrainingCnt    = request.getParameter("TRAINING_CNT") != null;

//day/swing/night details are not used here
    showDayList     = request.getParameter("DAY_DETAILS") != null;
    showSwingList     = request.getParameter("SWING_DETAILS") != null;
    showNightList   = request.getParameter("NIGHT_DETAILS") != null;
    showTrainingList   = request.getParameter("TRAINING_DETAILS") != null;

    StartDay    = cvtToInt(request.getParameter("StartDay"));
    StartMonth  = cvtToInt(request.getParameter("StartMonth"));
    StartYear   = cvtToInt(request.getParameter("StartYear"));
    EndDay      = cvtToInt(request.getParameter("EndDay"));
    EndMonth    = cvtToInt(request.getParameter("EndMonth"));
    EndYear     = cvtToInt(request.getParameter("EndYear"));
    useMinDays  = request.getParameter("MIN_DAYS") != null;
	if(useMinDays)
	    MinDays     = cvtToInt(request.getParameter("MinDays"));
	else
		MinDays = 0;	//no minimum
	if(debug) {
		System.out.println("getParameter(MinDays)="+request.getParameter("MinDays"));
		System.out.println("EveryBody="+EveryBody);
		System.out.println("SubList="+SubList);
		System.out.println("StartDay="+StartDay);
		System.out.println("StartMonth="+StartMonth);
		System.out.println("StartYear="+StartYear);
		System.out.println("EndDay="+EndDay);
		System.out.println("EndMonth="+EndMonth);
		System.out.println("EndYear="+EndYear);
		System.out.println("useMinDays="+useMinDays);
		System.out.println("MinDays="+MinDays);
		System.out.println("showDayCnt="+showDayCnt);
		System.out.println("showSwingCnt="+showSwingCnt);
		System.out.println("showTrainingCnt="+showTrainingCnt);
		System.out.println("showNightCnt="+showNightCnt);
	}

    String[] incList= {"BAS","INA","SR","SRA","ALM","PRO","AUX","TRA","CAN","OTH"};
    classificationsToDisplay = new Vector();
    commitmentToDisplay = 0;
//classification
    for(int i=0; i < incList.length; ++i) {
        str = request.getParameter(incList[i]);
        if(str != null) {
            classificationsToDisplay.add(incList[i]);
        }
    }
//commitment
    if( request.getParameter("FullTime") != null)   commitmentToDisplay += 4;
    if( request.getParameter("PartTime") != null)   commitmentToDisplay += 2;
    if( request.getParameter("Inactive") != null)   commitmentToDisplay += 1;
	if(debug)
		System.out.println("commitmentToDisplay= "+commitmentToDisplay);

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
	if(debug) {
		System.out.println("listAll= "+listAll);
		System.out.println("ListDirector= "+listDirector);
		System.out.println("instructorFlags= "+instructorFlags);
	}

    }

    /*******************/
    /* readAssignments */
    /*******************/
    public void readAssignments(PatrolData patrol) {
        Assignments ns;
        int i;
        patrol.resetRoster();
        MemberData member;

//        maxShiftCount = 0;
        members = new Vector(PatrolData.MAX_PATROLLERS);
        hash = new Hashtable();
if(debug) System.out.println("====== extering readAssignments ===========");
        while((member = patrol.nextMember("&nbsp;")) != null) {
//System.out.print(member);
            if(member.okToDisplay(EveryBody,SubList,listAll, classificationsToDisplay,commitmentToDisplay, listDirector, instructorFlags, 0)) {
//              ++count;
//System.out.println("ok");
                members.addElement(member);
                hash.put(member.getID() ,member);
            }
//else System.out.println("NOT ok to display "+member);
        }

        patrol.resetAssignments();
//        SimpleDateFormat normalDateFormatter = new SimpleDateFormat ("MM'/'dd'/'yyyy");
        GregorianCalendar date = new GregorianCalendar(StartYear,StartMonth,StartDay);
        long startMillis = 0;
        long endMillis = 99999999999999L;
        long currMillis;
        if(date != null)
            startMillis = date.getTimeInMillis();
        date = new GregorianCalendar(EndYear,EndMonth,EndDay);
        if(date != null)
            endMillis = date.getTimeInMillis();

		//loop through all assignments
if(debug) System.out.println("=====loop thru all assignments and get actual assignment info =====");
        while((ns = patrol.readNextAssignment()) != null) {
            date = new GregorianCalendar(ns.getYear(),ns.getMonth(),ns.getDay());
            if(date != null)
                currMillis = date.getTimeInMillis();
            else
                currMillis = startMillis+1;
if(debug) System.out.print("start="+startMillis+"end="+endMillis+" curr="+currMillis+" "+ns.getYear()+" "+ns.getMonth()+" "+ns.getDay());
            if(startMillis <= currMillis && currMillis <= endMillis) {
if(debug) System.out.println(" Assignment with date range");
				//loop thru individual assignments on this day
                for(i =0; i < Assignments.MAX   ; ++i) {
        //              member = patrol.getMemberByID(ns.getPosID(i));
                    member = (MemberData)hash.get(ns.getPosID(i));
if(debug) System.out.print(ns.getPosID(i) + " ");
                    if(member != null && member.okToDisplay(EveryBody,SubList,listAll, classificationsToDisplay,commitmentToDisplay, listDirector, instructorFlags, 0)) {
if(debug) System.out.print(" ok to display");
                        String tim = ns.getStartingTimeString();
						// count shifts                    
                        if(showDayCnt && ns.isDayShift()) {
                            ++member.AssignmentCount[Assignments.DAY_TYPE];
                        } else if(showSwingCnt && ns.isSwingShift()) {
                            ++member.AssignmentCount[Assignments.SWING_TYPE];
                        } else if(showNightCnt && ns.isNightShift()) {
                            ++member.AssignmentCount[Assignments.NIGHT_TYPE];
                        } else if(showTrainingCnt && ns.isTrainingShift()) {
                            ++member.AssignmentCount[Assignments.TRAINING_TYPE];
                        }
                    } //end if okToDisplay
                } //end for loop for shift
if(debug) System.out.println();
            } //end test for date
        } //end while loop (all assignments)
    } //readAssignments
	

    /************/
    /* cvtToInt */
    /************/
    int cvtToInt(String strNum) {
        int num = 0;
        try {
            if(strNum != null)
                num = Integer.parseInt(strNum);
        } catch (Exception e) {
        }
        return num;
    }

    /**************/
    /* BuildLists */
    /**************/
    void BuildLists(String IDOfEditor) {
    patrol = new PatrolData(PatrolData.FETCH_ALL_DATA,resort);

    readAssignments(patrol); //must read ASSIGNMENT data for other code to work

    ds = patrol.readDirectorSettings();
    patrol.resetRoster();
    ePatrollerList = new Vector();
    invalidPatrollerList = new Vector();
    MemberData memberx = patrol.nextMember("&nbsp;");
//	int siz = members.size();
//	int i = 0;
//int xx=0;
//if(debug) System.out.println("in BuildList, MinDays="+MinDays);
	MemberData member;

    while(memberx != null) {
//if(debug) System.out.println("memberx id="+memberx.getID());
		member = (MemberData)hash.get(memberx.getID());
if(debug) System.out.println("member="+member);
		if(member != null) {
if(debug) System.out.println(member.getID() + ": " + member.getEmail());
  	       if(member.okToDisplay(EveryBody,SubList,listAll, classificationsToDisplay,commitmentToDisplay, listDirector, instructorFlags, MinDays)) {
    	         String em = member.getEmail();
        	    //check for valid email
            	if( em != null && em.length() > 6 && em.indexOf('@') > 0 && em.indexOf('.') > 0) {
	                ePatrollerList.add(member.getID());
    	        } else {
        	        invalidPatrollerList.add(member.getID());
            	}
			}
        }
//else System.out.println("NOT OK to display "+member);
        memberx = patrol.nextMember("");
    }
//System.out.println("length of email string = "+ePatrollerList.length());
    MemberData editor = patrol.getMemberByID(IDOfEditor); //ID from cookie
//      patrol.close(); //must close connection!
    if(editor != null)
        isDirector=editor.isDirector();
    else
        isDirector = false;
    }

}