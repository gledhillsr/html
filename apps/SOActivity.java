/*
 * BTN.java
 *
 * Created on December 12, 2002, 1:43 PM
 */

import java.sql.*;
import java.net.*;
import java.util.*;
import java.io.*;
import java.text.*;
import javax.swing.*;


/**
 *
 * @author  Gledhill
 */
public class SOActivity {
    
    final static String SOACTIVITY_FILE = "Soactvty.det";
    static boolean DEBUG = false;
    static String DEBUG_BTN_STR = "8019674402733";
    boolean DEBUG(String str)  {
		return str.indexOf(DEBUG_BTN_STR) != -1 ? true : false;
	}

    final static int BTN = 0;
    final static int DATE = 1;
    final static int WTN = 4;
    final static int ORDER1 = 7;
    final static int ORDER2 = 8;
    final static int QTY = 10;
    final static int USOC = 11;
    final static int EXPLAIN1 = 12;
    final static int EXPLAIN2 = 13;
    final static int EXPLAIN3 = 14;
    final static int EXPLAIN4 = 15;
    final static int USOC_DESC = 16;
    final static int TOTAL = 21;
    final static int INSTALL = 23;
    final static int NCR  = 33;
    final static int FIELD_COUNT = 36;
    
   
//"#1"             ,"#2"      ,"#3"  ,"4","#5"        ,"#6"                ,"#7"      ,"8","#9"      ,10 ,11,12,"#13"                                             ,"#14"                      ,15 ,16 ,17 ,18 ,"#19"     ,"#20"     ,#21 ,"#22",#23 ,"#24",#25,#26,#27,#28,#29,30,31,32,33,#34,#35,#36
//"8012610527423 O","10222005","1000"," ","8012610527","69.USDA.622881..MS","09222005"," ","00000000"," ",0," ","NET CHANGE IN MONTHLY BILLING DUE TO RATE CHANGE","FROM 09-23-05 TO 10-22-05"," "," "," "," ","00000000","00000000",+.00,+5.65,+.00,+5.47," "," "," "," "," ",0,.00,0," ","F"," ",0
//	 BTN		DATE	 col3  4   WTN	        CircutID             orderDate	8   9         a	 qty b   Explain1			                    Explain2		       f   g   exp3   i   j	exp3	                                   Total      Install
    Vector col = new Vector(FIELD_COUNT);    //exact number of columns
    Vector row;
    String btn;	//col[0]
    String lastBTN;
    String invDate;   //col[1]
    int rowCount;
    private boolean exceptionError;
    static private javax.swing.JFrame frame = null;
    private Customer customer;

