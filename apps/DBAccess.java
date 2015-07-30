/*
 * DBAccess.java
 *
 * Created on December 12, 2002, 3:02 PM
 */

import java.sql.*;
import java.net.*;
import javax.swing.*;
/**
 *
 * @author  Gledhill
 */
public class DBAccess {
    static private javax.swing.JFrame frame = null;

    static boolean DEBUG = true;
    private Driver drv;
    private Connection connection;
    private PreparedStatement agencyStatement;
    private ResultSet agencyResults;
    private ResultSet customerResults;
    private ResultSet userResults;
    private ResultSet btnResults;
    private ResultSet amcResults;
//            String IP_ADDRESS = "64.32.145.130";	//MegaPath (DON'T USE)
    public static String IP_ADDRESS = "localhost";	//localhost
    public static String USER = "root";
    public static String PASSWORD = "my_password";
    

    /** Creates a new instance of DBAccess */
    public DBAccess(javax.swing.JFrame frm) {
        if(frm != null)
            frame = frm;
        try  {
            if(DEBUG)
                System.out.println("getting JData driver");	
            drv = (Driver) Class.forName("org.gjt.mm.mysql.Driver").newInstance();
            if(DEBUG)
                System.out.println("driver ="+drv);	
        }
        catch (Exception e) {
              displayException("Cannot load the driver, reason:"+e.toString()+"Most likely the Java class path is incorrect.");
//            77System.out.println("Cannot load the driver, reason:"+e.toString());
//            System.out.println("Most likely the Java class path is incorrect.");
        }

        try {
          // Change MyDSN, myUsername and myPassword to your specific DSN
//...            String jdbcURL = "jdbc:mysql://localhost/CSRDATA";
            String jdbcURL = "jdbc:mysql://"+IP_ADDRESS+"/csrdata";
//            String jdbcURL = "jdbc:JDataConnect://"+IP_ADDRESS+"/CSRDATA:root:my_password";
            if(DEBUG)
                System.out.println("creating Connection to: "+jdbcURL);	  
//            connection =java.sql.DriverManager.getConnection(jdbcURL);
            connection =java.sql.DriverManager.getConnection(jdbcURL,USER,PASSWORD);
            if(DEBUG)
                System.out.println("connection = "+connection);	  
        } //end try
        catch (Exception e) {
            displayException("2) Error connecting or reading table:"+e.getMessage());
        } //end catch
    }

    public void close() {
	    try {
                if(DEBUG) {
                    System.out.println("******************");
                    System.out.println("DBAccess.close()");		
                    System.out.println("******************");
                }
                connection.close(); //let it close in finalizer ??
            }  catch (Exception e) {
      		displayException("(close) Error connecting or reading table on close:"+e.getMessage());
	    } //end try
    }
    /**************/
    /* readString */
    /**************/
    public static String readString(ResultSet results, String tag, boolean err) {
            String str = null;
            try {
                    str= results.getString(tag);
            }  catch (Exception e) {
                    displayException("exception in readString e="+e);
                    new MessageDialog(frame,"exception in readString e="+e.toString(),"OK");
                    err = true;
            } //end try

            return str;
    }
    /**************/
    /* readInt */
    /**************/
    public static int readInt(ResultSet results, String tag, boolean err) {
            int value = 0;
            try {
                    value = results.getInt(tag);
            }  catch (Exception e) {
                    displayException("exception in readInt e="+e);
                    err = true;
            } //end try

            return value;
    }
                
    /**************/
    /* readLong   */
    /**************/
    public static long readLong(ResultSet results, String tag, boolean err) {
            long value = 0;
            try {
                    value = results.getLong(tag);
            }  catch (Exception e) {
                    displayException("exception in readLong e="+e);
                    err = true;
            } //end try

            return value;
    }
                
