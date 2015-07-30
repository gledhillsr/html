/* $Id: memberLogin.java,v 1.1.1.1 1999/10/09 00:19:59 duncan Exp $
 *
 */

import java.io.*;
import java.text.*;
import java.util.*;
import javax.servlet.*;
import javax.servlet.http.*;
import java.lang.*;
import java.sql.*;

public class memberLogin extends HttpServlet {

//    ResourceBundle rb = ResourceBundle.getBundle("LocalStrings");

    public void doGet(HttpServletRequest request,
                      HttpServletResponse response)
        throws IOException, ServletException
    {
        PrintWriter out;
        String szParent;
        String resort;

        response.setContentType("text/html");
        out = response.getWriter();
//no default cookie, since other login attempts start here
//        Cookie cookie = new Cookie("NSPgoto", "UpdateInfo");
//      cookie.setMaxAge(60*30); //default is -1, indicating cookie is for current session only
//        response.addCookie(cookie);

        resort = request.getParameter("resort");
//System.out.println("memberLogin:resort="+resort);
        szParent = request.getParameter(CookieID.NSP_goto);
//System.out.println("memberLogin:szParent="+szParent);
        printTop(out, resort);
        if(PatrolData.validResort(resort))
            printMiddle(out, szParent, resort);
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
        out.println("<html>");
        out.println("<head>");
        out.println("<meta http-equiv=\"Content-Language\" content=\"en-us\">");
        out.println("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1252\">");
        out.println("<title>Member Login</title>");
        out.println("<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">");
        out.println("<META HTTP-EQUIV=\"Expires\" CONTENT=\"-1\">");
        out.println("</head>");
        out.println("<body>");
        out.println("<p><H2>"+PatrolData.getResortFullName(resort)+" Ski Patrol Login</H2></p>");
        out.println("<p>");
        out.println("<p>&nbsp;<table border=\"0\" width=\"500\" cellspacing=\"4\" cellpadding=\"0\">");
        out.println("  <tr>");
        if(resort.equalsIgnoreCase("Sample")) {
            out.println("    <td width=\"75%\">To try <b>DEMO</b> mode, login as a Director, or a Normal Patroller<br>");
            out.println("&nbsp;&nbsp;&nbsp;&nbsp;Director  id='<b>123456</b>' password='<b>password</b>'<br>");
            out.println("&nbsp;&nbsp;&nbsp;&nbsp;Patroller id='<b>111111</b>' password='<b>password</b>'<br><br>");
            out.println("Everything is enabled, but no email notifications will be sent.");
        } else
            out.println("    <td width=\"75%\">If you have not yet logged in, your <b>last name</b> is your password");
        out.println("    </td>");
        out.println("  </tr>");
        out.println("  <tr>");
        out.println("    <td width=\"75%\" valign=top>");
        out.println("<p>");

    }

    private void printMiddle(PrintWriter out, String szParent, String resort) {
        out.println("&nbsp;");
//post to

        out.println("<form method=post action=\""+PatrolData.SERVLET_URL+"loginHelp\">");
        out.println("<INPUT TYPE=\"HIDDEN\" NAME=\""+CookieID.NSP_goto+"\" VALUE=\""+szParent+"\">");
        out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"resort\" VALUE=\""+resort+"\">");

        out.println("  <TABLE WIDTH=450 BGCOLOR=#ABABAB ALIGN=center BORDER=0 CELLSPACING=1 CELLPADDING=5>");
        out.println("   <TR>");
        out.println("       <TD>");
        out.println("       <P>&nbsp;<TABLE WIDTH=\"75%\" BGCOLOR=#ABABAB align=center BORDER=0 CELLSPACING=1 CELLPADDING=1>");
        out.println("   <TR>");
        out.println("       <TD><B><font face=verdana, size =2 color=white arial>Member ID Number:</font></B></TD>");
        out.println("       <TD>");
        out.println("       <INPUT id=ID name=ID> ");
        out.println("        </TD>");
        out.println("   </TR>");
        out.println("   <TR>");
        out.println("       <TD><B><font face=verdana, size =2 color=white arial>Password:</font></B></TD>");
        out.println("       <TD>");
        out.println("       <INPUT type=\"password\" id=Password name=Password>");
//??
        out.println("       <INPUT type=\"submit\" value=\"Login\" id=submit1 name=submit1>");
        out.println("          </a></TD>");
        out.println("   </TR>");
        out.println("   <TR>");
        out.println("       <TD colspan=2 align=middle>");
        out.println("       <font face=verdana, size =2 arial >");
//login help
        out.println("        <A href=\""+PatrolData.SERVLET_URL+"loginHelp?resort="+resort+"\">Login Help</a></font>");
        out.println("        </TD>");
        out.println("   </TR>");
        out.println("   <TR>");
        out.println("       <TD colspan=2 align=middle>");
//activate your account
        out.println("   </TR>");
        out.println("  </TABLE>");
        out.println("</FORM>");
        out.println("</P></TD>");
        out.println("   </TR>");
        out.println("</TABLE>");
        out.println("</P></td>");
        out.println("   </tr>");
        out.println("</table>");

    }

    private void printBottom(PrintWriter out) {
        out.println("</body>");
        out.println("</html>");
    }

}


