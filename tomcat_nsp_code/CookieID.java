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
//import javax.swing.UIManager;

public class CookieID {
    final static String NSP_fullname = "NSP_fullname";
    final static String NSP_goto = "NSPgoto";
    final static String NSP = "NSP";
    final static String RESORT = "Resort";

    final static boolean trace = false;

//All the folowing data must be initialized in the constructor
    String szID = null;
    private String resort;
    boolean error;

    public CookieID(HttpServletRequest request,HttpServletResponse response,String parent,String owner) {
        String szParent = null;
        String cookieID = null;
        String lastResort = null;
        int i;
        error = false;
        szID = request.getParameter("ID");
        szParent = request.getParameter(NSP_goto);
        resort = request.getParameter("resort");
if (trace) System.out.println("CookieID: ID=("+szID+")");
if (trace) System.out.println("CookieID: NSPgoto=("+szParent+")");
if (trace) System.out.println("CookieID: resort=("+resort+")");

    if(szID == null     || szID.equals("") ||
//       szParent == null || szParent.equals("") ||
       resort == null   || resort.equals("")) {
        try {
            error = true;
if (trace) System.out.println("error, lastResort=("+lastResort+"), resort=("+resort+")");
            String newLoc = PatrolData.SERVLET_URL+"memberLogin?resort="+resort+"&"+NSP_goto+"="+parent;
if (trace) System.out.println(",,calling sendRedirect("+newLoc+")");
            response.sendRedirect( newLoc);

        } catch (Exception ex) { }
        return;
    } else {
if (trace) System.out.println("Cookie was OK.  id="+szID+", parent="+szParent+", resort="+resort);
        return;
    }

    } //end CookieID()

    public String getID() {
        return szID;
    }

}