    /**************/
    /* readDouble */
    /**************/
    public static double readDouble(ResultSet results, String tag, boolean err) {
            double value = 0.0;
            try {
                    value = results.getDouble(tag);
            }  catch (Exception e) {
                    displayException("exception in readDouble e="+e);
                    err = true;
            } //end try

            return value;
    }
        
    
/*****************************/
/*       Agency              */
/*****************************/
    public void resetAgency(){
        String qryString = Agency.getSQLResetString();
	try {
            agencyResults = connection.prepareStatement(qryString).executeQuery();
    	}  catch (Exception e) {
            displayException("Error reseting Agency table query:"+e.getMessage());
	} //end try
    }
    public Agency nextAgency() {
        Agency ag = null;
        try {
          if(agencyResults.next()) {
            ag = new Agency();
            ag.read(agencyResults);
           }
        } catch (Exception e) {
            displayException("Cannot read Agency, reason:"+e.toString());
            return null;
        }
        return ag;
    }
    public boolean updateAgency(Agency agency) {
        String qryString= agency.getSQLUpdateString();
        try {
            PreparedStatement sAssign = connection.prepareStatement(qryString);
            ResultSet rs = sAssign.executeQuery();
        } catch (Exception e) {
            displayException("Cannot update Agency, reason:"+e.toString());
            return false;
        }
        return true;
    }
    public boolean addAgency(Agency agency) {
        String qryString= agency.getSQLInsertString();
        try {
            PreparedStatement sAssign = connection.prepareStatement(qryString);
            ResultSet rs = sAssign.executeQuery();
        } catch (Exception e) {
            displayException("Cannot add Agency, reason:"+e.toString());
            return false;
        }
        return true;
    }
    public boolean deleteAgency(Agency agency) {
        String qryString= agency.getSQLDeleteString();
        try {
            PreparedStatement sAssign = connection.prepareStatement(qryString);
            ResultSet rs = sAssign.executeQuery();
//System.out.println(rs);            
        } catch (Exception e) {
            displayException("Cannot delete Agency, reason:"+e.toString());
            return false;
        }
        return true;
    }
/*****************************/
/*      Customer             */
/*****************************/
    public void resetCustomer(){
        String qryString = Customer.getSQLResetString();
	try {
            customerResults = connection.prepareStatement(qryString).executeQuery();
    	}  catch (Exception e) {
            displayException("Error reseting Customer table query:"+e.getMessage());
	} //end try
    }

    public Customer findCustomer(String id) {
        Customer customer = null;
        String qryString = customer.getSQLFindString(id);
	try { 
            customerResults = connection.prepareStatement(qryString).executeQuery();
    	}  catch (Exception e) {
            displayException("Error finding User table query:"+e.getMessage());
	} //end try

        try {
          if(customerResults.next()) {
            customer = new Customer();
            customer.read(customerResults);
           }
        } catch (Exception e) {
            displayException("Cannot read Customer, reason:"+e.toString());
            return null;
        }
        return customer;
    }
    
    public Customer nextCustomer() {
        Customer customer = null;
        try {
          if(customerResults.next()) {
            customer = new Customer();
            customer.read(customerResults);
           }
        } catch (Exception e) {
            displayException("Cannot read Customer, reason:"+e.toString());
            return null;
        }
        return customer;
    }
    public boolean updateCustomer(Customer customer) {
        String qryString= customer.getSQLUpdateString();
        try {
            PreparedStatement sAssign = connection.prepareStatement(qryString);
            ResultSet rs = sAssign.executeQuery();
        } catch (Exception e) {
            displayException("Cannot update Customer, reason:"+e.toString());
            return false;
        }
        return true;
    }
    public boolean addCustomer(Customer customer) {
        String qryString= customer.getSQLInsertString();
        try {
            PreparedStatement sAssign = connection.prepareStatement(qryString);
            ResultSet rs = sAssign.executeQuery();
        } catch (Exception e) {
            displayException("Cannot add Customer, reason:"+e.toString());
            return false;
        }
        return true;
    }
    public boolean deleteCustomer(Customer customer) {
        String qryString= customer.getSQLDeleteString();
        try {
            PreparedStatement sAssign = connection.prepareStatement(qryString);
            ResultSet rs = sAssign.executeQuery();
        } catch (Exception e) {
            displayException("Cannot delete Customer, reason:"+e.toString());
            return false;
        }
        return true;
    }
/*****************************/
/*      User                 */
/*****************************/
    public void resetUser(){
        String qryString = User.getSQLResetString();
	try { 
            userResults = connection.prepareStatement(qryString).executeQuery();
    	}  catch (Exception e) {
            displayException("Error reseting User table query:"+e.getMessage());
	} //end try
    }
    
    public User findUser(String id) {
        User user = null;
        String qryString = User.getSQLFindString(id);
	try { 
            userResults = connection.prepareStatement(qryString).executeQuery();
    	}  catch (Exception e) {
            displayException("Error finding User table query:"+e.getMessage());
	} //end try

        try {
          if(userResults.next()) {
            user = new User();
            user.read(userResults);
           }
        } catch (Exception e) {
            displayException("Cannot read Login, reason:"+e.toString());
            return null;
        }
        return user;
    }
    
    public User nextUser() {
        User user = null;
        try {
          if(userResults.next()) {
            user = new User();
            user.read(userResults);
           }
        } catch (Exception e) {
            displayException("Cannot read Login, reason:"+e.toString());
            return null;
        }
        return user;
    }

