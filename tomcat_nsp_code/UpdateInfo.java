import java.io.*;
import java.text.*;
import java.util.*;
import javax.servlet.*;
import javax.servlet.http.*;
import java.lang.*;
import java.sql.*;


    /**************/
    /* UpdateInfo */
    /**************/
public class UpdateInfo extends HttpServlet {

    final static String szMonths[] = { //0 based
    "Jan", "Feb", "Mar", "Apr", "May", "June",
    "July", "Aug","Sep","Oct","Nov","Dec"
    };

    PrintWriter out;
    String title;
    ResultSet rs;
    String IDOfEditor, IDToEdit = null, NameToEdit, Purpose;
    boolean editorIsDirector;
    PatrolData patrol;
    MemberData user;
    MemberData editor;
    MemberData oldMemberData;
    boolean isNewPatroller;
    boolean deletePatroller;
    boolean finalDelete;
    boolean setNewID;
    String oldMemberID;
    boolean editInfo;
    private String resort;
//    boolean usingCookie;

    /*********/
    /* doGet */
    /*********/
    public void doGet(HttpServletRequest request,
                      HttpServletResponse response)
        throws IOException, ServletException
    {
        int found = 0;
        int i;
synchronized (this) {
//        if(request.getParameter("NoCookie") != null){
//            IDOfEditor = request.getParameter("NoCookie");
//            usingCookie = false;
//        } else {
//            usingCookie = true;
//System.out.println("Entering UpdateInfo");
            CookieID cookie = new CookieID(request,response,"UpdateInfo",null);
            if(cookie.error)
                return;
            IDOfEditor = cookie.getID();    //editor's ID
//        }
//System.out.println("  in UpdateInfo --IDOfEditor from cookie="+IDOfEditor);
        if(IDOfEditor == null)
            return;
        IDToEdit = IDOfEditor;          //same (for now!)
        resort = request.getParameter("resort");
        NameToEdit = request.getParameter("NameToEdit");
//System.out.println("NameToEdit="+NameToEdit);
        oldMemberID = request.getParameter("removeOldID");  //only set on final pass
//System.out.println("oldMemberID="+oldMemberID);

        setNewID = deletePatroller = editInfo = false;

        Purpose    = request.getParameter("Purpose");
//System.out.println("Purpose="+Purpose);
        isNewPatroller  = (Purpose != null && Purpose.equals("Add_Patroller"));
        finalDelete     = (Purpose != null && Purpose.equals("FinalDelete"));
        setNewID        = (Purpose != null && Purpose.equals("setNewID"));  //set 2nd pass
//System.out.println("Purpose: setNewID="+setNewID);

        String str1    = request.getParameter("setNewID");                  //set 1st pass
        String str2    = request.getParameter("removeName");
        String str3    = request.getParameter("EditInfo");
        if(str1 != null) {
//System.out.println("---setNewID(1)---");
            setNewID = true;
            Purpose = str1;
        }
        if(str2 != null) {
//System.out.println("---deletePatroller---");
            deletePatroller = true;
            Purpose = str2;
        }
        if(str3 != null) {
//System.out.println("---editInfo---");
            editInfo = true;
            Purpose = str3;
        }

//      setNewID           = (Purpose != null && Purpose.equals("setNewID"));
//      deletePatroller = (Purpose != null && Purpose.equals("removeName"))
//      editInfo        = (Purpose != null && Purpose.equals("EditInfo"));


        response.setContentType("text/html");
        out = response.getWriter();
        printTop();

        if(request.getParameter("HomePhone") != null || deletePatroller || finalDelete) //did I post this form to myself?
            printBodySave(request, response);
        else
            printBodyForm(request, response);

        printBottom();
} //end Syncronized
    }

    /**********/
    /* doPost */
    /**********/
    public void doPost(HttpServletRequest request,
                      HttpServletResponse response)
        throws IOException, ServletException
    {
        doGet(request, response);
    }

    /************/
    /* readData */
    /************/
    public int readData(String readID) {
        patrol = new PatrolData(PatrolData.FETCH_ALL_DATA,resort);
        if((setNewID || editInfo || deletePatroller || finalDelete) && NameToEdit != null)
        {

//System.out.println("in UpdateInfo, setNewID="+setNewID+", editInfo="+editInfo+", NameToEdit=("+NameToEdit+")");
            user = patrol.getMemberByLastNameFirstName(NameToEdit);
			if(user == null) {
				out.println("ERROR, member "+NameToEdit+" not found!");
				System.exit(1);
			}
            IDToEdit = user.getID();
        } else {
            IDToEdit = IDOfEditor;  //same
            user = patrol.getMemberByID(readID);
        }

        if(IDOfEditor.equals(PatrolData.bdLogin)) {
            editor = null;
            editorIsDirector = true;
            return 1;
        }
        editor = patrol.getMemberByID(IDOfEditor); //ID from cookie

        if(oldMemberID != null && oldMemberID.length() > 3) {
            oldMemberData = patrol.getMemberByID(oldMemberID);
        } else {
            oldMemberData = null;
		}
					
        if(editor != null)
            editorIsDirector = editor.isDirector();
        patrol.close(); //must close connection!
//        else
//            editorIsDirector = false;

        return 1;
    } //end ReadData

