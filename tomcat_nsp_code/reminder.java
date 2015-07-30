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

public class reminder {
	String szDay[] = {"Error", "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday" };
    String szMonth[] = {"January", "February", "March", "April", "May", "June","July", "August","September","October","November","December"};
    int status = 0;
//    DBAccess dbAccess = null;
//    Order order;
    Vector emailTo = new Vector();
	PatrolData patrol = null;
//    String from = "Steve@Gledhills.com <Automated Patrol Reminder>";
    String from = "Steve@Gledhills.com";
    String smtp="mail.gledhills.com";
/** Creates a new instance of reminder */
    public reminder(String[] args) {
		if(args.length != 1) {
			System.out.println("error, syntax is");
			System.out.println("  java reminder <resort>");
			System.exit(1);
		}
		String resort = args[0];
System.out.println("processing data for resort: "+resort);
		if(!resort.equals("Brighton") && 
		   !resort.equals("UOP") && 
		   !resort.equals("PebbleCreek") && 
		   !resort.equals("KellyCanyon") && 
		   !resort.equals("Afton") && 
		   !resort.equals("PineCreek") && 
		   !resort.equals("Pomerelle") && 
		   !resort.equals("GrandTarghee") && 
		   !resort.equals("WhitePine") && 
		   !resort.equals("RMSP") && 
		   !resort.equals("SnowKing") && 
		   !resort.equals("SnowBird") && 
		   !resort.equals("SoldierHollow")) {
			System.out.println("error, Invalid resort:(" + resort + ")");
			System.out.println("  resort muse be either Brighton, UOP, KellyCanyon, Afton, PineCreek, GrandTarghee, or SoldierHollow.  (It is case sensitive) ");
			System.exit(1);
		}
 	   patrol = new PatrolData(PatrolData.FETCH_ALL_DATA,resort);
       if(patrol != null) {

			DirectorSettings ds = patrol.readDirectorSettings();
			if(!ds.getSendReminder()) {
				System.out.println("Don't send email reminders for "+resort);
				System.exit(0);
			}
			int daysAhead = ds.getReminderDays();
			MailMan mail= new MailMan(smtp,from,"Automated Ski Patrol Reminder");
            if(mail == null) {
                System.out.println("Critical Error, could not connect to Mail system.  Aborting");
                System.exit(0);
            }
		    GregorianCalendar today = new GregorianCalendar();
System.out.println("reminder days="+daysAhead);
			long millis = today.getTimeInMillis() + (24 * 3600 * 1000 * daysAhead);
		    GregorianCalendar date = new GregorianCalendar();
			date.setTimeInMillis(millis);
//System.out.println("today="+today);
//System.out.println("date="+date);

			int month=date.get(Calendar.MONTH) + 1;
			int day = date.get(Calendar.DATE);
	 		String testDate = date.get(Calendar.YEAR)+"-";
	 		String sunday = "";
	 		String monday = "";
			if(month < 10) testDate += "0";
	 		testDate += month+"-";
			if(day < 10) testDate += "0";
	 		testDate += day;
//System.out.println("reminder days="+daysAhead);
//System.out.println("First testdate="+testDate);
//System.out.println("dayofweek="+date.get(Calendar.DAY_OF_WEEK));
			checkAndSend(testDate,date,mail,resort);



			if(date.get(Calendar.DAY_OF_WEEK) == Calendar.SATURDAY) {
//System.out.println("testing a Saturday, so also test Sunday & Monday");

			//Sunday
				millis += 24 * 3600 * 1000;
				date.setTimeInMillis(millis);
				month=date.get(Calendar.MONTH) + 1;
				day = date.get(Calendar.DATE);
		 		sunday = date.get(Calendar.YEAR)+"-";
				if(month < 10) sunday += "0";
		 		sunday += month+"-";
				if(day < 10) sunday += "0";
		 		sunday += day;
System.out.println("sunday="+sunday);
				checkAndSend(sunday,date,mail,resort);
			//Monday
				millis += 24 * 3600 * 1000;
				date.setTimeInMillis(millis);
				month=date.get(Calendar.MONTH) + 1;
				day = date.get(Calendar.DATE);
		 		monday = date.get(Calendar.YEAR)+"-";
				if(month < 10) monday += "0";
		 		monday += month+"-";
				if(day < 10) monday += "0";
		 		monday += day;
System.out.println("monday="+monday);
				checkAndSend(monday,date,mail,resort);
			}
//loop for each order
System.out.println("finished processing ALL orders");
            if(mail != null)
                mail.close();
            patrol.close();
        } //end if
        System.exit(status);
    }

private void checkAndSend(String dat,GregorianCalendar date, MailMan mail, String resort) {
int dayOfWeek = date.get(Calendar.DAY_OF_WEEK);
int month = date.get(Calendar.MONTH);
//System.out.println("dayOfWeek="+dayOfWeek+" ("+szDay[dayOfWeek]+")");
	patrol.resetAssignments();
	Assignments assignment;
    emailTo.clear();
    while((assignment=patrol.readNextAssignment()) != null) {
		String assignDate = assignment.getDateOnly();
		if(!dat.equals(assignDate)) 
			continue;

		System.out.println("Assignment="+assignment);
		String message = "Reminder\n\nYou are scheduled to Ski Patrol at "+resort+", on "+szDay[dayOfWeek]+", "+szMonth[month]+" "+date.get(Calendar.DAY_OF_MONTH)+", "+date.get(Calendar.YEAR)+" from "+
				assignment.getStartingTimeString()+" to "+
				assignment.getEndingTimeString()+".\n\nThanks, your help is greatly appreciated.\n\n";
//		if(!resort.equals("Brighton"))
			message += "Please do NOT reply to this automated reminder. \nUnless, you are NOT a member of the National Ski Patrol, and received this email accidently.";
	    emailTo.clear();
		for(int i = 0; i < assignment.getCount(); ++i) {
			String id = assignment.getPosID(i);
			System.out.print(id+" ");
			MemberData member = patrol.getMemberByID(id);
			if(member != null) {
				String em = member.getEmail();
				System.out.println(member.getFullName() + " " + em);
								//check for valid email
				if( em != null && em.length() > 6 && em.indexOf('@') > 0 && em.indexOf('.') > 0) {
//System.out.println("hack");
//emailTo.add("Steve@Gledhills.com");
					emailTo.add(em);
				}
			}
			System.out.println();
		}
		if(emailTo.size() > 0) {
//System.out.println("using smtp="+smtp+" from="+from);
			try {
	                for(int i=0; i < emailTo.size(); ++i) {
                        mail.sendMessage("Ski Patrol Shift Reminder", message, (String)emailTo.elementAt(i));
	                    System.out.println("Message:\n"+message);	//no e-mail, JUST LOG IT
	                    System.out.println("mail sent to: "+emailTo.elementAt(i));	//no e-mail, JUST LOG IT
	                 }
			} catch ( Exception ex) {
				System.out.println("error "+ex);
				System.out.println("attempting to send mail to: "+emailTo);	//no e-mail, JUST LOG IT
			} //end try/catch
	   } //anybody to email this to

	} //end loop for assignments
}


    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        new reminder(args);
    }

}
