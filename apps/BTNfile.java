/*
 * BTNfile.java
 *
 * Created on December 23, 2002, 6:20 PM
 */

import java.util.*;
import java.io.*;
import java.text.*;
/**
 *
 * @author  Gledhill
 */
public class BTNfile {
    
    public File detailInputFile;
    File newDetailOutFile;
    File summaryInputFile;
    File newSummaryOutFile;
    public static String ID;    //company ID
    public static String homeDir = "c:\\web\\CSR_home";
    private javax.swing.JFrame frame;
//    String customerName;
    public Customer customer;
    int lineCount;
    double total;
    static int convertedCount = 0;

    String LocationAddress = "";
    int qty;
    String USOC;
    String description;
    double price;
    GregorianCalendar date = new GregorianCalendar();
    String originalDate;
    String contactName;
    String dept;
    DecimalFormat myFormatter;
    
    public BTNfile(File file,javax.swing.JFrame frm,Customer cust) {
        detailInputFile = file;
        frame = frm;
        customer = cust;
        myFormatter = new DecimalFormat("###,###.00");
        total = 0.0;
    }
    public String toString() {
        String str = detailInputFile.getName().toLowerCase();
        return str.substring(0,str.indexOf(".txt"));
    }
    private boolean makeSureOutputDirExists() {
        String szHtmlDir = homeDir+getHtmlDir(ID);
        File dir = new File(szHtmlDir);
        if(dir.exists())
            return dir.isDirectory();
        //does not exist
        try {
            if(dir.mkdir())
                return true;
        } catch (Exception e) { /* fall thru on error */  }
        new MessageDialog(frame,"Error creating directory "+szHtmlDir+".","OK");            
        return false;
    }
//------
    private boolean readTokens(String line) {
    if(++lineCount == 1)
        return false;   //1st line is bogus
    int pos = 0;                    
    int endIndex;
    int beginIndex = 0;
    endIndex = line.indexOf('\t',beginIndex);
    boolean lastToken = (endIndex == -1);
    String str;
    while (true) {
        if(lastToken)
            str = line.substring(beginIndex);
        else
            str = line.substring(beginIndex,endIndex);
        str = str.trim();
        switch (pos++) {
             case 0:
                 if(str.length() == 0)
                     str = "0";
                 try { qty = Integer.parseInt(str); }
                 catch (Exception e) {
System.out.println("error reading quanity for record ("+line+")"); 
                     qty = 0;
                 }
             break;
         case 1:
             USOC = str;
             break;
         case 2:
             description = str;
             break;
         case 3:
             if(str.length() == 0)
                 str = "0.0";
             if(str.charAt(0) == '$')
                 str = str.substring(1);
             try { 
                 price = Double.parseDouble(str); 
             } catch (Exception e) {
System.out.println("error reading price for record ("+line+")"); 
                 price = 0.0;
             }
             break;
        }
        //System.out.print("("+str+") ");
        if(lastToken)
            break;
        beginIndex = endIndex+1;
        endIndex = line.indexOf('\t',beginIndex);
        lastToken = (endIndex == -1);
    } //end while
    if(lineCount <= 3 && description.substring(0,3).equals("SA ")) {
        LocationAddress = description.substring(3).trim();
//System.out.println("LocationAddress="+LocationAddress);        
        return false;
     }
     else if(lineCount <= 3 && description.substring(0,3).equals("LA ")) {
        LocationAddress = description.substring(3).trim();
//System.out.println("LocationName="+LocationAddress);
        return false;
     }
    return true;
}
    
/*********************************
 *  writeSummaryHeader         
 *********************************/
    public void writeSummaryHeader(RandomAccessFile out) throws IOException {
        String str = 
"<html>\n"+
"<head>\n"+
"<meta http-equiv=\"Content-Language\" content=\"en-us\">\n"+
"<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1252\">\n"+
"<meta name=\"ProgId\" content=\"Document\">\n"+
"<title>Carrier Sales:  Sample Customer Summary</title>\n"+
"</head>\n"+
"<body>\n"+
"<table border=\"0\" width=\"100%\" cellpadding=\"2\" cellspacing=\"1\">\n"+
"  <tr>\n"+
"    <td width=\"25%\">\n"+
"      <p align=\"left\"><b><font size=\"4\">-Summary Report-</font></b></td>\n"+
"    <td width=\"32%\"><b><font size=\"6\">"+toString()+"</font></b></td>\n"+
"    <td width=\"20%\" align=\"right\"><b><font size=\"2\">Company</font></b></td>\n"+
"    <td width=\"23%\">"+customer.getName()+"</td>\n"+
"  </tr>\n"+
"  <tr>\n"+
"    <td width=\"25%\"></td>\n"+
"    <td width=\"32%\">\n"+
"      <p align=\"center\"><font size=\"2\">Billing Telephone Number</font></td>\n"+
"    <td width=\"20%\" align=\"right\"><b><font size=\"2\">Department\\Division</font></b></td>\n"+
"    <td width=\"23%\"><font size=\"2\">"+dept+"</font></td>\n"+
"  </tr>\n"+
"  <tr>\n"+
"    <td width=\"25%\"><font size=\"2\"><b>Date Copied</b>&nbsp;&nbsp;&nbsp;"+
"      "+originalDate+"</font></td>\n"+
"    <td width=\"32%\">\n"+
"      <p align=\"center\"><font color=\"#808080\" size=\"2\">"+
" " + //"    Provided by CarrierSales.com" +
"     </font></td>\n"+
"    <td width=\"20%\" align=\"right\"><b><font size=\"2\">Contact Name</font></b></td>\n"+
"    <td width=\"23%\">"+contactName+"</td>\n"+
"  </tr>\n"+
"</table>\n"+
"<p>&nbsp;</p>\n"+
"<table border=\"1\" width=\"100%\" bordercolorlight=\"#FFFFFF\">\n"+
"  <tr>\n"+
"    <td width=\"11%\">Quantity</td>\n"+
"    <td width=\"9%\">USOC</td>\n"+
"    <td width=\"60%\">Description</td>\n"+
"    <td width=\"10%\">Price</td>\n"+
"    <td width=\"10%\">Extension</td>\n"+
"  </tr>\n"+
"</table>\n"+
"<table border=\"0\" width=\"100%\">\n";
        out.writeBytes(str);
    }
    
/*********************************
 *  writeSummaryBody         
 *********************************/
    public void writeSummaryBody(RandomAccessFile in,RandomAccessFile out) throws IOException {
        String line;
        line = in.readLine();
        int lineCount = 0;
//        total = 0; initialized in constructor
        double subTotal;
        while(line != null) {
            if(readTokens(line)) {
                subTotal = qty*price; 
                total += subTotal;
                String str= 
"  <tr>\n"+
"    <td width=\"11%\" align=\"center\">"+qty+"</td>\n"+
"    <td width=\"9%\">"+USOC+"</td>\n"+
"    <td width=\"60%\">"+description+"</td>\n"+
"    <td width=\"10%\" align=\"right\">$"+myFormatter.format(price)+"</td>\n"+
"    <td width=\"10%\" align=\"right\">$"+myFormatter.format(subTotal)+"</td>\n"+
"  </tr>\n";
                out.writeBytes(str);
//System.out.println(qty+", "+USOC+", "+description+", "+price+", total="+subTotal);
            }
            line = in.readLine(); //must be last line within while
        } //end while
    }
    
/*********************************
 *  writeSummaryFooter         
 *********************************/
    public void writeSummaryFooter(RandomAccessFile out) throws IOException {
        String str = 
"  <tr>\n"+
"    <td width=\"11%\" align=\"center\"></td>\n"+
"    <td width=\"9%\"></td>\n"+
"    <td width=\"60%\"></td>\n"+
"    <td width=\"10%\" align=\"right\"></td>\n"+
"    <td width=\"10%\" align=\"right\"><b>$"+myFormatter.format(total)+"</b></td>\n"+
"  </tr>\n"+
"</table>\n"+
"</body>\n"+
"</html>\n";
        out.writeBytes(str);
    }

/*********************************
 *  delete
 *********************************/
    public void delete(DBAccess dbAccess) {
        if(!makeSureOutputDirExists())
            return;
        RandomAccessFile sumInFile,sumOutFile;
        String rootName = toString(); 
        String szOutputDetailFile = homeDir+getHtmlDir(ID)+"\\"+rootName+"_detail.html";
        String szOutputSumFile = homeDir+getHtmlDir(ID)+"\\"+rootName+"_totals.html";
        String szInputSumFile = homeDir+getOriginalDir(ID)+"\\"+rootName+".tab";
        
        newDetailOutFile = new File(szOutputDetailFile);
        newSummaryOutFile = new File(szOutputSumFile);
//File detailInputFile; //passed in
        summaryInputFile = new File(szInputSumFile);

//make sure all files exist        
        if(!newDetailOutFile.exists()) {
            new MessageDialog(frame,"Error, "+szOutputDetailFile+" must exist.  Run \"Fix BTN problems...\" first","OK");            
            return;
        } 
        if(!newSummaryOutFile.exists()) {
            new MessageDialog(frame,"Error, "+szOutputSumFile+" must exist.  Run \"Fix BTN problems...\" first","OK");            
            return;
        }
        if(!detailInputFile.exists()) {
            new MessageDialog(frame,"Error, "+detailInputFile.toString()+" must exist.  Run \"Fix BTN problems...\" first","OK");            
            return;
        }
        if(!summaryInputFile.exists()) {
            new MessageDialog(frame,"Error, "+szInputSumFile+" must exist.  Run \"Fix BTN problems...\" first","OK");            
            return;
        }
// delete files
        try {
            newDetailOutFile.delete();
        } catch (Exception e) {
            new MessageDialog(frame,"Error deleting old file "+szOutputDetailFile+".","OK");            
            return;
        }
        try {
            newSummaryOutFile.delete();
        } catch (Exception e) {
            new MessageDialog(frame,"Error deleting old file "+szOutputSumFile+".","OK");            
            return;
        }
        try {
            detailInputFile.delete();
        } catch (Exception e) {
            new MessageDialog(frame,"Error deleting old file "+detailInputFile.toString()+".","OK");            
            return;
        }
        try {
            summaryInputFile.delete();
        } catch (Exception e) {
            new MessageDialog(frame,"Error deleting old file "+szInputSumFile+".","OK");            
            return;
        }
        BTN btn = dbAccess.readBTN(rootName);
        dbAccess.deleteBTN(btn);

}

/*********************************
 *  convertToHTML         
 *********************************/
    public void convertToHTML(DBAccess dbAccess, Vector agencies) {
        if(!makeSureOutputDirExists())
            return;
        RandomAccessFile sumInFile,sumOutFile;
        String rootName = toString(); 
        String szOutputDetailFile = homeDir+getHtmlDir(ID)+"\\"+rootName+"_detail.html";
        String szOutputSumFile = homeDir+getHtmlDir(ID)+"\\"+rootName+"_totals.html";
        String szInputSumFile = homeDir+getOriginalDir(ID)+"\\"+rootName+".tab";
        
        newDetailOutFile = new File(szOutputDetailFile);
        summaryInputFile = new File(szInputSumFile);
        newSummaryOutFile = new File(szOutputSumFile);

        date.setTimeInMillis(detailInputFile.lastModified());
        originalDate = date.get(Calendar.MONTH)+"//"+date.get(Calendar.DATE)+"//"+date.get(Calendar.YEAR);
        BTN btn = dbAccess.readBTN(rootName);
        //get agency from ID
        Agency agency;
        String agentID = customer.getAgencyID();
        contactName = "";
        int max = agencies.size();
	for(int i=0; i < max; ++i) {
		agency = (Agency)agencies.elementAt(i);
                if(agency.getID().equals(agentID)) {
                    contactName = agency.getContact();
                    break;
                }
        }
//System.out.println("hack, get contactName from customer");        
        dept = "";
        if(btn != null) {
            String div = btn.getDivision();
            String dep = btn.getDepartment();
            if(div.length() >0)
                dept = div;
            if(dep.length() > 0) {
                if (dept.length() > 0)
                    dept += " / ";
                dept += dep;
            }
        }
        
// Create Output detail file if it doesn't exist
        if(newDetailOutFile.exists()) {
            try {
            newDetailOutFile.delete();
            } catch (Exception e) {
                new MessageDialog(frame,"Error deleting old file "+szOutputDetailFile+".","OK");            
                return;
            }
        }
        if(!newDetailOutFile.exists()) {
            try {
            newDetailOutFile.createNewFile();
            } catch (Exception e) {
                new MessageDialog(frame,"Error creating file "+szOutputDetailFile+".","OK");            
                return;
            }
        }
++convertedCount;
System.out.println(convertedCount+") Converting "+toString()+" to static HTML files.");
//write detail file                 
        if(newDetailOutFile.exists()) {
            try {
                FileWriter outDetail = new FileWriter(newDetailOutFile);
                FileReader inDetail  = new FileReader(detailInputFile);
//System.out.println("converting "+detailInputFile.getAbsolutePath()+" to NEW "+newDetailOutFile.getAbsolutePath());
                writeDetailHeader(outDetail);
                writeDetailBody(inDetail, outDetail);
                writeDetailFooter(outDetail);
                outDetail.close();
                inDetail.close();
            } catch (Exception e) {
                new MessageDialog(frame,"Error writing file "+newDetailOutFile+".","OK");            
                return;
            }
        } //end writing detail file
//write Summary
// Create Output detail file if it doesn't exist
        if(newSummaryOutFile.exists()) {
            try {
                newSummaryOutFile.delete();
            } catch (Exception e) {
                new MessageDialog(frame,"Error deleting old file "+szOutputSumFile+".","OK");            
                return;
            }
        }
        if(!newSummaryOutFile.exists()) {
            try {
            newSummaryOutFile.createNewFile();
            } catch (Exception e) {
                new MessageDialog(frame,"Error creating file "+szOutputSumFile+".","OK");            
                return;
            }
        }
        if(newSummaryOutFile.exists()) {
            try {
//System.out.println("   and "+summaryInputFile.getAbsolutePath()+" to NEW "+newSummaryOutFile.getAbsolutePath());
                try {
                    sumInFile = new RandomAccessFile(summaryInputFile, "r");
                } catch (Exception e) {
                    new MessageDialog(frame,"Error opening file "+szInputSumFile+".","OK");            
                    return;
                }
                try {
                    sumOutFile = new RandomAccessFile(newSummaryOutFile, "rw");
                    sumOutFile.setLength(0);
                } catch (Exception e) {
                    new MessageDialog(frame,"Error creating file "+szOutputSumFile+".","OK");            
                    return;
                }
                writeSummaryHeader(sumOutFile);
                writeSummaryBody(sumInFile,sumOutFile);
                writeSummaryFooter(sumOutFile);
                sumInFile.close();
                sumOutFile.close();
            } catch (Exception e) {
                new MessageDialog(frame,"Error writing file "+newDetailOutFile+".","OK");            
                return;
            }
        } //end writing suymmary file
        if(btn == null) {
            btn = new BTN(rootName,customer.getID(),LocationAddress,date.getTimeInMillis(),total);
            if(btn != null)
                dbAccess.addBTN(btn);
        }
        else {
            if(customer.getID().equals(btn.getCustomerID())) {
				//save data into "prev"...
				btn.setPrevUpdated(btn.getLastUpdated());
				btn.setPrevCost(btn.getCost());

                btn.setLastUpdated(date.getTimeInMillis());
                btn.setCost(total);
                dbAccess.updateBTN(btn);
            }
            else {
                new MessageDialog(frame,"Error, BTN "+rootName+" is now under a different customer.  The old customer is "
                    +btn.getCustomerID()+", and the new customer is "+customer.getID(),"OK");            
            }
        }
    }
    private void writeDetailHeader(FileWriter outDetail) throws IOException {
        String str = 
"<html>\n"+
"<head>\n"+
"<title>Carrier Sales:  Detail BTN for "+customer.getName()+"</title>\n"+
"</head>\n"+
"<body>\n" +
"<table border=\"0\" width=\"100%\" cellpadding=\"2\" cellspacing=\"1\">\n" +
"  <tr>\n" +
"    <td width=\"25%\">\n" +
"      <p align=\"left\"><b><font size=\"4\">-Detailed Report-</font></b></td>\n" +
"    <td width=\"32%\"><b><font size=\"6\">"+toString()+"</font></b></td>\n" +
"    <td width=\"20%\" align=\"right\"><b><font size=\"2\">Company</font></b></td>\n" +
"    <td width=\"23%\">"+customer.getName()+"</td>\n" +
"  </tr>\n" +
"  <tr>\n" +
"    <td width=\"25%\"></td>\n" +
"    <td width=\"32%\">\n" +
"      <p align=\"center\"><font size=\"2\">Billing Telephone Number</font></td>\n" +
"    <td width=\"20%\" align=\"right\"><b><font size=\"2\">Department\\Division</font></b></td>\n" +
"    <td width=\"23%\"><font size=\"2\">"+dept+"</font></td>\n" +
"  </tr>\n" +
"  <tr>\n" +
"    <td width=\"25%\"><font size=\"2\"><b>Date Copied</b>&nbsp;&nbsp;&nbsp;\n" +
"      "+originalDate+"</font></td>\n" +
"    <td width=\"32%\">\n" +
"      <p align=\"center\"><font color=\"#808080\" size=\"2\">Provided by\n" +
"      CarrierSales.com</font></td>\n" +
"    <td width=\"20%\" align=\"right\"><b><font size=\"2\">Contact Name</font></b></td>\n" +
"    <td width=\"23%\"><font size=\"2\">"+contactName+"</font></td>\n" +
"  </tr>\n" +
"</table>\n" +
"<p>&nbsp;</p>\n" +
"<PRE>";
        outDetail.write(str);
    }
    private void writeDetailBody(FileReader inDetail, FileWriter outDetail)  throws IOException {
        char[] buf = new char[5000];
        int bytesRead;
        while((bytesRead = inDetail.read(buf,0, 5000)) > 0) {
            outDetail.write(buf,0,bytesRead);
        }

    }
    private void writeDetailFooter(FileWriter outDetail)  throws IOException {
        String str = 
"</PRE>\n"+
"</body>\n"+
"</html>\n";
        outDetail.write(str);
    }
    static public String getOriginalDir(String id) {
        ID = id;
        return "\\"+ID+"\\BTN\\original";
    }
    
    static public String getHtmlDir(String id) {
        ID = id;
        return "\\"+ID+"\\BTN\\html";
    }
    
    static public boolean isBtnFile(File f) {
        String szFile = f.getName().toLowerCase();
        return (szFile.endsWith(".txt"));
    }
}