    /************/
    /* printTop */
    /************/
    public void printTop() {
        out.println("<html>");
        out.println("<body bgcolor=\"white\">");
        out.println("<head>");

        title = "Update Personal Information";
        if (Purpose != null)
            title += " ** " + Purpose + " Patroller **";
        out.println("<title>" + title + "</title>");
//      if(isNewPatroller) {
        //zz
            out.println("<SCRIPT LANGUAGE =\"JavaScript\">");
            out.println("function validate(val) {");
            out.println("   var last  = document.forms[0].LastName.value");
            out.println("   var first = document.forms[0].FirstName.value");
            out.println("   var ok = false;");
            out.println("   var found=false");
            out.println("   for(var i=0; i < last.length && !found; ++i) {");
            out.println("     var letter = last.charAt(i)");
            out.println("     if(letter != \" \") found = true;");
            out.println("   }");
            out.println("   if(!found)  { alert(\"You MUST enter a non-blank Last Name\"); return false; }");
            out.println("   found=false");
            out.println("   for(var i=0; i < first.length && !found; ++i) {");
            out.println("     var letter = first.charAt(i)");
            out.println("     if(letter != \" \") found = true;");
            out.println("   }");
            out.println("   if(!found)  { alert(\"You MUST enter a non-blank First Name\"); return false; }");
            out.println("   var cnt = document.forms[0].elements.length");
            out.println("   var str = document.forms[0].IDToEdit.value");
            out.println("   var num = parseInt(str);");
            out.println("   if (num >= 10001 && num <= 999999) ok = true;");
            out.println("   if(!ok) alert(\"'ID Number' must be a UNIQUE number between 10000 and 999999.  You entered '\"+str+\"'\");");
            out.println("   return ok;");
            out.println("}");

            out.println("function printWindow(){");
            out.println("   bV = parseInt(navigator.appVersion)");
            out.println("   if (bV >= 4) window.print()");
            out.println("}");

            out.println("</SCRIPT>");
//      }
        out.println("<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">");
        out.println("<META HTTP-EQUIV=\"Expires\" CONTENT=\"-1\">");
        out.println("</head>");
        out.println("<body>");

//      out.println("<script>");
/***
        out.println("function CheckID() {");
        out.println("   var str = document.forms[0].IDToEdit.value");
//      out.println("  if(???) {");
        out.println("    alert(\"value=\"+str)");
        out.println("    return false");
//      out.println("  } else {");
//      out.println("    return true");
//      out.println("  }");
        out.println("}");
**/

//      out.println("</script>");

    }

    /*********************/
    /* updateAssignments */
    /*********************/
    public void updateAssignments(String oldMemberID, String IDToEdit, Connection c) {
        PreparedStatement cs;
        ResultSet cr;
        String IDpos[] = {"P0", "P1", "P2", "P3", "P4", "P5", "P6", "P7", "P8", "P9" };
        for(int i=0; i < 10; ++i) {
            String qryString = "SELECT Date, " + IDpos[i] + " FROM `assignments` WHERE "+IDpos[i]+"="+oldMemberID;
//System.out.println(qryString);
            try {
              cs = c.prepareStatement(qryString);
              cr = cs.executeQuery();
              while(cr.next()) {
                PreparedStatement cs1;
                ResultSet cr1;
                String szDate = cr.getString("Date");
                qryString = "UPDATE assignments SET " + IDpos[i] + "=\""+IDToEdit+"\" WHERE Date=\""+szDate+"\"";
//System.out.print("  "+qryString);
                cs1 = c.prepareStatement(qryString);
                cs1.executeQuery();
//System.out.println(" - Complete");
              }

            }  catch (Exception e) {
                System.out.println("Error reseting Assignments table query:"+e.getMessage());
            } //end try
        }
    }

    /***************/
    /* printBottom */
    /***************/
    public void printBottom() {
        out.println("</body>");
        out.println("</html>");
    }

    /*****************/
    /* printBodySave */
    /*****************/
    public void printBodySave(HttpServletRequest request,HttpServletResponse response) {
    String sz;
    int i;
    readData(IDOfEditor); //mostly just to get director (ID from cookie)

    try  {
////------- the following line works for an applet, but not for a servlet -----
      Driver drv = (Driver) Class.forName(PatrolData.JDBC_DRIVER).newInstance();
    }
    catch (Exception e) {
        out.println("Cannot load the driver, reason:"+e.toString());
        return;
    }

    // Try to connect to the database

    try {

      // Change MyDSN, myUsername and myPassword to your specific DSN
        Connection c =java.sql.DriverManager.getConnection(PatrolData.getJDBC_URL(resort));
        PreparedStatement sRost;

        MemberData md = new MemberData();
        if (deletePatroller || finalDelete) {
//          md = patrol.getMemberByID(IDToEdit);
            md = user;
        }else {
            IDToEdit = request.getParameter(MemberData.dbData[MemberData.ID_NUM][MemberData.SERVLET_NAME]);
            md = new MemberData(request); //read from command line
//          md.setID(IDToEdit);
        }
//      String szFirst = request.getParameter(MemberData.dbData[MemberData.FIRST][MemberData.SERVLET_NAME]);
//      String szLast  = request.getParameter(MemberData.dbData[MemberData.LAST][MemberData.SERVLET_NAME]);
        String name = md.getFullName();

        //        out.println("<h2>Your new record has been updated as follows:</h2><br>");
        if (deletePatroller)
            out.println("<h2>CAUTION!!! DELETE record for "+name+":</h2><br>");
        else if (isNewPatroller)
            out.println("<h2>NEW record for "+name+":</h2>");
        else if (finalDelete)
            out.println("<h2>Deleted record for "+name+":</h2>");
        else
            out.println("<h2>Updated record for "+name+":</h2>");
        out.println("host resort: "+PatrolData.getResortFullName(resort)+"<br>");

        int first=0; // was DB_START;
        int last = MemberData.DB_SIZE; //director is last column

        if(!resort.equalsIgnoreCase("Brighton"))
            last -= 3;
        boolean isError = false;
//System.out.println("-----------************---------------");
        if (deletePatroller) {
//System.out.println("delete Patroller");
//          sz = md.getDeleteSQLString();
            //don't DELETE record here
        } else if(finalDelete) {
//System.out.println("Final delete Patroller");
            sz = md.getDeleteSQLString();
            sRost = c.prepareStatement(sz);
            sRost.executeQuery();
//System.out.println("executing delete");
            updateAssignments(IDToEdit, "0", c);    //replace ID with 0 in assignment table
        } else if(isNewPatroller) {
//System.out.println("new patroller");
            //check for duplicate ID
//asdfasdfasd7
            patrol = new PatrolData(PatrolData.FETCH_ALL_DATA,resort);
            MemberData mem = patrol.getMemberByID(md.getID()); //zz
            patrol.close(); //must close connection!
            if(mem != null) {
                out.println("<h3>ERROR, id '"+md.getID()+"' is already owned by "+mem.getFullName()+"</h3>");
                out.println("<br><br>Use the 'Back' button on your browser to make a change.");
                isError = true;
            } else {
                sz = md.getInsertSQLString();
                sRost = c.prepareStatement(sz);
                sRost.executeQuery();
            }
        } else if(setNewID) {
//System.out.println("Changing Member ID.  OldID="+oldMemberID+", NewID="+IDToEdit);
            //test if any ID change was made
            patrol = new PatrolData(PatrolData.FETCH_ALL_DATA,resort);
            MemberData mem = patrol.getMemberByID(md.getID()); //zz
            patrol.close(); //must close connection!
            if(mem != null) {
                out.println("<h3>ERROR, id '"+md.getID()+"' is already owned by "+mem.getFullName()+"</h3>");
                out.println("<br><br>Use the 'Back' button on your browser to make a change.");
            } else if(IDToEdit.equals(oldMemberID)) {
                //if ID was same, just update record
                //use data read from command line
                sz = md.getUpdateSQLString();
                sRost = c.prepareStatement(sz);
                sRost.executeQuery();
            } else {
            //if ID was new, insert new, then delete old
            //insert new ID
                updateAssignments(oldMemberID, IDToEdit,c);
                sz = md.getInsertSQLString();
                sRost = c.prepareStatement(sz);
                sRost.executeQuery();
            //remove OLD id
                sz = oldMemberData.getDeleteSQLString();
                sRost = c.prepareStatement(sz);
                sRost.executeQuery();
            }
        } else {
//System.out.println("Simple Update");
            sz = md.getUpdateSQLString();
            sRost = c.prepareStatement(sz);
            sRost.executeQuery();
        }

        c.close();
        if(finalDelete) {
//			out.println("Deleted...<br>");
			String nextPage = PatrolData.SERVLET_URL+"Directors?resort="+resort+"&ID+"+IDOfEditor;
//System.out.println("+++ in finalDelete, nextPage="+nextPage);			
//            response.sendRedirect(nextPage);
        }
        if(editorIsDirector) {
            first = 0;
            last = MemberData.DB_SIZE;      //show director field for director
            if(!resort.equalsIgnoreCase("Brighton"))
                last -= 5; //skip display of all "Credit" stuff, except for brighton resort
        }

        if(isError)
            return; //don't continue

        out.println("<table border=\"1\" width=\"580\" cellspacing=\"0\" cellpadding=\"0\">");
//print data that is to be saved (not in FORM format)
        String szTmp;
        for (i=first; i < last; ++i) {
            if(!resort.equalsIgnoreCase("Brighton")) {
                if(i == MemberData.TEAM_LEAD || i == MemberData.MENTORING)
                    continue;   //only display if brighton
            }
            //only display Team Lead trained to Directors
            if(i == MemberData.TEAM_LEAD && !editorIsDirector)
                continue;
//          localData[i] = request.getParameter(MemberData.dbData[i][MemberData.SERVLET_NAME]);
            szTmp = md.memberData[i];
            if(szTmp == null || szTmp.length() == 0)
                szTmp = " ";
			
if(i == MemberData.CARRY_OVER_CREDITS) { //old field
continue;
}
if(i == MemberData.CREDITS_USED) { //old field
continue;
}

				
           if(deletePatroller && i == MemberData.CARRY_OVER_CREDITS) { //already good, except on delete
                szTmp = MemberData.creditsToVouchers(szTmp);
            }

            out.println("<tr>");
            out.println("<td width=\"230\" valign=\"middle\"><font size=3>"+MemberData.dbData[i][MemberData.DLG_NAME]+"</font></td>");
            out.println("<td width=\"350\" valign=\"left\"><font size=3>");
            if(i == MemberData.LAST_CREDIT_UPDATE) {
//              out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"voucherDay\"   VALUE=\""+day+"\">");
//              out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"voucherMonth\" VALUE=\""+month+"\">");
//              out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"voucherYear\"  VALUE=\""+year+"\">");
                String date = "Error";
                Calendar cal = Calendar.getInstance();
                try {
                long millis = Long.parseLong(szTmp);
                cal.setTimeInMillis(millis);
                date = szMonths[cal.get(Calendar.MONTH)] + "-" + cal.get(Calendar.DATE) + "-" + cal.get(Calendar.YEAR);
                } catch (Exception e) {
                    System.out.println("Error parsing Long value for: ("+szTmp+")");
                }
                out.println(date);
            } else if(i == MemberData.COMMITMENT) {
                out.println(MemberData.getCommitmentString(szTmp));
            } else if(i == MemberData.TEAM_LEAD || i == MemberData.MENTORING || i == MemberData.CAN_EARN_CREDITS) {
//System.out.println(i+") = ("+szTmp+")");
                if(szTmp.equals("1")) out.println("Yes");
                else                  out.println("No");
            } else if(i == MemberData.INSTRUCTOR) {
                out.println(MemberData.getInstructorString(szTmp));
            } else if(i == MemberData.COMMENTS && !editorIsDirector) {
                //ONLY display this firld is the editor is a director
            } else
                out.println(szTmp);
            out.println("&nbsp;</font></td>");
            out.println("</tr>");
        }
        out.println("</table>");
        if(deletePatroller) {
            out.println("<form action=\""+PatrolData.SERVLET_URL+"UpdateInfo\" method=POST>");
            out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"resort\" VALUE=\""+resort+"\">");
//System.out.println("+++ in deletePatroller, IDOfEditor="+IDOfEditor);			
			out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"ID\" VALUE=\""+IDOfEditor+"\">");
            out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"Purpose\" VALUE=\"FinalDelete\">");
            out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"NameToEdit\" VALUE=\""+NameToEdit+"\">");
            out.println("<INPUT TYPE=SUBMIT VALUE=\"Delete Record\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
            out.println("<input type=\"button\" value=\"Cancel\" name=\"B3\" onClick=\"history.back()\">");
            out.println("</form>");
//???
        } else if(isNewPatroller) {
            String nextPage=PatrolData.SERVLET_URL+"UpdateInfo?resort="+resort+"&Purpose=Add_Patroller&ID="+IDOfEditor;
            out.println("<br><INPUT TYPE=button VALUE=\"Add Another New Patroller\" onClick=window.location=\""+nextPage+"\">&nbsp;&nbsp;&nbsp;&nbsp;");
            nextPage=PatrolData.SERVLET_URL+"Directors?resort="+resort+"&ID="+IDOfEditor;
            out.println("<INPUT TYPE=button VALUE=\"Return to Director's Page\" onClick=window.location=\""+nextPage+"\">");
		} else // if (finalDelete || setNewID) 
		{
            String nextPage=PatrolData.SERVLET_URL+"Directors?resort="+resort+"&ID="+IDOfEditor;
            out.println("<INPUT TYPE=button VALUE=\"Return to Director's Page\" onClick=window.location=\""+nextPage+"\">");
		}
//        } else if(editorIsDirector && usingCookie ) {
//            String nextPage=PatrolData.SERVLET_URL+"Directors?resort="+resort+"&ID="+IDOfEditor;
//            out.println("<br><INPUT TYPE=button VALUE=\"Return to Director's Page\" onClick=window.location=\""+nextPage+"\">");
//        }


    }  catch (Exception e) {
        out.println("Error connecting or reading table:"+e.getMessage()+"<br>");
        System.out.println("Error connecting or reading table:"+e.getMessage()+"<br>");
        System.out.println("in UpdateInfo.  ID of editor:"+IDOfEditor);
        System.out.println("in UpdateInfo.  ID to edit:"+IDToEdit);
    } //end try

    } //end printBodySave

    /*****************/
    /* printBodyForm */
    /*****************/
    public void printBodyForm(HttpServletRequest request,HttpServletResponse response) {
//        String ID = request.getParameter("ID");
        int i;
        //was any data passed into this form?

        int isValidNum = 0;

        if (IDToEdit != null) {
            isValidNum = readData(IDToEdit);
        }
//System.out.println("**** IDToEdit = "+IDToEdit+", isValidNum="+isValidNum);
//        out.println("Parameters in this request:" + "<br>");
        if (isValidNum == 1) {
 //valid #
            String fullName;
            if(isNewPatroller) {
                user = new MemberData();
                fullName = "NEW PATROLLER";
                IDToEdit = "";
            }
            else {
                fullName = user.getFullName();
            }
// header -------------------
            out.println("<h2>Personal Information for:  " + fullName + "</h2><br>");
            out.println("host resort: <b>"+PatrolData.getResortFullName(resort)+"</b>");
            out.println("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
            out.println("<a href=\"javascript:printWindow()\">Print This Page</a></font><br>");

            out.println("<P>");
            out.print("<form action=\"");
            out.print(PatrolData.SERVLET_URL+"UpdateInfo?resort="+resort+"&ID="+IDOfEditor);
            out.print("\" ");
//          out.print("UpdateInfo\" ");
            out.println("method=POST ");
            if(isNewPatroller || setNewID)
                out.println("onSubmit=\"return validate(1)\" ");
            out.println(">");

            out.println("<table border=\"0\" width=\"800\" cellspacing=\"0\" cellpadding=\"0\">");

            int first = 0;
            int last = MemberData.DB_SIZE;      //show director field for director
//            if(!editorIsDirector))
//                last -= 1;
            for (i = first; i < last; ++i) {
                showField(i,user.memberData[i], editorIsDirector);
            }

            out.println("</table>");
//hidden fields ---------------------
//out.println("usingCookie="+usingCookie+"<br>");
//..                out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"ID\" VALUE=\""+IDOfEditor+"\">");
//..	            out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"resort\" VALUE=\""+resort+"\">");
//            if(!usingCookie) {
//                out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"NoCookie\" VALUE=\""+IDOfEditor+"\">");
//            }
            if(isNewPatroller) {
                out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"Purpose\" VALUE=\"Add_Patroller\">");
                out.println("<input type=submit value=\"New Profile\">");
            }
            else if(setNewID) {
                out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"Purpose\" VALUE=\"setNewID\">");
                out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"removeOldID\" VALUE=\""+IDToEdit+"\">");
                out.println("<input type=submit value=\"Set New ID\">");
            }
            else {

//submit button ---------------------
                out.println("<input type=submit value=\"Update Profile\">");
            }
            out.println("</form>");
        } else {
//no number
            out.println("<h3>" + title + "</h3><br>");

//          out.println("Invalid ID number<br>");
            out.println("<P>");
            out.print("<form action=\"");
            out.print("UpdateInfo?resort="+resort+"&ID="+IDOfEditor+"\" ");
            out.println("method=POST>");
            out.println("Patrol Number:");
            out.println("<input type=text size=7 maxlength=7 name=ID value=\""+IDToEdit+"\">");
            out.println("<br>");
//            out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"ID\" VALUE=\"" + IDOfEditor + "\">");
            out.println("<input type=submit>");
            out.println("</form>");
        }
    }

    /*************/
    /* isValidField */
    /*************/
    public boolean isValidField(int index, boolean editorIsDirector) {
		//fields ONLY visible to directors
        if(!editorIsDirector) {
            switch (index) {
            	case MemberData.COMMENTS:
	            case MemberData.TEAM_LEAD:				
                return false;
			}
		}
		//fields only visible is using locker room scheduling software
        if(!resort.equalsIgnoreCase("Brighton")) {
            switch (index) {
	            case MemberData.TEAM_LEAD:
                case MemberData.MENTORING:
                case MemberData.CAN_EARN_CREDITS:
                case MemberData.LAST_CREDIT_UPDATE:
                case MemberData.CARRY_OVER_CREDITS:
                case MemberData.CREDITS_EARNED:
                case MemberData.CREDITS_USED:
    	            return false;
           }
		}
        return true;
    }
    /*************/
    /* showField */
    /*************/
    public void showField(int index, String szDefault, boolean editorIsDirector) {

if(index == MemberData.CARRY_OVER_CREDITS) { //old field
return;
}
if(index == MemberData.CREDITS_USED) { //old field
return;
}


        String label = MemberData.dbData[index][MemberData.DLG_NAME];
        String szField = MemberData.dbData[index][MemberData.SERVLET_NAME];
        if(!isValidField(index, editorIsDirector)) {
            out.println("<INPUT TYPE=\"HIDDEN\" NAME=\""+szField+"\" VALUE=\""+szDefault+"\">");
//System.out.println(index+") field "+szField+"=("+szDefault+") is hidden, for resort "+resort);
            return;
        }
        int len = 30;
//System.out.println(index+") " + label + ", " + szField);
        if(index == MemberData.CAN_EARN_CREDITS ) {
            out.println("<tr>");
            out.println("<td bgcolor=#e1e1e1 valign=center align=right><font size=3><b>Please Note:&nbsp;</b></font></td>");
            out.println("<td bgcolor=#e1e1e1 valign=center align=left><font size=3>&nbsp;&nbsp;2 credits equals 1 day pass voucher</font></td>");
            out.println("</tr>");
        }
        out.println("<tr>");
        if (index == MemberData.COMMENTS && !editorIsDirector) {
			//display NOTHING if the editor is not a director
			label = "&nbsp;" ;
		} 
	    out.println("<td width=\"210\" valign=\"middle\"><font size=3>" + label + "</font></td>");

        int width=400;

        if(index == MemberData.INSTRUCTOR)
            width=450;
        out.println("<td width=\""+width+"\" valign=\"left\"><font size=3>");
//  static final int CLASSIFICATION=1;
        if(index == MemberData.ID_NUM)
            len = 6;

        if(index == MemberData.COMMITMENT) {
            out.println("<SELECT NAME="+szField+" CLASS=\"select\">");
            out.println("<OPTION "+(szDefault.equals("2") ? "SELECTED " : "") +" VALUE=\"2\">Full Time");
            out.println("<OPTION "+(szDefault.equals("1") ? "SELECTED " : "") +" VALUE=\"1\">Part Time");
            out.println("<OPTION "+(szDefault.equals("0") ? "SELECTED " : "") +" VALUE=\"0\">Inactive");
            out.println("</SELECT>");
        } else if(index == MemberData.INSTRUCTOR) {
            //convert string to number
            int idx = 0;
//System.out.println("szDefault="+szDefault);
            try {
                idx = Integer.parseInt(szDefault);
            } catch (Exception e) {}
//System.out.println("idx="+idx);
            out.println("<input type=\"checkbox\" name=\"OEC\""+((idx & 1) == 1 ? "checked" : "")+">OEC");
            out.println("&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"CPR\""+((idx & 2) == 2 ? "checked" : "")+">CPR");
            out.println("&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"SKI\""+((idx & 4) == 4 ? "checked" : "")+">Ski");
            out.println("&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"TOBOGGAN\""+((idx & 8) == 8 ? "checked" : "")+">Toboggan");
        } else if(index == MemberData.TEAM_LEAD) {
// must be a director to get this far
            out.println("<SELECT NAME="+szField+" CLASS=\"select\">");
            out.println("<OPTION "+(szDefault.equals("1") ? "SELECTED " : "") +" VALUE=\"1\">Yes");
            out.println("<OPTION "+(!szDefault.equals("1")? "SELECTED " : "") +" VALUE=\"0\">No");
            out.println("</SELECT>");
        } else if(index == MemberData.MENTORING) {
//must be Brighton to get this far
            if(editorIsDirector) {
                out.println("<SELECT NAME="+szField+" CLASS=\"select\">");
                out.println("<OPTION "+(szDefault.equals("1") ? "SELECTED " : "") +" VALUE=\"1\">Yes");
                out.println("<OPTION "+(!szDefault.equals("1")? "SELECTED " : "") +" VALUE=\"0\">No");
                out.println("</SELECT>");
            } else {
                if(szDefault.equals("1"))
                    out.println("Yes");
                else
                    out.println("No");
                out.println("<INPUT TYPE=\"HIDDEN\" NAME=\""+szField+"\" VALUE=\""+szDefault+"\">");
            }
        } else if(index == MemberData.DIRECTOR) {
            if(editorIsDirector) {
                out.println("<SELECT NAME="+szField+" CLASS=\"select\">");
                boolean isYes = szDefault.equals("yes");
                boolean isYesEmail = szDefault.equals("yesEmail");
                out.println("<OPTION "+(isYes   ? "SELECTED " : "") +" VALUE=\"yes\">Yes");
                out.println("<OPTION "+(isYesEmail ? "SELECTED " : "") +" VALUE=\"yesEmail\">Yes & email me ALL schedule changes");
                out.println("<OPTION "+(!isYes && !isYesEmail  ? "SELECTED " : "") +" VALUE=\"\">No");
                out.println("</SELECT>");
            } else {
                out.println("No");
                out.println("<INPUT TYPE=\"HIDDEN\" NAME=\""+szField+"\" VALUE=\"no\">");
            }
        } else if(index == MemberData.CLASSIFICATION) {
            if(editorIsDirector) {
                out.println("<SELECT NAME="+szField+" CLASS=\"select\">");
                out.println("<OPTION "+(szDefault.equals("ALM") ? "SELECTED " : "") +" VALUE=\"ALM\">Alumni");
                out.println("<OPTION "+(szDefault.equals("AUX") ? "SELECTED " : "") +" VALUE=\"AUX\">Auxilary");
                out.println("<OPTION "+(szDefault.equals("BAS") ? "SELECTED " : "") +" VALUE=\"BAS\">Basic");
                out.println("<OPTION "+(szDefault.equals("CAN") ? "SELECTED " : "") +" VALUE=\"CAN\">Candidate");
//                out.println("<OPTION "+(szDefault.equals("INA") ? "SELECTED " : "") +" VALUE=\"INA\">Inactive");
                out.println("<OPTION "+(szDefault.equals("PRO") ? "SELECTED " : "") +" VALUE=\"PRO\">Pro");
                out.println("<OPTION "+(szDefault.equals("SR")  ? "SELECTED " : "") +" VALUE=\"SR\">Senior");
                out.println("<OPTION "+(szDefault.equals("SRA") ? "SELECTED " : "") +" VALUE=\"SRA\">Senior Auxilary");
                out.println("<OPTION "+(szDefault.equals("TRA") ? "SELECTED " : "") +" VALUE=\"TRA\">Transfer");
                out.println("<OPTION "+(szDefault.equals("OTH") ? "SELECTED " : "") +" VALUE=\"OTH\">Other");
                out.println("</SELECT>");
            } else {
                String classification = user.getFullClassification();
                if(classification == null)
                    classification = user.getClassification();
                out.println(classification);
                out.println("<INPUT TYPE=\"HIDDEN\" NAME=\""+szField+"\" VALUE=\""+szDefault+"\">");
            }
        } else if(index == MemberData.SUB) {
            out.println("<SELECT NAME="+szField+" CLASS=\"select\">");
            out.println("<OPTION "+(szDefault.equals("yes") ? "SELECTED " : "") +" VALUE=\"yes\">Yes");
            out.println("<OPTION "+(!szDefault.equals("yes")? "SELECTED " : "") +" VALUE=\"\">No");
            out.println("</SELECT>");
        } else if(index == MemberData.EMERGENCY) {
            out.println("<SELECT NAME="+szField+" CLASS=\"select\">");
            out.println("<OPTION "+(szDefault.trim().equals("") ? "SELECTED " : "") +" VALUE=\"\">No");
            out.println("<OPTION "+(szDefault.equals("day")  ? "SELECTED " : "") +" VALUE=\"day\">Day Only");
            out.println("<OPTION "+(szDefault.equals("night")? "SELECTED " : "") +" VALUE=\"night\">Night Only");  //brighton
            out.println("<OPTION "+(szDefault.equals("both") ? "SELECTED " : "") +" VALUE=\"both\">Both (Days & Nights)");
            out.println("</SELECT>");
        } else if(index == MemberData.LAST_UPDATED) {
//              out.println("<input type=text size=12 name="+szField+" value=\""+szDefault+"\" readonly>");
                out.println(szDefault);
                out.println("<INPUT TYPE=\"HIDDEN\" NAME=\""+szField+"\" VALUE=\""+szDefault+"\">");
        } else if(index == MemberData.ID_NUM) {
            if(isNewPatroller || setNewID ) {
//enable edit
                out.println("<input type=text size=7 maxlength=7 name="+szField+" value=\""+szDefault+"\">");
            } else {
//display only
                out.println(IDToEdit);
//but needs IDToEdit Field
                out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"IDToEdit\" VALUE=\""+IDToEdit+"\">");
            }
        } else if (index == MemberData.LAST_CREDIT_UPDATE) {
// must be Brighton to get this far
            int year = 2003,month = 0,day = 1; //bogus day
            String date = szMonths[month] + "-" + day + "-" + year;
            if(szDefault.equals("0"))
                szDefault = "1048169730029"; //feb, 20,2003

            try {
                // the date is stored in szDefault
                Calendar cal = Calendar.getInstance();
                long millis = Long.parseLong(szDefault);
                cal.setTimeInMillis(millis);
                year = cal.get(Calendar.YEAR);
                month = cal.get(Calendar.MONTH);
                day = cal.get(Calendar.DATE);
                date = szMonths[month] + "-" + day + "-" + year;
//System.out.println("date="+date);
            } catch (Exception e) {
                System.out.println("Error, exception parsing "+szDefault+" in UpdateInfo: "+e);
            };
            if(!editorIsDirector) {
                out.println(date);
                out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"voucherDay\"   VALUE=\""+day+"\">");
                out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"voucherMonth\" VALUE=\""+month+"\">");
                out.println("<INPUT TYPE=\"HIDDEN\" NAME=\"voucherYear\"  VALUE=\""+year+"\">");
            } else {

                out.println("<select size=1 name=\"voucherDay\">");
                for(int i=1; i <= 31; ++i)
                    out.println("    <option "+((i == day) ? "selected" : "") + ">"+i+"</option>");
                out.println("  </select>&nbsp;&nbsp;<select size=1 name=\"voucherMonth\">");
                for(int i=0; i < 12; ++i)
                    out.println("    <option value=\""+i+"\" "+((i == month) ? "selected" : "") + ">"+szMonths[i]+"</option>");
                out.println("  </select> ");
                out.println("  </select>&nbsp;&nbsp;<select size=1 name=\"voucherYear\">");

                for(int i=year-1; i <= year+2; ++i)
                    out.println("    <option value=\""+i+"\" "+((i == year) ? "selected" : "") + ">"+i+"</option>");
                out.println("  </select>");
            }
        } else if (index == MemberData.CAN_EARN_CREDITS) {
// must be Brighton to get this far
            if(editorIsDirector) {
                out.println("<SELECT NAME="+szField+" CLASS=\"select\">");
                boolean isYes = szDefault.equals("1");
                out.println("<OPTION "+(isYes ? "SELECTED" : "") +" VALUE=1>Yes");
                out.println("<OPTION "+(isYes ? "" : "SELECTED") +" VALUE=0>No");
                out.println("</SELECT>");
            } else {
                if(szDefault.equals("1"))
                    out.println("Yes");
                else
                    out.println("No");
                out.println("<INPUT TYPE=\"HIDDEN\" NAME=\""+szField+"\" VALUE=\""+szDefault+"\">");
            }
        } else if(index >= MemberData.CARRY_OVER_CREDITS && index <= MemberData.CREDITS_USED) {
// must be Brighton to get this far
            szDefault = MemberData.creditsToVouchers(szDefault);
            String disabled = " readonly ";
            if(editorIsDirector) {
//                disabled = "";
                out.println("<input type=text size="+len+" name="+szField+" value=\""+szDefault+"\">");
            } else {
                out.println(szDefault);
                out.println("<INPUT TYPE=\"HIDDEN\" NAME=\""+szField+"\" VALUE=\""+szDefault+"\">");
            }
            try {
                double xx = Double.parseDouble(szDefault) / 2;
                out.println("&nbsp;&nbsp;&nbsp;("+xx+" Vouchers)");
            } catch (Exception e) {}
        } else if (index == MemberData.COMMENTS) {
			//display NOTHING if the editor is not a director
       		if (editorIsDirector && index == MemberData.COMMENTS)
            	out.println("<textarea cols=70 rows=4 name="+szField+">"+szDefault+"</textarea>");
        } else {
            out.println("<input type=text size="+len+" name="+szField+" value=\""+szDefault+"\">");
        }

        out.println("</font></td>");
        out.println("</tr>");
    }
}


