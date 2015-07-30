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

    static boolean DEBUG = false;
    private Driver drv;
    private Connection connection;
    private ResultSet agentResults;
    private ResultSet orderResults;

    /** Creates a new instance of DBAccess */
    public DBAccess(javax.swing.JFrame frm, String table) {
        try  {
            if(DEBUG)
                System.out.println("getting JData driver");
            drv = (Driver) Class.forName("org.gjt.mm.mysql.Driver").newInstance();
            if(DEBUG)
                System.out.println("driver ="+drv);
        }
        catch (Exception e) {
            System.out.println("Cannot load the driver, reason:"+e.toString());
            System.out.println("Most likely the Java class path is incorrect.");
        }

        try {
          // Change MyDSN, myUsername and myPassword to your specific DSN
            String IP_ADDRESS = "127.0.0.1";	//localhost
            String jdbcURL = "jdbc:mysql://localhost/" + table;

            if(DEBUG)
                System.out.println("creating Connection");
            connection =java.sql.DriverManager.getConnection(jdbcURL,"root","my_password");
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
//                    new MessageDialog(frame,"exception in readString e="+e.toString(),"OK");
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
/*       Agent              */
/*****************************/

    public void resetAgent(){
        String qryString = Agent.getSQLResetString();
		try {
	            agentResults = connection.prepareStatement(qryString).executeQuery();
	    	}  catch (Exception e) {
	            displayException("Error reseting Agent table query:"+e.getMessage());
		} //end try
    } //end resetAgent
	
    public Agent nextAgent() {
        Agent na = null;
        try {
          if(agentResults.next()) {
            na = new Agent();
            na.read(agentResults);
           }
        } catch (Exception e) {
            displayException("Cannot read Agent, reason:"+e.toString());
            return null;
        }
        return na;
    } //end nextAgent

    public Agent getAgent(String first, String last) {
        Agent agent = null;
        String qryString = Agent.getSQLSelectString(first,last);
        try {
          agentResults = connection.prepareStatement(qryString).executeQuery();
		  if(agentResults.next()){
            agent = new Agent();
            agent.read(agentResults);
           }
        } catch (Exception e) {
            displayException("Cannot read Agent, reason:"+e.toString());
            return null;
        }
        return agent;
    } //end getAgent

    /*****************************/
    /*       Order              */
    /*****************************/

        public void resetOrder(){
            String qryString = Order.getSQLResetString();
            try {
                orderResults = connection.prepareStatement(qryString).executeQuery();
                }  catch (Exception e) {
                displayException("Error resetting Order table query:"+e.getMessage());
            } //end try
        }
        public Order nextOrder() {
            Order order = null;
            try {
              if(orderResults.next()) {
                order = new Order();
                order.read(orderResults);
               }
            } catch (Exception e) {
                displayException("Cannot read Agent, reason:"+e.toString());
                return null;
            }
            return order;
        }

    public boolean updateOrder(Order order) {
        String qryString= order.getSQLUpdateString();
        try {
            PreparedStatement sAssign = connection.prepareStatement(qryString);
            ResultSet rs = sAssign.executeQuery();
        } catch (Exception e) {
            displayException("Cannot update Order, reason:"+e.toString());
            return false;
        }
        return true;
    }
/***
    public boolean updateAgent(Agent agent) {
        String qryString= agent.getSQLUpdateString();
        try {
            PreparedStatement sAssign = connection.prepareStatement(qryString);
            ResultSet rs = sAssign.executeQuery();
        } catch (Exception e) {
            displayException("Cannot update Agent, reason:"+e.toString());
            return false;
        }
        return true;
    }
    public boolean addAgent(Agent agent) {
        String qryString= agent.getSQLInsertString();
        try {
            PreparedStatement sAssign = connection.prepareStatement(qryString);
            ResultSet rs = sAssign.executeQuery();
        } catch (Exception e) {
            displayException("Cannot add Agent, reason:"+e.toString());
            return false;
        }
        return true;
    }
    public boolean deleteAgent(Agent agent) {
        String qryString= agent.getSQLDeleteString();
        try {
            PreparedStatement sAssign = connection.prepareStatement(qryString);
            ResultSet rs = sAssign.executeQuery();
//System.out.println(rs);
        } catch (Exception e) {
            displayException("Cannot delete Agent, reason:"+e.toString());
            return false;
        }
        return true;
    }
****/
    public static void displayException(String errStr) {
        System.out.println(errStr);
//        if(frame != null)
//           new MessageDialog(frame,errStr,"OK");
    }
}
