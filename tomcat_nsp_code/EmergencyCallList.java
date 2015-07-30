import java.io.*;
import java.text.*;
import java.util.*;
import javax.servlet.*;
import javax.servlet.http.*;
import java.lang.*;
import java.sql.*;

public class EmergencyCallList extends HttpServlet {

//    ResourceBundle rb = ResourceBundle.getBundle("LocalStrings");
	    
    public void doGet(HttpServletRequest request,
                      HttpServletResponse response)
        throws IOException, ServletException
    {
	    PrintWriter out;
		String resort;
		
		CookieID cookie = new CookieID(request,response,"EmergencyCallList",null);

        response.setContentType("text/html");
        out = response.getWriter();
		resort = request.getParameter("resort");		

	    printTop(out,resort);
		if(PatrolData.validResort(resort))
			printBody(out,resort);
		else
			out.println("Invalid host resort.");			
	    printBottom(out);		
    }

    public void doPost(HttpServletRequest request,
                      HttpServletResponse response)
        throws IOException, ServletException
    {
        doGet(request, response);
    }

    public void printTop(PrintWriter out, String resort) {
	    out.println("<html><head>");
	    out.println("<meta http-equiv=\"Content-Language\" content=\"en-us\">");
	    out.println("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1252\">");
	    out.println("<title>Emergency Call List</title>");
        out.println("<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">");
        out.println("<META HTTP-EQUIV=\"Expires\" CONTENT=\"-1\">");
	    out.println("</head>");
	    out.println("<h2>"+PatrolData.getResortFullName(resort)+"'s Emergency Call List (for lift Evac)</h2>");
	    out.println("    <table style=\"font-size: 10pt; face=\'Verdana, Arial, Helvetica\' \" border=\"1\" width=\"100%\" bordercolordark=\"#003366\" bordercolorlight=\"#C0C0C0\">");
	    out.println("        <tr>");
	    out.println("          <td width=\"148\" bgcolor=\"#C0C0C0\"><font face=\"Verdana, Arial, Helvetica\"><font size=\"2\">Name</font></font></td>");
	    out.println("          <td width=\"70\" bgcolor=\"#C0C0C0\"><font face=\"Verdana, Arial, Helvetica\"><font size=\"2\">Day/Night<br> or Both</font></font></td>");
	    out.println("          <td width=\"90\" bgcolor=\"#C0C0C0\"><font face=\"Verdana, Arial, Helvetica\"><font size=\"2\">Home</font></font></td>");
	    out.println("          <td width=\"83\" bgcolor=\"#C0C0C0\"><font face=\"Verdana, Arial, Helvetica\"><font size=\"2\">Work</font></font></td>");
	    out.println("          <td width=\"70\" bgcolor=\"#C0C0C0\"><font face=\"Verdana, Arial, Helvetica\"><font size=\"2\">Cell</font></font></td>");
	    out.println("          <td width=\"73\" bgcolor=\"#C0C0C0\"><font face=\"Verdana, Arial, Helvetica\"><font size=\"2\">Pager</font></font></td>");
	    out.println("          <td width=\"136\" bgcolor=\"#C0C0C0\"><font face=\"Verdana, Arial, Helvetica\"><font size=\"2\">Email</font></font></td>");
	    out.println("        </tr>");
    }

    private void printBottom(PrintWriter out) {
	    java.util.Date trialTime = new java.util.Date();

        out.println("</table>");

        out.println("<br>Last updated: "+trialTime);

        out.println("</body></html>");
    }

    public String getServletInfo() {
	return "Create a page lists people that may subsitute for Day/Night Skiing";
    }

    public void printBody(PrintWriter out, String resort) {
	PatrolData patrol = new PatrolData(PatrolData.FETCH_ALL_DATA,resort);
	MemberData member = patrol.nextMember("&nbsp;");
	while(member != null) {
		if(member.getEmergency() != null && !member.getEmergency().equals(" ") && !member.getEmergency().equals("&nbsp;"))
  		    member.printEmergencyCallRow(out,member.getEmergency());
		member = patrol.nextMember("&nbsp;");	// "&nbsp;" is the default string field
	}
	patrol.close();	//must close connection!
    } //end printBody
}


