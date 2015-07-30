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
import java.io.*;
import java.text.DateFormat;

public class reminder {
    int status = 0;
    DBAccess dbAccess = null;
    AMC amc;
    Vector emailTo = new Vector();
/** Creates a new instance of reminder */
    public reminder() {
        if((dbAccess = new DBAccess(null)) != null) {
            dbAccess.resetAMC_by_PastDue();
            String from = "autoUpdate@online-csr.com";
            while((amc = dbAccess.nextAMC()) != null) {
                String due = amc.getRemindDate();
System.out.println(amc.getBTN() + "  status = " + amc.getStatus() +", due time=" + due + " = " + UnixToDate(amc.getRemindDate()) );
               emailTo.clear();
               if(amc.getStatus().equals("Closed") || due.length() < 3 || due.charAt(0) == '-')
                   continue;
               String message = getEmailBody();
               if(emailTo.size() == 0) {  //nobody to e-mail to
                   System.out.println("no one in email list, so flag as done.");
                   Date now = new Date();
                   long ticks = -now.getTime()/1000;
                   String st = Long.toString(ticks);
                   amc.setRemindDate(st);
                   dbAccess.updateAMC(amc);
                   continue;
               }
System.out.println("Processing..., date=" + UnixToDate(amc.getRemindDate()));               
               //send E-mail
//String smtp="mail.gledhills.com";
String smtp="mail.online-csr.com";

System.out.println("using smtp="+smtp+" from="+from);

		MailMan mail= new MailMan(smtp,from);
		if(mail != null) {
			try {
//		        mail.sendMessage(<subject>, <message>, <recipient address>);
System.out.println("emailTo.size() = " + emailTo.size());                            
                            for(int i=0; i < emailTo.size(); ++i) {
                                mail.sendMessage("Online-CSR Request Action Overdue", message, (String)emailTo.elementAt(i));
                                System.out.println("mail sent to: "+emailTo.elementAt(i));	//no e-mail, JUST LOG IT
                             }
                            Date now = new Date();
                            long ticks = -now.getTime()/1000;
                            String st = Long.toString(ticks);
                            amc.setRemindDate(st);
                            dbAccess.updateAMC(amc);
			} catch ( MailManException ex) {
//			} catch ( Exception ex) {
				System.out.println("error "+ex);
				System.out.println("attempting to send mail to: "+emailTo);	//no e-mail, JUST LOG IT
			}
//break;                        
		}
			   
System.out.println("\n-----To:" + emailTo);
System.out.println("-----Message:\n" + message);
System.out.println("-----From:" + from);
       } //end while
        
//        if(dbAccess != null)
            dbAccess.close();
        }
        System.exit(status);
    }
    
    /**
     * getEmailBody
     */
    String getEmailBody() {
        String users = "";
        String userIDs = amc.getUsersNotified();
        User user;
        int count = 0,endPos = 0,startPos = 0;
        //loop for all users in the Notify list
        while(true) {
            endPos = userIDs.indexOf(' ',endPos);
            if(endPos == -1)
                endPos = userIDs.length();
            if(endPos <= startPos)
                break;
            String userID = userIDs.substring(startPos,endPos);
            //convert userID to Name
            user = dbAccess.findUser(userID);
            String userName = user.getUserName();
            if(user != null) {
                if(count++ > 0) {
                    users += ", ";
                }
                users += userName;
                emailTo.add( user.getEmail());
            }
            
            //convert userID to e-mail
            startPos = ++endPos;
//System.out.println("(" + userID + ")");            
        } //end while
    if(emailTo.equals(""))
        return null;
    String requestedID = amc.getRequestedBy();
    String customerID = amc.getCustomerID();
    Customer customer = dbAccess.findCustomer(customerID);
    String customerName = "";
    if(customer != null)
        customerName = customer.getName();
    user = dbAccess.findUser(requestedID);
    String dat1 = UnixToDate(amc.getRequestDate());
    String RemindDate = UnixToDate(amc.getRemindDate()); //remove the last 8 digets "hh:mm PM"
    RemindDate = RemindDate.substring(0,RemindDate.length()-8);
    String message= "=============Add, Move, or Change Request:===========\n\n---Past Due---\n"
        + "\nBTN:                  " + amc.getBTN()
        + "\nService:              " + amc.getService()
        + "\nInitial Request by:   " + user.getUserName()
        + "\nInitial Request date: " + UnixToDate(amc.getRequestDate())
        + "\nInstructions:         " + amc.getInstructions()
        + "\nResponse:             " + amc.getResponse()
        + "\nOrder Number:         " + amc.getOrderNum()
        + "\nRemindDate:              " + RemindDate
        + "\nStatus:               " + amc.getStatus()
        + "\nLast Modified Date:   " + UnixToDate(amc.getLastModified())
        + "\nCustomer:             " + customerName
        + "\nNotify List:          " + users;
        return message;
    }
    
    /**
     ** UnixToDate
     ***/
    String UnixToDate(String seconds) {
        long millis;
        if(seconds.charAt(0) == '-')
            return "";
        DateFormat dt = DateFormat.getDateTimeInstance(DateFormat.LONG ,DateFormat.SHORT);
        Date myDate = new Date();
        //convert string to long
        try {
            millis = Long.parseLong(seconds);
        } catch (Exception e) { 
            return ""; 
        }
        millis *= 1000; //convert Unix (seconds) to Java (milli-seconds)
        myDate.setTime(millis); //build a Date object
        return dt.format(myDate);   //format
    }
    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        new reminder();
    }
    
}