    DecimalFormat myFormatter;
    double Totals = 0;
    double Installs = 0;
/**** database fields *********************************/    
/*************************************/    
    GregorianCalendar date = new GregorianCalendar();
    
/** Creates a new instance of BTN */
public SOActivity(String szDir,javax.swing.JFrame frm,Customer cust,Vector SOBtns) {
    
    File[] pStr;
    int max = -1;
    int i;
    
    if(DEBUG)
        System.out.println("Processing SOActivity files in directory: "+szDir);
    frame = frm;
    customer = cust;
    myFormatter = new DecimalFormat("###,###.00");
    
    File rootDir = new File(szDir);
    File subDir;
    File file;
    RandomAccessFile SOFile = null;
    pStr = rootDir.listFiles();  //list all files or directories
    max = pStr.length;
    for(i=0; i < max; ++i) {
        subDir = pStr[i];
System.out.println("getAbsolutePath="+subDir.getAbsolutePath()+",: is directory="+subDir.isDirectory()+", name="+subDir.getName());
        file = null;
        String fileName = null;
        if(subDir.isDirectory())
            fileName = subDir.getAbsolutePath() + subDir.separator + SOACTIVITY_FILE;
        else if(subDir.getName().equalsIgnoreCase(SOACTIVITY_FILE))     //the ignore case junk was just for windows
            fileName =  szDir + subDir.separator + subDir.getName();
        if(fileName != null)
            file = new File(fileName);
        if(file != null && file.exists()) {
            try {
System.out.println("scanning file: "+file);
                SOFile = new RandomAccessFile(file, "r");
                if(SOFile != null) {
                    if(DEBUG)
                            System.out.println("opening: "+file);
                    processSORecords(SOFile, cust, SOBtns, file);
                    SOFile.close();
                }
            } catch (Exception e) {
                System.out.println("Error opening:"+e);
                new MessageDialog(frame,"Error opening file "+file+".","OK");            
//                return;
            }
        }
    }
    
    btn = "";
    exceptionError = false;
}

private boolean processSORecords(RandomAccessFile SOFile, Customer cust, Vector SOBtns, File file){
    try {
        //read file here
        int cnt = 0;
        String line = SOFile.readLine();    
        while(line != null && line.length() > 0) {
            cnt++;
            if(cnt != 1 && line.indexOf("#1",1) != 1) {
                if(DEBUG)
                    System.out.println("processing: "+line);
                String[] args = line.split(",");


                if(args.length != FIELD_COUNT) {
                //==split did not work, so do it myself!!!!
                //  check for commas within strings==
                    if(DEBUG(line))
                        System.out.println(rowCount+") = "+line);
                    int startIndex=0, lastIndex;
                    int pos = 0; 
//                        if(line.charAt(0) == '"') 
//                            startIndex++;	//skip past bogus starting " character
                    args = new String[FIELD_COUNT];
                    lastIndex = getNextComma(line,startIndex);
                    while(lastIndex != -1){
                        if(pos >= FIELD_COUNT){
                            //ERROR
                            return false;
                        }
                        if(DEBUG(line))
                            System.out.println(pos+") = "+line.substring(startIndex,lastIndex));
                            args[pos++] = line.substring(startIndex,lastIndex);
                            startIndex = lastIndex+1;
                            lastIndex = getNextComma(line,startIndex);
                        }
                        if(pos != FIELD_COUNT) {
//if correct count, then convert all ` characters back to commas in any quoted strings
//                            if(DEBUG)
                                System.out.println("Error in MONSERV.DET, processing line "+rowCount+", there were "+pos+" arguments (should be "+FIELD_COUNT+").");
		                System.out.println("Line: "+line);
                            MessageDialog md = new MessageDialog(frame,"Error in MONSERV.DET, processing line "+rowCount+", there were "+args.length+" arguments (should be "+FIELD_COUNT+").","Skip", "Abort");
                            if(md.btnSel == 2)  //2nd button is abort
                                return false;
                        }
                    } 
                
                
                
                if(args.length != FIELD_COUNT) {
    //                        if(DEBUG)
                            System.out.println("Error in "+ file.getAbsolutePath() +", processing line "+cnt+", there were "+args.length+" arguments (should be "+FIELD_COUNT+").");
                            System.out.println("Line: "+line);
                    new MessageDialog(frame,"Error in "+ file.getAbsolutePath() +", processing line "+cnt+", there were "+args.length+" arguments (should be "+FIELD_COUNT+").","OK");                                    
                } else {
                    //remove leading/trailing " (quote) if it exists, for all args
                    int i;
                    for(i=0; i < FIELD_COUNT; ++i){
                        if(args[i].charAt(0) == '"') {
                            args[i] = args[i].substring(1,args[i].length()-1);
                        }
                    }

                    if(row == null)
                        row = new Vector();
                    //I can add code here to make sure vector is sorted. using row.insertElementAt()
                    row.add(args);
                    if(DEBUG)
                        System.out.println("added BTN["+args[BTN]+"], new size= "+row.size());
    //                        SOBtns.add(args[0]);
                    int last = SOBtns.size();
     if(args[0].indexOf(" ") == (args[0].length()-2))
         args[0] = args[0].substring(0,args[0].length()-2);
                    if (last == 0) {
                        SOBtns.add(args[0]);
                    } else {
                        String strLast = (String)SOBtns.elementAt(last-1);
                        if(!strLast.equals(args[0]))
                            SOBtns.add(args[0]);
                    }

                }
    //                    System.out.println(args[BTN]+" "+args[EXPLAIN1]);
            }
            line = SOFile.readLine();
        }
    } catch (Exception e) {
        System.out.println("Error processing: "+e);
        new MessageDialog(frame,"Error processing file "+file+".","OK");            
        return false;
    }
    return true;
}

private int getNextComma(String line,int startIndex) {
    int quoteIndex,commaIndex;
    if(startIndex >= line.length())
        return -1;  //end of line
    quoteIndex = line.indexOf('"',startIndex);
    commaIndex = line.indexOf(',',startIndex);
    if (commaIndex == -1) {
        return line.length(); //end of line
    } else if (quoteIndex == -1 || commaIndex < quoteIndex) { //no quote, or comma is first
        return commaIndex;
    } else if(commaIndex < quoteIndex) { //comma is first
        return commaIndex;
    }  else
      quoteIndex = line.indexOf('"',quoteIndex+1);
      if (quoteIndex == -1) {
        //error, mis-matched quotes
        return -1;
      }
      commaIndex = line.indexOf(',',quoteIndex+1);
      if(commaIndex == -1)  //quoted character was at EOL
        commaIndex = line.length(); //end of line
      return commaIndex;
    }

public boolean writeHTMLs(DBAccess dbAccess) {
     String subDir = BTNfile.getHtmlDir(customer.getID());
     String szDir = BTNfile.homeDir+subDir;
     int idx = 0;
     int cnt = row.size();
     RandomAccessFile out = null;
     //loop for each row
     lastBTN="";
     Totals = 0.0;
     Installs = 0.0;
     //initialize any totals here
   try {
     while(idx < cnt){
         String[] args = (String[])row.elementAt(idx);
         //if new row, close old file, and open new file
         btn=args[BTN]; //this should never change within the class
         invDate=args[DATE];
//System.out.println("idx ="+btn.indexOf(" ")+"   pos="+(btn.length()-2));
         if(btn.indexOf(" ") == (btn.length()-2))
             btn = btn.substring(0,btn.length()-2);
         
         if(DEBUG)
            System.out.println("writeHTMLs - "+szDir+"\\"+btn+"_SO.html");
         
         //open (or keep open) correct BTN file
         if(!lastBTN.equals(btn)){
             //if old file exists, output footer and close
if(DEBUG)
  System.out.println("new file");
             if(out != null){
                 //close file
                 writeSOFooter(dbAccess,out);
                 out.close();                  
             }
             //create and initialize new file
             String fileName = szDir+"\\"+btn+"_SO.html";
             out = new RandomAccessFile(fileName, "rw");
             out.setLength(0);
             writeSOHeader(out);
             lastBTN = btn;
             //initialize totals here also
         }
         writeSOBody(out,idx);
         idx++;
     }
    } catch (Exception e) {
        //debug message
         System.out.println("Error: "+e+" on file :"+szDir+"\\"+btn+".");         
    }
     if (out != null) {
        try {
         //close file
            writeSOFooter(dbAccess, out);
            out.close();    //can throw IO exception
        } catch (Exception e) {
            //debug message
        }
     }
return true;    
}

public boolean updateDB() {
return true;    
}

public boolean read(ResultSet results) {
	exceptionError = false;
/*	
        btn = DBAccess.readString(results,"BTN",exceptionError);
        String str = DBAccess.readString(results,"LastUpdated",exceptionError);
        LastUpdated = 0;
        try {
            LastUpdated = Long.parseLong(str);
        } catch (Exception e) { }
        Location = DBAccess.readString(results,"Location",exceptionError);
        Division = DBAccess.readString(results,"Division",exceptionError);
        Department = DBAccess.readString(results,"Department",exceptionError);
        AMC = DBAccess.readString(results,"AMC",exceptionError);
        Description = DBAccess.readString(results,"Description",exceptionError);
        ContractExpDate = 0;
        str = DBAccess.readString(results,"ContractExpDate",exceptionError);
        try {
            ContractExpDate = Long.parseLong(str);
        } catch (Exception e) { }
        Cost = DBAccess.readDouble(results,"Cost",exceptionError);
        Review = DBAccess.readString(results,"Review",exceptionError);
        CustomerID = DBAccess.readString(results,"CustomerID",exceptionError);
        ReviewNotes = DBAccess.readString(results,"ReviewNotes",exceptionError);

//new
        prevCost 		= DBAccess.readDouble(results,"prevCost",exceptionError);
        prevUpdated 	= DBAccess.readLong(results,"prevUpdated",exceptionError);

		MonthlySavings 	= DBAccess.readDouble(results,"MonthlySavings",exceptionError);
		Product 		= DBAccess.readInt(results,"Product",exceptionError);
		MRC 			= DBAccess.readDouble(results,"MRC",exceptionError);
*/
        return exceptionError;
    }
    public String getBTN()          {        return btn;    }
/*	
    public long getLastUpdated()    {      return LastUpdated;    }
    public String getLocation()     {      return Location;    }
    public String getDivision()     {      return Division;    }
    public String getDepartment()   {      return Department;    }
    public String getAMC()          {      return AMC;    }
    public String getDescription()  {      return Description;    }
    public long getContractExpDate(){   return ContractExpDate;    }
    public double getCost()         {      return Cost;    }
    public String getReview()       {      return Review;    }
    public String getCustomerID()   {      return CustomerID;    }
    public double getPrevCost()     {      return prevCost;    }
    public long getPrevUpdated()    {      return prevUpdated;    }

    public void setLastUpdated(long millis) { LastUpdated = millis; }
    public void setCost(double amt)         { Cost = amt;    }
    public void setAMC(String str)          { AMC  = str;    }
    public void setPrevCost(double amt)     { prevCost = amt; }
    public void setPrevUpdated(long millis) { prevUpdated = millis; }
*/	
//-----------------------------------------------------
// getSQLUpdateString
//-----------------------------------------------------
    public String getSQLUpdateString() {
    String qryString = "UPDATE btn SET " +
/*	
        " LastUpdated=\"" + LastUpdated +
        "\", Location=\"" + Location +
        "\", Division=\"" + Division +
        "\", Department=\"" + Department +
        "\", AMC=\"" + AMC +
        "\", Description=\"" + Description +
        "\", ContractExpDate=\"" + ContractExpDate +
        "\", Cost=\"" + Cost +
        "\", Review=\"" + Review +
        "\", CustomerID=\"" + CustomerID +
        "\", ReviewNotes=\"" + ReviewNotes +        
        "\", prevCost=\"" + prevCost +
        "\", prevUpdated=\"" + prevUpdated +
        "\", MonthlySavings=\"" + MonthlySavings +
        "\", Product=\"" + Product +
        "\", MRC=\"" + MRC +
*/
        "\" WHERE BTN=\"" + btn + "\"";
    if(DEBUG)
        System.out.println(qryString);
    return qryString;
    }


//-----------------------------------------------------
// getSQLResetByCustomerIDString
//-----------------------------------------------------
//    public static String getSQLResetByCustomerIDString(String CustomerID) {
//        String qryString= "SELECT * FROM btn WHERE CustomerID=\""+CustomerID+"\""; //select 1 btn
//        if(DEBUG)
//            System.out.println(qryString);
//        return qryString;
//    }
    
//-----------------------------------------------------
// getSQLSelectBTNString
//-----------------------------------------------------
    public static String getSQLSelectBTNString(String btn) {
        String qryString= "SELECT * FROM btn WHERE BTN=\""+btn+"\""; //select 1 btn
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }
    
//-----------------------------------------------------
// getSQLResetString
//-----------------------------------------------------
    public static String getSQLResetString() {
        String qryString= "SELECT * FROM btn ORDER BY BTN"; //sort by default key
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }
    
//-----------------------------------------------------
// getSQLInsertString
//-----------------------------------------------------
    public String getSQLInsertString() {
        String qryString = "INSERT INTO btn "+
        " Values(\""+ btn + 
/*		
        "\",\"" + LastUpdated +
        "\",\"" + Location + 
        "\",\"" + Division +
        "\",\"" + Department +
        "\",\"" + AMC +
        "\",\"" + Description +
        "\",\"" + ContractExpDate +
        "\",\"" + Cost +
        "\",\"" + Review +
        "\",\"" + CustomerID + 
        "\",\"" + ReviewNotes + 
        "\",\"" + prevCost + 
        "\",\"" + prevUpdated + 
        "\",\"" + MonthlySavings + 
        "\",\"" + Product + 
        "\",\"" + MRC + 
*/
        "\")" ;
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }

//-----------------------------------------------------
// getSQLDeleteString
//-----------------------------------------------------
    public String getSQLDeleteString() {
        int i;
        String qryString = "DELETE FROM btn WHERE BTN=\""+btn+"\"";
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }

    public String toString() {
        return btn;
    }

/*********************************
 *  writeSummaryHeader         
 *********************************/
    public void writeSOHeader(RandomAccessFile out) throws IOException {
        Totals = 0.0;
        Installs = 0.0;
        rowCount = 0;
        String str = 
"<html>\n"+
"<head>\n"+
"<meta http-equiv=\"Content-Language\" content=\"en-us\">\n"+
"<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1252\">\n"+
"<title>Service Orders</title>\n"+
"<style type=\"text/css\">\n"+
"<!--\n"+
"body  {font-size:12; color:#000000; background-color:#ffffff  background=\"../../../ncmnthbk.jpg\"}\n"+
"table {border-width:1px; border-color:#ffffff; border-style:solid; border-collapse:collapse; border-spacing:0}\n"+
"th    {font-size:14; font-weight: bold; color:#000000; background-color:#ffffff; border-width:1px; border-color:#ffffff; border-style:solid; padding:2px}\n"+
"tr  {font-size: 9pt; font-family: Arial, serif; line-height: 11pt; color:#000000; } \n" +
".r1 {font-size: 8pt; background-color:#F0F0E8; font-family: Arial, Helvetica, sans-serif; } \n" +
".r2 {font-size: 8pt; background-color:#F7F7EF; font-family: Arial, Helvetica, sans-serif; } \n" +
".ry {font-size: 8pt; background-color:#FFFFCC; font-family: Arial, Helvetica, sans-serif; } \n" +
".rw {font-size:12; color:#000000; background-color:#ffffff; border-width:1px; border-color:#ffffff; border-style:solid; padding:1px}\n"+
"-->\n"+
"</style>\n"+
"</head>\n"+
"<body background=\"../../../ncmnthbk.jpg\">\n"+
"<p align=\"center\"><font size=5>Service Orders Activity - "+customer.getName()+"</font></p>\n"+
"<table style=\"t2\" border=0 cellpadding=0 cellspacing=0 width=\"100%\">\n"+
"  <tr class=rw>\n"+
"    <td width=\"50%\"><font size=2>Billing Telephone Number: <b>"+btn+"</b></font></td>\n"+
"    <td width=\"50%\"><font size=2>\n"+
//invDate                
"      <p align=\"right\">Dated: "+invDate.substring(0,2)+"/"+invDate.substring(2,4)+"/"+invDate.substring(4)+"</font></td>\n"+
"  </tr>\n"+
"</table>\n"+
"<p align=\"left\">&nbsp;</p>\n"+
"<table border=0 cellpadding=0 cellspacing=0 width=\"100%\">\n"+
"  <tr class=rw>\n"+
"    <td width=\"5%\"><font size=2>&nbsp;Tel&nbsp;Number&nbsp;</font></td>\n"+
"    <td width=\"5%\"><font size=2>Order&nbsp;#</font></td>\n"+
"    <td width=\"4%\" align=\"center\"><font size=2>&nbsp;Qty&nbsp;</font></td>\n"+
"    <td width=\"4%\"><font size=2>&nbsp;USOC&nbsp;</font></td>\n"+
"    <td width=\"18%\"><font size=2>&nbsp;USOC&nbsp;Desc&nbsp;</font></td>\n"+
"    <td width=\"49%\"><font size=2>&nbsp;Explanation&nbsp;</font></td>\n"+
"    <td width=\"8%\" align=\"right\"><font size=2>&nbsp;Total&nbsp;</font></td>\n"+
"    <td width=\"8%\" align=\"center\"><font size=2>&nbsp;NCR/Fract&nbsp;</font></td>\n"+
"  </tr>\n";
    out.writeBytes(str);
    }
    
/*********************************
 *  writeSummaryBody         
 *********************************/
    public void writeSOBody(RandomAccessFile out, int idx) throws IOException {
        String[] args = (String[])row.elementAt(idx);
        double tot=-1,ins=-1;
        try {
        String str=args[TOTAL]; //this should never change within the class
            tot = Double.valueOf(str.trim()).doubleValue();
            str = args[INSTALL];
            ins = Double.valueOf(str.trim()).doubleValue();
            Totals += tot;
            Installs += ins;
        } catch (Exception e) {
            System.out.println("Exception error:"+e+", evaluating either "+args[TOTAL]+" or "+args[INSTALL]);
        }
        
        rowCount++;
        String tr;
        if((rowCount % 2) == 1) {
            tr = " class=r1";
        } else {
            tr = " class=r2";
        }
        String str= 
"  <tr"+tr+">\n"+
"    <td>"+args[WTN]+"</td>\n"+
"    <td>"+args[ORDER1]+args[ORDER2]+"</td>\n"+
"    <td align=\"center\">"+args[QTY]+"</td>\n"+
"    <td>"+args[USOC]+"</td>\n"+
"    <td>"+args[USOC_DESC]+"</td>\n"+
"    <td>"+args[EXPLAIN1]+args[EXPLAIN2]+"</td>\n"+
"    <td align=\"right\">$"+myFormatter.format(tot)+"</td>\n"+
"    <td align=\"center\">"+args[NCR]+"</td>\n"+
//"    <td align=\"right\">$"+myFormatter.format(ins)+"</td>\n"+
"  </tr>\n";
                        
         out.writeBytes(str);
    }
    
/*********************************
 *  writeSummaryFooter         
 *********************************/
    public void writeSOFooter(DBAccess dbAccess, RandomAccessFile out) throws IOException {
        
        String str = 
"<tr class=rw>\n"+
" <td></td> <td></td> <td></td> <td></td> <td></td> <td></td>\n"+
" <td align=\"right\"><b>$"+myFormatter.format(Totals)+"</b></td>\n"+
" <td align=\"right\"><b>&nbsp;&nbsp;&nbsp;</b></td>\n"+
"</tr>\n"+
"</table>\n"+
"</body>\n"+
"</html>\n";
        out.writeBytes(str);
        
        
        String LocationAddress = "XYZ3";
        BTN btnDB = dbAccess.readBTN(lastBTN);

        if(btnDB == null) {
            btnDB = new BTN(lastBTN,customer.getID(),LocationAddress, date.getTimeInMillis(),0.0);
	        long millis;
	        try {
	            int year = Integer.parseInt(invDate.substring(4));
	            int month = Integer.parseInt(invDate.substring(0,2)) - 1;	//month should be 0 based
	            int dom = Integer.parseInt(invDate.substring(2,4));
	            GregorianCalendar gc = new GregorianCalendar(year, month, dom);
	            millis = gc.getTimeInMillis();
	        } catch (Exception e) {
	            millis = date.getTimeInMillis();
	System.out.println("invalid date, using today, invDate=("+invDate+") m=("+invDate.substring(0,2)+"), d=("+invDate.substring(2,4)+"), y=("+invDate.substring(4)+")");
	        }
	        btnDB.setLastUpdated(millis);
            btnDB.setServiceOrders(Totals);
//remember to for for ServiceOrders            
            if(btnDB != null) {
               dbAccess.addBTN(btnDB);
            }
        } else {
            if(customer.getID().equals(btnDB.getCustomerID())) {

		        long millis;
		        try {
		            int year = Integer.parseInt(invDate.substring(4));
		            int month = Integer.parseInt(invDate.substring(0,2)) - 1;	//month should be 0 based
		            int dom = Integer.parseInt(invDate.substring(2,4));
		            GregorianCalendar gc = new GregorianCalendar(year, month, dom);
		            millis = gc.getTimeInMillis();
		        } catch (Exception e) {
		            millis = date.getTimeInMillis();
		System.out.println("invalid date, using today, invDate=("+invDate+") m=("+invDate.substring(0,2)+"), d=("+invDate.substring(2,4)+"), y=("+invDate.substring(4)+")");
		        }
		        btnDB.setLastUpdated(millis);
                
                btnDB.setServiceOrders(Totals);
                dbAccess.updateBTN(btnDB);
            } else {
                new MessageDialog(frame,"Error, BTN "+lastBTN+" is now under a different customer.  The old customer is "
                    +btnDB.getCustomerID()+", and the new customer is "+customer.getID(),"OK");            
                
            }
        }
        
    }
}  /* end SOActivity class */