    public boolean updateUser(User user) {
        String qryString= user.getSQLUpdateString();
        try {
            PreparedStatement sAssign = connection.prepareStatement(qryString);
            ResultSet rs = sAssign.executeQuery();
        } catch (Exception e) {
            displayException("Cannot update Login, reason:"+e.toString());
            return false;
        }
        return true;
    }
    public boolean addUser(User user) {
        String qryString= user.getSQLInsertString();
        try {
            PreparedStatement sAssign = connection.prepareStatement(qryString);
            ResultSet rs = sAssign.executeQuery();
        } catch (Exception e) {
            displayException("Cannot add Login, reason:"+e.toString());
            return false;
        }
        return true;
    }
    public boolean deleteUser(User user) {
        String qryString= user.getSQLDeleteString();
        try {
            PreparedStatement sAssign = connection.prepareStatement(qryString);
            ResultSet rs = sAssign.executeQuery();
        } catch (Exception e) {
            displayException("Cannot delete Login, reason:"+e.toString());
            return false;
        }
        return true;
    }
    
    
/************************/
/*      BTN             */
/************************/
    public void resetBTN(){
        String qryString = BTN.getSQLResetString();
	try {
            btnResults = connection.prepareStatement(qryString).executeQuery();
    	}  catch (Exception e) {
            displayException("Error reseting BTN table query:"+e.getMessage());
	} //end try
    }

    public boolean BTNexists(String szBtn) {
        String qryString = BTN.getSQLSelectBTNString(szBtn);
        try {
            btnResults = connection.prepareStatement(qryString).executeQuery();
//            PreparedStatement sAssign = connection.prepareStatement(qryString);
//            ResultSet rs = sAssign.executeQuery();
        } catch (Exception e) {
//            displayException("Cannot read BTN, reason:"+e.toString());
            return false;
        }
        return true;
    }
    
    public void resetBTNbyCustomerID(String customerID){
        String qryString = BTN.getSQLResetByCustomerIDString(customerID);
	try {
            btnResults = connection.prepareStatement(qryString).executeQuery();
    	}  catch (Exception e) {
            displayException("Error reseting BTN table query:"+e.getMessage());
	} //end try
    }
    public BTN nextBTN() {
        BTN btn = null;
        try {
          if(btnResults.next()) {
            btn = new BTN();
            btn.read(btnResults);
           }
        } catch (Exception e) {
            displayException("Cannot read BTN, reason:"+e.toString());
            return null;
        }
        return btn;
    }
    public BTN readBTN(String btn_key) {
        BTN btn = null;
        String qryString = BTN.getSQLSelectBTNString(btn_key);
        try {
            btnResults = connection.prepareStatement(qryString).executeQuery();
//            PreparedStatement sAssign = connection.prepareStatement(qryString);
//            ResultSet rs = sAssign.executeQuery();
        } catch (Exception e) {
            displayException("Cannot read BTN, reason:"+e.toString());
            return null;
        }
        return nextBTN();
    }
    public boolean updateBTN(BTN btn) {
        String qryString= btn.getSQLUpdateString();
        try {
            PreparedStatement sAssign = connection.prepareStatement(qryString);
            ResultSet rs = sAssign.executeQuery();
        } catch (Exception e) {
            displayException("Cannot update BTN, reason:"+e.toString()+" qryString="+qryString);
            return false;
        }
        return true;
    }
    public boolean addBTN(BTN btn) {
        String qryString= btn.getSQLInsertString();
        try {
            PreparedStatement sAssign = connection.prepareStatement(qryString);
            ResultSet rs = sAssign.executeQuery();
        } catch (Exception e) {
            displayException("Cannot add BTN, reason:"+e.toString()+"  qryString="+qryString);
            return false;
        }
        return true;
    }
    public boolean deleteBTN(BTN btn) {
        String qryString= btn.getSQLDeleteString();
        try {
            PreparedStatement sAssign = connection.prepareStatement(qryString);
            ResultSet rs = sAssign.executeQuery();
        } catch (Exception e) {
            displayException("Cannot delete BTN, reason:"+e.toString());
            return false;
        }
        return true;
    }
/*****************************/
/*      AMC                 */
/*****************************/
    public void resetAMC(String filterBTN){
        String qryString = AMC.getSQLResetString(filterBTN);
	try { 
            amcResults = connection.prepareStatement(qryString).executeQuery();
    	}  catch (Exception e) {
            displayException("Error reseting AMC table query:"+e.getMessage());
	} //end try
    }
    
    public void resetAMC_by_PastDue(){
        String qryString = AMC.getSQLPastDueString();
	try { 
            amcResults = connection.prepareStatement(qryString).executeQuery();
    	}  catch (Exception e) {
            displayException("Error reseting AMC table query:"+e.getMessage());
	} //end try
    }
    
    public AMC nextAMC() {
        AMC amc = null;
        try {
          if(amcResults.next()) {
            amc = new AMC();
            amc.read(amcResults);
           }
        } catch (Exception e) {
            displayException("Cannot read AMC, reason:"+e.toString());
            return null;
        }
        return amc;
    }
    
    public boolean updateAMC(AMC amc) {
        String qryString= amc.getSQLUpdateString();
        try {
            PreparedStatement sAssign = connection.prepareStatement(qryString);
            ResultSet rs = sAssign.executeQuery();
        } catch (Exception e) {
            displayException("Cannot update AMC, reason:"+e.toString());
            return false;
        }
        return true;
    }
    public static void displayException(String errStr) {
        System.out.println(errStr);
        if(frame != null)
           new MessageDialog(frame,errStr,"OK");
    }
}
