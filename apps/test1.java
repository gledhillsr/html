/*
 * test1.java
 *
 * Created on December 12, 2002, 11:22 AM
 */

/**
 *
 * @author  Gledhill
 */
import java.util.*;
import java.io.*;

public class test1 extends javax.swing.JFrame {
    private javax.swing.JFrame frame;
    public Vector agencies = new Vector();
    public Vector customers = new Vector();
    public Vector users = new Vector();
    public Vector btnFiles = new Vector();    
    private int agencyPos = 0;
    private int customerPos = 0;
    private int userPos = 0;
    private int btnCustomerPos = 0;
    private DBAccess dbAccess = null;
    private SOActivity serviceOrder = null;
    private MonthlyService monthService = null;
        
    /****************************/
    /*   initialize main        */
    /****************************/
    public test1() {
        frame = this;
        initComponents();

        jButton3.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                InitSQLActionPerformed(evt);
            }
        });
        
        
        
        initMaintaince();
        dbAccess = new DBAccess(frame);
        //initialize tab windows
        initAgency();
        initCustomer();
        initUser();
        tabPassword.setTitleAt(0,"Login/Password");
        tabPassword.setTitleAt(1,"Customer");
        tabPassword.setTitleAt(2,"Agency");
        tabPassword.setTitleAt(3,"BTN");
        tabPassword.setTitleAt(4,"Maintaince");

    }
    private void initMaintaince() {
        IPAddrField.setText(DBAccess.IP_ADDRESS);
        SQLUserField.setText(DBAccess.USER);
        SQLPasswordField.setText(DBAccess.PASSWORD);
    }
    
    /****************************/
    /*   initialize Agency Tab  */
    /****************************/
    private void updateAgencyList(int pos) {
        ExistingAgencies.setText("Existing Agencies: "+agencies.size());
        AgencyList.setListData(agencies);
//System.out.println("selecting index pos "+pos);        
        if(pos >= 0) {
            AgencyList.setSelectedIndex(pos);
        }
        AgencyList.repaint();
    }
    
    private void initAgency(){
        agencies = new Vector();
        initNewAgency();    //initialize input text fields
//read records from database
        Agency agency;
        dbAccess.resetAgency();
        agency = dbAccess.nextAgency();
        while(agency != null) {
            agencies.add(agency);
            agency = dbAccess.nextAgency();
        }
        UpdateAgencyComboBox();     //on agency tab (not this tab)
//display Agency ComboBox
        updateAgencyList((agencies.size() >0) ? 0 : -1); //update list box (can reset textfields)
    }
    /******************************/
    /*   initialize Customer Tab  */
    /******************************/
    private void updateCustomerList(int pos) {
        ExistingCustomers.setText("Existing Customers: "+customers.size());
        CustomerList.setListData(customers);
        if(pos >= 0) {
            CustomerList.setSelectedIndex(pos);
        }
        CustomerList.repaint();
        //customer list on BTN Maintaince tab
        BtnCustomerList.setListData(customers);
        if(customers.size() > 0)
            BtnCustomerList.setSelectedIndex(0);
        BtnCustomerList.repaint();
    }
       
    private void initCustomer(){
        customers = new Vector();
        AllowArchiveAccess.addItem("No");
        AllowArchiveAccess.addItem("Yes");
//        ExistingCustomers.setText("Existing Customers: ");
        initNewCustomer();
//read records from database
        Customer customer;
        dbAccess.resetCustomer();
        customer = dbAccess.nextCustomer();
        while(customer != null) {
            customers.add(customer);
            customer = dbAccess.nextCustomer();
        }
        UpdateCustomerComboBox();     //on login tab (not this tab)
//display Customer List
        updateCustomerList((customers.size() >0) ? 0 : -1); //update list box (can reset textfields)
        
    }
    /******************************/
    /*   initialize Login Tab  */
    /******************************/
    private void updateUserList(int pos) {
        existingUsers.setText("Existing Users: "+users.size());
        loginList.setListData(users);
//System.out.println("selecting index pos "+pos);        
        if(pos >= 0) {
            loginList.setSelectedIndex(pos);
        }
        loginList.repaint();
    }
       
    private void initUser(){
        users = new Vector();
        LoginAdministrator.addItem("No");
        LoginAdministrator.addItem("Yes");
        NotifyLogin.addItem("No");
        NotifyLogin.addItem("Yes");
        NotifyChange.addItem("No");
        NotifyChange.addItem("Yes");
        initNewUser();
//read records from database
        User user;
        dbAccess.resetUser();
        user = dbAccess.nextUser();
        while(user != null) {
            users.add(user);
            user = dbAccess.nextUser();
        }
//display User List
        updateUserList((users.size() >0) ? 0 : -1); //update list box (can reset textfields)
        
    }
    /** This method is called from within the constructor to
     * initialize the form.
     * WARNING: Do NOT modify this code. The content of this method is
     * always regenerated by the Form Editor.
     */
    // <editor-fold defaultstate="collapsed" desc=" Generated Code ">//GEN-BEGIN:initComponents
    private void initComponents() {
        jMenuBar1 = new javax.swing.JMenuBar();
        jMenu1 = new javax.swing.JMenu();
        jLabel1 = new javax.swing.JLabel();
        tabPassword = new javax.swing.JTabbedPane();
        jPanel1 = new javax.swing.JPanel();
        jScrollPane1 = new javax.swing.JScrollPane();
        loginList = new javax.swing.JList();
        existingUsers = new javax.swing.JLabel();
        jPanel6 = new javax.swing.JPanel();
        jLabel2 = new javax.swing.JLabel();
        tfLoginName = new javax.swing.JTextField();
        DeleteUser = new javax.swing.JButton();
        AddSaveUser = new javax.swing.JButton();
        jLabel4 = new javax.swing.JLabel();
        jLabel5 = new javax.swing.JLabel();
        jLabel6 = new javax.swing.JLabel();
        jLabel7 = new javax.swing.JLabel();
        jLabel8 = new javax.swing.JLabel();
        tfPassword = new javax.swing.JTextField();
        tfLoginDept = new javax.swing.JTextField();
        tfLoginUserName = new javax.swing.JTextField();
        tfAccessCount = new javax.swing.JTextField();
        CustomerComboBox = new javax.swing.JComboBox();
        CreateNewUser = new javax.swing.JButton();
        jLabel30 = new javax.swing.JLabel();
        LoginAdministrator = new javax.swing.JComboBox();
        jLabel31 = new javax.swing.JLabel();
        NotifyChange = new javax.swing.JComboBox();
        jLabel32 = new javax.swing.JLabel();
        tfLoginEmail = new javax.swing.JTextField();
        jLabel33 = new javax.swing.JLabel();
        jLabel34 = new javax.swing.JLabel();
        tfLoginPhone = new javax.swing.JTextField();
        tfLoginMobile = new javax.swing.JTextField();
        jLabel35 = new javax.swing.JLabel();
        NotifyLogin = new javax.swing.JComboBox();
        btnExit = new javax.swing.JButton();
        jLabel10 = new javax.swing.JLabel();
        jPanel3 = new javax.swing.JPanel();
        jLabel15 = new javax.swing.JLabel();
        jScrollPane3 = new javax.swing.JScrollPane();
        CustomerList = new javax.swing.JList();
        ExistingCustomers = new javax.swing.JLabel();
        jPanel8 = new javax.swing.JPanel();
        jLabel17 = new javax.swing.JLabel();
        CreateNewCustomer = new javax.swing.JButton();
        jLabel18 = new javax.swing.JLabel();
        jLabel19 = new javax.swing.JLabel();
        tfCustomerID = new javax.swing.JTextField();
        tfCustomerName = new javax.swing.JTextField();
        AgencyComboBox = new javax.swing.JComboBox();
        DeleteCustomer = new javax.swing.JButton();
        AddSaveCustomer = new javax.swing.JButton();
        jLabel37 = new javax.swing.JLabel();
        taCustomerAddress = new javax.swing.JTextArea();
        jLabel16 = new javax.swing.JLabel();
        AllowArchiveAccess = new javax.swing.JComboBox();
        jPanel9 = new javax.swing.JPanel();
        CustomerBtnCount = new javax.swing.JLabel();
        jPanel12 = new javax.swing.JPanel();
        CustomerBtnCount1 = new javax.swing.JLabel();
        jLabel39 = new javax.swing.JLabel();
        jLabel3 = new javax.swing.JLabel();
        CustomerBtnStorage = new javax.swing.JLabel();
        jLabel42 = new javax.swing.JLabel();
        jPanel13 = new javax.swing.JPanel();
        ArchiveBtnCount = new javax.swing.JLabel();
        ArchiveBtnStorage = new javax.swing.JLabel();
        jPanel14 = new javax.swing.JPanel();
        CustomerBtnCount3 = new javax.swing.JLabel();
        jLabel41 = new javax.swing.JLabel();
        jLabel28 = new javax.swing.JLabel();
        jLabel43 = new javax.swing.JLabel();
        CustomerExit = new javax.swing.JButton();
        jPanel2 = new javax.swing.JPanel();
        jScrollPane2 = new javax.swing.JScrollPane();
        AgencyList = new javax.swing.JList();
        ExistingAgencies = new javax.swing.JLabel();
        jLabel11 = new javax.swing.JLabel();
        AgencyExit = new javax.swing.JButton();
        jPanel7 = new javax.swing.JPanel();
        btnNewAgency = new javax.swing.JButton();
        DeleteAgency = new javax.swing.JButton();
        SaveAddAgency = new javax.swing.JButton();
        jLabel12 = new javax.swing.JLabel();
        jLabel13 = new javax.swing.JLabel();
        jLabel14 = new javax.swing.JLabel();
        tfAgencyID = new javax.swing.JTextField();
        tfAgencyName = new javax.swing.JTextField();
        taAgencyAddress = new javax.swing.JTextArea();
        jLabel9 = new javax.swing.JLabel();
        jLabel20 = new javax.swing.JLabel();
        jLabel21 = new javax.swing.JLabel();
        tfContactName = new javax.swing.JTextField();
        tfContactPhone = new javax.swing.JTextField();
        tfContactFax = new javax.swing.JTextField();
        jLabel22 = new javax.swing.JLabel();
        tf2ndContact = new javax.swing.JTextField();
        jLabel27 = new javax.swing.JLabel();
        tfPluralName = new javax.swing.JTextField();
        jPanel4 = new javax.swing.JPanel();
        jLabel23 = new javax.swing.JLabel();
        jScrollPane4 = new javax.swing.JScrollPane();
        BtnList = new javax.swing.JList();
        jButton4 = new javax.swing.JButton();
        BtnCount = new javax.swing.JLabel();
        btnStoredAt = new javax.swing.JLabel();
        jPanel11 = new javax.swing.JPanel();
        jLabel29 = new javax.swing.JLabel();
        jScrollPane5 = new javax.swing.JScrollPane();
        BtnCustomerList = new javax.swing.JList();
        jPanel10 = new javax.swing.JPanel();
        jLabel25 = new javax.swing.JLabel();
        ConvertAllBTN = new javax.swing.JButton();
        jLabel36 = new javax.swing.JLabel();
        jLabel26 = new javax.swing.JLabel();
        btnCompany = new javax.swing.JLabel();
        jPanel15 = new javax.swing.JPanel();
        DeleteSelectedBTN = new javax.swing.JButton();
        jLabel24 = new javax.swing.JLabel();
        fixBtnButton = new javax.swing.JButton();
        BtnList1 = new javax.swing.JList();
        jScrollPane7 = new javax.swing.JScrollPane();
        SOList = new javax.swing.JList();
        SOCount = new javax.swing.JLabel();
        jPanel5 = new javax.swing.JPanel();
        jButton1 = new javax.swing.JButton();
        jButton2 = new javax.swing.JButton();
        jLabel40 = new javax.swing.JLabel();
        IPAddrField = new javax.swing.JTextField();
        jLabel38 = new javax.swing.JLabel();
        jLabel44 = new javax.swing.JLabel();
        SQLUserField = new javax.swing.JTextField();
        SQLPasswordField = new javax.swing.JTextField();
        jButton3 = new javax.swing.JButton();
        jPanel16 = new javax.swing.JPanel();

        jMenu1.setText("Menu");
        jMenuBar1.add(jMenu1);

        addWindowListener(new java.awt.event.WindowAdapter() {
            public void windowClosing(java.awt.event.WindowEvent evt) {
                exitForm(evt);
            }
        });

        jLabel1.setFont(new java.awt.Font("Dialog", 1, 18));
        jLabel1.setHorizontalAlignment(javax.swing.SwingConstants.CENTER);
        jLabel1.setText("Carrier Sales BTN Database Maintaince");
        getContentPane().add(jLabel1, java.awt.BorderLayout.NORTH);

        tabPassword.setToolTipText("Add/Change User Password Access");
        jPanel1.setLayout(new org.netbeans.lib.awtextra.AbsoluteLayout());

        loginList.addListSelectionListener(new javax.swing.event.ListSelectionListener() {
            public void valueChanged(javax.swing.event.ListSelectionEvent evt) {
                loginListValueChanged(evt);
            }
        });

        jScrollPane1.setViewportView(loginList);

        jPanel1.add(jScrollPane1, new org.netbeans.lib.awtextra.AbsoluteConstraints(390, 60, 190, 350));

        existingUsers.setText("Existing users: 117");
        jPanel1.add(existingUsers, new org.netbeans.lib.awtextra.AbsoluteConstraints(430, 40, -1, -1));

        jPanel6.setLayout(new org.netbeans.lib.awtextra.AbsoluteLayout());

        jPanel6.setBorder(javax.swing.BorderFactory.createBevelBorder(javax.swing.border.BevelBorder.RAISED));
        jLabel2.setText("Login Name:");
        jLabel2.setToolTipText("The Login Name/Password combination MUST be unique");
        jPanel6.add(jLabel2, new org.netbeans.lib.awtextra.AbsoluteConstraints(50, 60, -1, -1));

        tfLoginName.setText("jTextField1");
        tfLoginName.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                tfLoginNameActionPerformed(evt);
            }
        });

        jPanel6.add(tfLoginName, new org.netbeans.lib.awtextra.AbsoluteConstraints(140, 60, 160, -1));

        DeleteUser.setText("Delete User");
        DeleteUser.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                DeleteUserActionPerformed(evt);
            }
        });

        jPanel6.add(DeleteUser, new org.netbeans.lib.awtextra.AbsoluteConstraints(50, 420, -1, -1));

        AddSaveUser.setText("Save / Add");
        AddSaveUser.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                AddSaveUserActionPerformed(evt);
            }
        });

        jPanel6.add(AddSaveUser, new org.netbeans.lib.awtextra.AbsoluteConstraints(180, 420, -1, -1));

        jLabel4.setText("Password:");
        jPanel6.add(jLabel4, new org.netbeans.lib.awtextra.AbsoluteConstraints(60, 90, -1, -1));

        jLabel5.setText("Customer Name:");
        jPanel6.add(jLabel5, new org.netbeans.lib.awtextra.AbsoluteConstraints(40, 120, -1, -1));

        jLabel6.setText("Title/Department:");
        jPanel6.add(jLabel6, new org.netbeans.lib.awtextra.AbsoluteConstraints(30, 270, -1, -1));

        jLabel7.setText("Users Name:");
        jPanel6.add(jLabel7, new org.netbeans.lib.awtextra.AbsoluteConstraints(50, 150, -1, -1));

        jLabel8.setText("AccessCount:");
        jPanel6.add(jLabel8, new org.netbeans.lib.awtextra.AbsoluteConstraints(50, 390, -1, -1));

        tfPassword.setText("jTextField2");
        jPanel6.add(tfPassword, new org.netbeans.lib.awtextra.AbsoluteConstraints(140, 90, 160, -1));

        tfLoginDept.setText("jTextField4");
        jPanel6.add(tfLoginDept, new org.netbeans.lib.awtextra.AbsoluteConstraints(140, 270, 160, -1));

        tfLoginUserName.setText("jTextField5");
        jPanel6.add(tfLoginUserName, new org.netbeans.lib.awtextra.AbsoluteConstraints(140, 150, 200, -1));

        tfAccessCount.setText("jTextField1");
        jPanel6.add(tfAccessCount, new org.netbeans.lib.awtextra.AbsoluteConstraints(140, 390, 160, -1));

        jPanel6.add(CustomerComboBox, new org.netbeans.lib.awtextra.AbsoluteConstraints(140, 120, 200, -1));

        CreateNewUser.setText("Create New User");
        CreateNewUser.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                CreateNewUserActionPerformed(evt);
            }
        });

        jPanel6.add(CreateNewUser, new org.netbeans.lib.awtextra.AbsoluteConstraints(90, 20, -1, -1));

        jLabel30.setText("Notify about Logins:");
        jPanel6.add(jLabel30, new org.netbeans.lib.awtextra.AbsoluteConstraints(10, 210, 120, -1));

        LoginAdministrator.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                LoginAdministratorActionPerformed(evt);
            }
        });

        jPanel6.add(LoginAdministrator, new org.netbeans.lib.awtextra.AbsoluteConstraints(140, 180, 70, -1));

        jLabel31.setText("Notify about changes:");
        jPanel6.add(jLabel31, new org.netbeans.lib.awtextra.AbsoluteConstraints(10, 240, -1, -1));

        jPanel6.add(NotifyChange, new org.netbeans.lib.awtextra.AbsoluteConstraints(140, 240, 70, -1));

        jLabel32.setText("email:");
        jPanel6.add(jLabel32, new org.netbeans.lib.awtextra.AbsoluteConstraints(90, 300, -1, -1));

        tfLoginEmail.setText("jTextField1");
        jPanel6.add(tfLoginEmail, new org.netbeans.lib.awtextra.AbsoluteConstraints(140, 300, 210, -1));

        jLabel33.setText("Phone:");
        jPanel6.add(jLabel33, new org.netbeans.lib.awtextra.AbsoluteConstraints(90, 330, -1, -1));

        jLabel34.setText("Mobile:");
        jPanel6.add(jLabel34, new org.netbeans.lib.awtextra.AbsoluteConstraints(90, 360, -1, -1));

        tfLoginPhone.setText("jTextField2");
        jPanel6.add(tfLoginPhone, new org.netbeans.lib.awtextra.AbsoluteConstraints(140, 330, 160, -1));

        tfLoginMobile.setText("jTextField5");
        jPanel6.add(tfLoginMobile, new org.netbeans.lib.awtextra.AbsoluteConstraints(140, 360, 160, -1));

        jLabel35.setText("Administrator");
        jPanel6.add(jLabel35, new org.netbeans.lib.awtextra.AbsoluteConstraints(40, 180, -1, -1));

        jPanel6.add(NotifyLogin, new org.netbeans.lib.awtextra.AbsoluteConstraints(140, 210, 70, -1));

        jPanel1.add(jPanel6, new org.netbeans.lib.awtextra.AbsoluteConstraints(10, 60, 370, 460));

        btnExit.setText("Exit");
        btnExit.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnExitActionPerformed(evt);
            }
        });

        jPanel1.add(btnExit, new org.netbeans.lib.awtextra.AbsoluteConstraints(440, 470, -1, -1));

        jLabel10.setFont(new java.awt.Font("Dialog", 1, 18));
        jLabel10.setText("Login / Password Maintaince");
        jPanel1.add(jLabel10, new org.netbeans.lib.awtextra.AbsoluteConstraints(70, 20, -1, -1));

        tabPassword.addTab("tab1", jPanel1);

        jPanel3.setLayout(new org.netbeans.lib.awtextra.AbsoluteLayout());

        jLabel15.setFont(new java.awt.Font("Dialog", 1, 18));
        jLabel15.setText("Customer Maintaince");
        jPanel3.add(jLabel15, new org.netbeans.lib.awtextra.AbsoluteConstraints(100, 20, -1, -1));

        CustomerList.addListSelectionListener(new javax.swing.event.ListSelectionListener() {
            public void valueChanged(javax.swing.event.ListSelectionEvent evt) {
                CustomerListValueChanged(evt);
            }
        });

        jScrollPane3.setViewportView(CustomerList);

        jPanel3.add(jScrollPane3, new org.netbeans.lib.awtextra.AbsoluteConstraints(410, 63, 180, 390));

        ExistingCustomers.setText("Existing Customers: 23");
        jPanel3.add(ExistingCustomers, new org.netbeans.lib.awtextra.AbsoluteConstraints(430, 40, -1, -1));

        jPanel8.setLayout(new org.netbeans.lib.awtextra.AbsoluteLayout());

        jPanel8.setBorder(javax.swing.BorderFactory.createBevelBorder(javax.swing.border.BevelBorder.RAISED));
        jLabel17.setText("Customer ID:");
        jPanel8.add(jLabel17, new org.netbeans.lib.awtextra.AbsoluteConstraints(60, 70, -1, -1));

        CreateNewCustomer.setText("Create New Customer");
        CreateNewCustomer.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                CreateNewCustomerActionPerformed(evt);
            }
        });

        jPanel8.add(CreateNewCustomer, new org.netbeans.lib.awtextra.AbsoluteConstraints(80, 20, -1, -1));

        jLabel18.setText("Customer Name:");
        jPanel8.add(jLabel18, new org.netbeans.lib.awtextra.AbsoluteConstraints(40, 110, -1, -1));

        jLabel19.setText("Agency Name:");
        jPanel8.add(jLabel19, new org.netbeans.lib.awtextra.AbsoluteConstraints(60, 200, -1, -1));

        tfCustomerID.setText("jTextField3");
        jPanel8.add(tfCustomerID, new org.netbeans.lib.awtextra.AbsoluteConstraints(150, 70, 100, -1));

        tfCustomerName.setText("jTextField4");
        jPanel8.add(tfCustomerName, new org.netbeans.lib.awtextra.AbsoluteConstraints(150, 110, 170, -1));

        jPanel8.add(AgencyComboBox, new org.netbeans.lib.awtextra.AbsoluteConstraints(150, 200, 160, -1));

        DeleteCustomer.setText("Delete Customer");
        DeleteCustomer.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                DeleteCustomerActionPerformed(evt);
            }
        });

        jPanel8.add(DeleteCustomer, new org.netbeans.lib.awtextra.AbsoluteConstraints(40, 390, -1, -1));

        AddSaveCustomer.setText("Add / Save");
        AddSaveCustomer.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                AddSaveCustomerActionPerformed(evt);
            }
        });

        jPanel8.add(AddSaveCustomer, new org.netbeans.lib.awtextra.AbsoluteConstraints(190, 390, -1, -1));

        jLabel37.setText("Address:");
        jPanel8.add(jLabel37, new org.netbeans.lib.awtextra.AbsoluteConstraints(80, 150, -1, -1));

        jPanel8.add(taCustomerAddress, new org.netbeans.lib.awtextra.AbsoluteConstraints(150, 140, 167, 47));

        jLabel16.setText("Allow Archive Access:");
        jPanel8.add(jLabel16, new org.netbeans.lib.awtextra.AbsoluteConstraints(20, 240, -1, -1));

        jPanel8.add(AllowArchiveAccess, new org.netbeans.lib.awtextra.AbsoluteConstraints(150, 240, 60, -1));

        jPanel9.setLayout(new org.netbeans.lib.awtextra.AbsoluteLayout());

        jPanel9.setBorder(javax.swing.BorderFactory.createTitledBorder("Active BTN"));
        CustomerBtnCount.setText("171");
        jPanel9.add(CustomerBtnCount, new org.netbeans.lib.awtextra.AbsoluteConstraints(100, 30, -1, -1));

        jPanel12.setLayout(new org.netbeans.lib.awtextra.AbsoluteLayout());

        jPanel12.setBorder(javax.swing.BorderFactory.createTitledBorder("Active BTN"));
        CustomerBtnCount1.setText("Count: 17");
        jPanel12.add(CustomerBtnCount1, new org.netbeans.lib.awtextra.AbsoluteConstraints(10, 20, -1, -1));

        jLabel39.setText("Storage: 123MB");
        jPanel12.add(jLabel39, new org.netbeans.lib.awtextra.AbsoluteConstraints(10, 50, -1, -1));

        jPanel9.add(jPanel12, new org.netbeans.lib.awtextra.AbsoluteConstraints(20, 290, 120, 90));

        jLabel3.setText("Count:");
        jPanel9.add(jLabel3, new org.netbeans.lib.awtextra.AbsoluteConstraints(40, 30, -1, -1));

        CustomerBtnStorage.setText("222");
        jPanel9.add(CustomerBtnStorage, new org.netbeans.lib.awtextra.AbsoluteConstraints(100, 50, -1, -1));

        jLabel42.setText("Storage (MB):");
        jPanel9.add(jLabel42, new org.netbeans.lib.awtextra.AbsoluteConstraints(10, 50, -1, -1));

        jPanel8.add(jPanel9, new org.netbeans.lib.awtextra.AbsoluteConstraints(20, 280, 150, 90));

        jPanel13.setLayout(new org.netbeans.lib.awtextra.AbsoluteLayout());

        jPanel13.setBorder(javax.swing.BorderFactory.createTitledBorder("ARCHIVE BTN"));
        ArchiveBtnCount.setText("2343");
        jPanel13.add(ArchiveBtnCount, new org.netbeans.lib.awtextra.AbsoluteConstraints(100, 30, -1, -1));

        ArchiveBtnStorage.setText("423");
        jPanel13.add(ArchiveBtnStorage, new org.netbeans.lib.awtextra.AbsoluteConstraints(100, 50, -1, -1));

        jPanel14.setLayout(new org.netbeans.lib.awtextra.AbsoluteLayout());

        jPanel14.setBorder(javax.swing.BorderFactory.createTitledBorder("Active BTN"));
        CustomerBtnCount3.setText("Count: 17");
        jPanel14.add(CustomerBtnCount3, new org.netbeans.lib.awtextra.AbsoluteConstraints(10, 20, -1, -1));

        jLabel41.setText("Storage: 123MB");
        jPanel14.add(jLabel41, new org.netbeans.lib.awtextra.AbsoluteConstraints(10, 50, -1, -1));

        jPanel13.add(jPanel14, new org.netbeans.lib.awtextra.AbsoluteConstraints(20, 290, 120, 90));

        jLabel28.setText("Storage (MB):");
        jPanel13.add(jLabel28, new org.netbeans.lib.awtextra.AbsoluteConstraints(10, 50, -1, -1));

        jLabel43.setText("Count:");
        jPanel13.add(jLabel43, new org.netbeans.lib.awtextra.AbsoluteConstraints(50, 30, -1, -1));

        jPanel8.add(jPanel13, new org.netbeans.lib.awtextra.AbsoluteConstraints(180, 280, 150, 90));

        jPanel3.add(jPanel8, new org.netbeans.lib.awtextra.AbsoluteConstraints(50, 70, 340, 430));

        CustomerExit.setText("Exit");
        CustomerExit.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                CustomerExitActionPerformed(evt);
            }
        });

        jPanel3.add(CustomerExit, new org.netbeans.lib.awtextra.AbsoluteConstraints(460, 470, -1, -1));

        tabPassword.addTab("tab3", jPanel3);

        jPanel2.setLayout(new org.netbeans.lib.awtextra.AbsoluteLayout());

        AgencyList.addListSelectionListener(new javax.swing.event.ListSelectionListener() {
            public void valueChanged(javax.swing.event.ListSelectionEvent evt) {
                AgencyListValueChanged(evt);
            }
        });

        jScrollPane2.setViewportView(AgencyList);

        jPanel2.add(jScrollPane2, new org.netbeans.lib.awtextra.AbsoluteConstraints(420, 73, 160, 320));

        ExistingAgencies.setText("Existing Agencies: 13");
        jPanel2.add(ExistingAgencies, new org.netbeans.lib.awtextra.AbsoluteConstraints(440, 40, -1, -1));

        jLabel11.setFont(new java.awt.Font("Dialog", 1, 18));
        jLabel11.setText("Agency Maintaince");
        jPanel2.add(jLabel11, new org.netbeans.lib.awtextra.AbsoluteConstraints(120, 20, -1, -1));

        AgencyExit.setText("Exit");
        AgencyExit.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                AgencyExitActionPerformed(evt);
            }
        });

        jPanel2.add(AgencyExit, new org.netbeans.lib.awtextra.AbsoluteConstraints(450, 470, -1, -1));

        jPanel7.setLayout(new org.netbeans.lib.awtextra.AbsoluteLayout());

        jPanel7.setBorder(new javax.swing.border.SoftBevelBorder(javax.swing.border.BevelBorder.RAISED));
        btnNewAgency.setText("Create New Agency");
        btnNewAgency.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnNewAgencyActionPerformed(evt);
            }
        });

        jPanel7.add(btnNewAgency, new org.netbeans.lib.awtextra.AbsoluteConstraints(130, 10, -1, -1));

        DeleteAgency.setText("Delete Agency");
        DeleteAgency.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                DeleteAgencyActionPerformed(evt);
            }
        });

        jPanel7.add(DeleteAgency, new org.netbeans.lib.awtextra.AbsoluteConstraints(50, 370, -1, -1));

        SaveAddAgency.setText("Save / Add");
        SaveAddAgency.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                SaveAddAgencyActionPerformed(evt);
            }
        });

        jPanel7.add(SaveAddAgency, new org.netbeans.lib.awtextra.AbsoluteConstraints(220, 370, -1, -1));

        jLabel12.setText("Agency ID:");
        jPanel7.add(jLabel12, new org.netbeans.lib.awtextra.AbsoluteConstraints(80, 50, -1, -1));

        jLabel13.setText("Agency Name:");
        jPanel7.add(jLabel13, new org.netbeans.lib.awtextra.AbsoluteConstraints(60, 80, -1, -1));

        jLabel14.setText("Agency Address:");
        jPanel7.add(jLabel14, new org.netbeans.lib.awtextra.AbsoluteConstraints(40, 150, -1, -1));

        tfAgencyID.setText("jTextField1");
        jPanel7.add(tfAgencyID, new org.netbeans.lib.awtextra.AbsoluteConstraints(150, 50, 120, -1));

        tfAgencyName.setText("jTextField2");
        jPanel7.add(tfAgencyName, new org.netbeans.lib.awtextra.AbsoluteConstraints(150, 80, 190, -1));

        taAgencyAddress.setText("11532 S. Cherry Hill");
        jPanel7.add(taAgencyAddress, new org.netbeans.lib.awtextra.AbsoluteConstraints(150, 150, 190, 70));

        jLabel9.setText("Contact Name:");
        jPanel7.add(jLabel9, new org.netbeans.lib.awtextra.AbsoluteConstraints(50, 240, -1, -1));

        jLabel20.setText("Telephone:");
        jPanel7.add(jLabel20, new org.netbeans.lib.awtextra.AbsoluteConstraints(70, 270, -1, -1));

        jLabel21.setText("Fax:");
        jPanel7.add(jLabel21, new org.netbeans.lib.awtextra.AbsoluteConstraints(110, 300, -1, -1));

        tfContactName.setText("jTextField1");
        jPanel7.add(tfContactName, new org.netbeans.lib.awtextra.AbsoluteConstraints(150, 240, 190, -1));

        tfContactPhone.setText("jTextField2");
        jPanel7.add(tfContactPhone, new org.netbeans.lib.awtextra.AbsoluteConstraints(150, 270, 120, -1));

        tfContactFax.setText("jTextField5");
        jPanel7.add(tfContactFax, new org.netbeans.lib.awtextra.AbsoluteConstraints(150, 300, 120, -1));

        jLabel22.setText("Secondary Contact:");
        jPanel7.add(jLabel22, new org.netbeans.lib.awtextra.AbsoluteConstraints(30, 330, -1, -1));

        tf2ndContact.setText("jTextField6");
        jPanel7.add(tf2ndContact, new org.netbeans.lib.awtextra.AbsoluteConstraints(150, 330, 180, -1));

        jLabel27.setText("Agency's Plural Name:");
        jPanel7.add(jLabel27, new org.netbeans.lib.awtextra.AbsoluteConstraints(10, 110, -1, -1));

        tfPluralName.setText("jTextField1");
        jPanel7.add(tfPluralName, new org.netbeans.lib.awtextra.AbsoluteConstraints(150, 110, 190, -1));

        jPanel2.add(jPanel7, new org.netbeans.lib.awtextra.AbsoluteConstraints(40, 70, 360, 430));

        tabPassword.addTab("tab2", jPanel2);

        jPanel4.setLayout(new org.netbeans.lib.awtextra.AbsoluteLayout());

        jLabel23.setFont(new java.awt.Font("Dialog", 1, 18));
        jLabel23.setText("Billing Telephone Number (BTN) Maintaince");
        jPanel4.add(jLabel23, new org.netbeans.lib.awtextra.AbsoluteConstraints(90, 30, -1, -1));

        jScrollPane4.setViewportView(BtnList);

        jPanel4.add(jScrollPane4, new org.netbeans.lib.awtextra.AbsoluteConstraints(440, 100, 140, 170));

        jButton4.setText("Exit");
        jButton4.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                jButton4ActionPerformed(evt);
            }
        });

        jPanel4.add(jButton4, new org.netbeans.lib.awtextra.AbsoluteConstraints(250, 480, -1, -1));

        BtnCount.setText("Monthly Service = 999");
        jPanel4.add(BtnCount, new org.netbeans.lib.awtextra.AbsoluteConstraints(430, 80, 160, -1));

        btnStoredAt.setFont(new java.awt.Font("Courier New", 1, 14));
        btnStoredAt.setText("Original BTN's are stored in ./<CustomerID>/BTN/original");
        jPanel4.add(btnStoredAt, new org.netbeans.lib.awtextra.AbsoluteConstraints(70, 460, 470, -1));

        jPanel11.setLayout(new org.netbeans.lib.awtextra.AbsoluteLayout());

        jPanel11.setBorder(javax.swing.BorderFactory.createBevelBorder(javax.swing.border.BevelBorder.RAISED));
        jLabel29.setText("Select Customer to get a list of their BTN's");
        jPanel11.add(jLabel29, new org.netbeans.lib.awtextra.AbsoluteConstraints(60, 50, -1, -1));

        BtnCustomerList.addListSelectionListener(new javax.swing.event.ListSelectionListener() {
            public void valueChanged(javax.swing.event.ListSelectionEvent evt) {
                BtnCustomerListValueChanged(evt);
            }
        });

        jScrollPane5.setViewportView(BtnCustomerList);

        jPanel11.add(jScrollPane5, new org.netbeans.lib.awtextra.AbsoluteConstraints(230, 80, 150, 260));

        jPanel10.setLayout(new org.netbeans.lib.awtextra.AbsoluteLayout());

        jPanel10.setBorder(javax.swing.BorderFactory.createBevelBorder(javax.swing.border.BevelBorder.RAISED));
        jLabel25.setFont(new java.awt.Font("Dialog", 1, 14));
        jLabel25.setText("Convert BTN's to HTML");
        jPanel10.add(jLabel25, new org.netbeans.lib.awtextra.AbsoluteConstraints(10, 10, 170, -1));

        ConvertAllBTN.setText("Process BTN's");
        ConvertAllBTN.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                ConvertAllBTNActionPerformed(evt);
            }
        });

        jPanel10.add(ConvertAllBTN, new org.netbeans.lib.awtextra.AbsoluteConstraints(10, 70, 170, -1));

        jLabel36.setText("And update database with totals");
        jPanel10.add(jLabel36, new org.netbeans.lib.awtextra.AbsoluteConstraints(10, 30, 170, 20));

        jPanel11.add(jPanel10, new org.netbeans.lib.awtextra.AbsoluteConstraints(20, 220, 200, 120));

        jLabel26.setFont(new java.awt.Font("Dialog", 1, 18));
        jLabel26.setText("BTN's for:");
        jPanel11.add(jLabel26, new org.netbeans.lib.awtextra.AbsoluteConstraints(40, 20, -1, -1));

        btnCompany.setFont(new java.awt.Font("Dialog", 1, 18));
        btnCompany.setText("xyzzy");
        jPanel11.add(btnCompany, new org.netbeans.lib.awtextra.AbsoluteConstraints(140, 20, -1, -1));

        jPanel15.setLayout(new org.netbeans.lib.awtextra.AbsoluteLayout());

        jPanel15.setBorder(javax.swing.BorderFactory.createBevelBorder(javax.swing.border.BevelBorder.RAISED));
        DeleteSelectedBTN.setText("Delete selected BTN(s)");
        DeleteSelectedBTN.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                DeleteSelectedBTNActionPerformed(evt);
            }
        });

        jPanel15.add(DeleteSelectedBTN, new org.netbeans.lib.awtextra.AbsoluteConstraints(10, 70, 170, -1));

        jLabel24.setFont(new java.awt.Font("Dialog", 1, 14));
        jLabel24.setText("Delete BTN's");
        jPanel15.add(jLabel24, new org.netbeans.lib.awtextra.AbsoluteConstraints(50, 50, -1, -1));

        fixBtnButton.setText("Zero Service Totals");
        fixBtnButton.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                fixBtnButtonActionPerformed(evt);
            }
        });

        jPanel15.add(fixBtnButton, new org.netbeans.lib.awtextra.AbsoluteConstraints(10, 20, 170, -1));

        jPanel11.add(jPanel15, new org.netbeans.lib.awtextra.AbsoluteConstraints(20, 90, 200, 110));

        jPanel4.add(jPanel11, new org.netbeans.lib.awtextra.AbsoluteConstraints(20, 80, 400, 360));

        jPanel4.add(BtnList1, new org.netbeans.lib.awtextra.AbsoluteConstraints(0, 0, -1, -1));

        jScrollPane7.setViewportView(SOList);

        jPanel4.add(jScrollPane7, new org.netbeans.lib.awtextra.AbsoluteConstraints(440, 308, 140, 120));

        SOCount.setText("Service Order BTN's = 99");
        jPanel4.add(SOCount, new org.netbeans.lib.awtextra.AbsoluteConstraints(440, 290, 140, -1));

        tabPassword.addTab("tab4", jPanel4);

        jPanel5.setLayout(new org.netbeans.lib.awtextra.AbsoluteLayout());

        jButton1.setText("Fix AMC record counts for all BTN's");
        jButton1.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                jButton1ActionPerformed(evt);
            }
        });

        jPanel5.add(jButton1, new org.netbeans.lib.awtextra.AbsoluteConstraints(60, 70, 240, -1));

        jButton2.setText("Exit");
        jButton2.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                jButton2ActionPerformed(evt);
            }
        });

        jPanel5.add(jButton2, new org.netbeans.lib.awtextra.AbsoluteConstraints(230, 450, -1, -1));

        jLabel40.setText("SQL User:");
        jPanel5.add(jLabel40, new org.netbeans.lib.awtextra.AbsoluteConstraints(200, 260, -1, -1));

        IPAddrField.setText("jTextField1");
        jPanel5.add(IPAddrField, new org.netbeans.lib.awtextra.AbsoluteConstraints(270, 220, 120, -1));

        jLabel38.setText("IP Address:");
        jPanel5.add(jLabel38, new org.netbeans.lib.awtextra.AbsoluteConstraints(200, 220, -1, -1));

        jLabel44.setText("SQL Password:");
        jPanel5.add(jLabel44, new org.netbeans.lib.awtextra.AbsoluteConstraints(170, 300, -1, -1));

        SQLUserField.setText("jTextField2");
        jPanel5.add(SQLUserField, new org.netbeans.lib.awtextra.AbsoluteConstraints(270, 260, 120, -1));

        SQLPasswordField.setText("jTextField3");
        jPanel5.add(SQLPasswordField, new org.netbeans.lib.awtextra.AbsoluteConstraints(270, 300, 120, -1));

        jButton3.setText("Init SQL Connection");
        jPanel5.add(jButton3, new org.netbeans.lib.awtextra.AbsoluteConstraints(220, 340, 150, -1));

        jPanel5.add(jPanel16, new org.netbeans.lib.awtextra.AbsoluteConstraints(100, 110, 220, 70));

        tabPassword.addTab("tab5", jPanel5);

        getContentPane().add(tabPassword, java.awt.BorderLayout.CENTER);

        pack();
    }// </editor-fold>//GEN-END:initComponents

    private void loginListValueChanged(javax.swing.event.ListSelectionEvent evt) {//GEN-FIRST:event_loginListValueChanged
        javax.swing.JList list = (javax.swing.JList)evt.getSource();
        userPos = list.getSelectedIndex();
        
        if(list.getModel().getSize() > 0) {
            displayUser((User)list.getSelectedValue());
//System.out.println("AgencyListValueChanged - Changed button text to Save");
            AddSaveUser.setText("Save Changes");
            tfLoginName.setEditable(false);
            AddSaveUser.repaint();
        }
    }//GEN-LAST:event_loginListValueChanged

    private void tfLoginNameActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_tfLoginNameActionPerformed
// Add your handling code here:
    }//GEN-LAST:event_tfLoginNameActionPerformed

    private void InitSQLActionPerformed(java.awt.event.ActionEvent evt) { 
        DBAccess.IP_ADDRESS = IPAddrField.getText();
        DBAccess.USER = SQLUserField.getText();
        DBAccess.PASSWORD = SQLPasswordField.getText();
        
        dbAccess = new DBAccess(frame);
        //initialize tab windows
        initAgency();
        initCustomer();
        initUser();

        
        System.out.println("InitSQLActionPerformed done");
// Add your handling code here:
    }                                           


    private void DeleteUserActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_DeleteUserActionPerformed
        int size = users.size();
        if(size > 0) {
            int pos = userPos;
            User user = (User)users.elementAt(userPos);
            if(dbAccess.deleteUser(user)) {
                users.remove(userPos--);
                pos=0;
                if(size == 1) {
                    DeleteUser.setEnabled(false);
                    DeleteUser.repaint();
                    initNewUser();
                }
                loginList.setListData(users);   //note, this deletes all old data
                updateUserList(pos);
//                UpdateCustomerComboBox();
            }
        }
    }//GEN-LAST:event_DeleteUserActionPerformed

    private void AddSaveUserActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_AddSaveUserActionPerformed
        String id = tfLoginName.getText();
        id = id.trim();
        if(id.equals("")) {
            new MessageDialog(frame,"Error, invalid 'Login Name'.","OK");
            return;
        }
        String password = tfPassword.getText();
        Customer customer = (Customer)CustomerComboBox.getSelectedItem();
        String customerID = customer.getID();
        String userName = tfLoginUserName.getText();
        int admin = LoginAdministrator.getSelectedIndex();
        int notifyLogin = NotifyLogin.getSelectedIndex();
        int notifyChange = NotifyChange.getSelectedIndex();
        String dept = tfLoginDept.getText();
        String email = tfLoginEmail.getText();
        String phone = tfLoginPhone.getText();
        String mobile = tfLoginMobile.getText();
        String strCnt = tfAccessCount.getText();
        strCnt = strCnt.trim();
        int count = 0;
        if(strCnt.length() == 0)
            strCnt = "0";
        try {
            count = Integer.parseInt(strCnt);
        } catch (Exception e) {
            new MessageDialog(frame,"Error, Invalid Access Count!.","OK");
            tfAccessCount.requestFocus();
            return;
        }
        
        User newUser = new User(id, password, customerID, userName, admin, notifyLogin, notifyChange, dept, email, phone, mobile, count);
        //add Agency to vector
        int pos = 0;
        if(AddSaveUser.getText().equals("Add")) {
            pos = users.size();
            //check for dup names
            
            for(int i = 0; i < pos; ++i) {
                String oldUserID = ((User)users.get(i)).getLoginName();
//System.out.println(id+"----"+agent);
                if(id.equalsIgnoreCase(oldUserID)) {
                    new MessageDialog(frame,"Error, User already in exists!.","OK");
                    return;
                }
            }
            if(dbAccess.addUser(newUser)) {
                users.add(newUser);
            } else
                return; //add failed
        } else {
            if(dbAccess.updateUser(newUser)) {
                pos = userPos;
                users.remove(userPos);
                users.add(userPos,newUser);
            } else
                return; //update failed
        }
//        UpdateCustomerComboBox();
        DeleteUser.setEnabled(true);
//System.out.println("Delete button enabled");
        DeleteUser.repaint();
        loginList.setListData(users);
        updateUserList(pos);
    }//GEN-LAST:event_AddSaveUserActionPerformed

    private void CreateNewUserActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_CreateNewUserActionPerformed
        initNewUser();
    }//GEN-LAST:event_CreateNewUserActionPerformed

    private void LoginAdministratorActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_LoginAdministratorActionPerformed
// Add your handling code here:
    }//GEN-LAST:event_LoginAdministratorActionPerformed

    private void btnExitActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnExitActionPerformed
        exitForm(null);
    }//GEN-LAST:event_btnExitActionPerformed

    private void CustomerListValueChanged(javax.swing.event.ListSelectionEvent evt) {//GEN-FIRST:event_CustomerListValueChanged
        javax.swing.JList list = (javax.swing.JList)evt.getSource();
        customerPos = list.getSelectedIndex();
//System.out.println("AgencyListValueChanged - agencyPos="+agencyPos);
//if(agencyPos == -1)
//    Thread.dumpStack();
//System.out.println("AgencyListValueChanged - Agency="+list.getSelectedValue());
        if(list.getModel().getSize() > 0) {
            displayCustomer((Customer)list.getSelectedValue());
//System.out.println("AgencyListValueChanged - Changed button text to Save");
            AddSaveCustomer.setText("Save Changes");
            tfCustomerID.setEditable(false);
            AddSaveCustomer.repaint();
        }
    }//GEN-LAST:event_CustomerListValueChanged

    private void CreateNewCustomerActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_CreateNewCustomerActionPerformed
        initNewCustomer();
    }//GEN-LAST:event_CreateNewCustomerActionPerformed

    private void DeleteCustomerActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_DeleteCustomerActionPerformed
        int size = customers.size();
        if(size > 0) {
            int pos = customerPos;
            Customer customer = (Customer)customers.elementAt(customerPos);
            if(dbAccess.deleteCustomer(customer)) {
                customers.remove(customerPos--);
                pos=0;
                if(size == 1) {
                    DeleteCustomer.setEnabled(false);
                    DeleteCustomer.repaint();
                    initNewCustomer();
                }
                CustomerList.setListData(customers);   //note, this deletes all old data
                updateCustomerList(pos);
                UpdateCustomerComboBox();
            }
        }
    }//GEN-LAST:event_DeleteCustomerActionPerformed

    private void AddSaveCustomerActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_AddSaveCustomerActionPerformed
        String id = tfCustomerID.getText().trim();
        if(id.equals("")) {
            new MessageDialog(frame,"Error, invalid 'Customer ID'.","OK");
            tfCustomerID.requestFocus();
            return;
        }
        String name = tfCustomerName.getText();
        if(name.equals("")) {
            new MessageDialog(frame,"Error, invalid 'Customer Name'.","OK");
            tfCustomerName.requestFocus();
            return;
        }
        String addr = taCustomerAddress.getText();
        int btnCount = 0;
        int btnStorage = 0;
        int archiveCount = 0;
        int archiveStorage = 0;
        try {
            btnCount = Integer.parseInt(CustomerBtnCount.getText());
            btnStorage = Integer.parseInt(CustomerBtnStorage.getText());
            archiveCount = Integer.parseInt(ArchiveBtnCount.getText());
            archiveStorage = Integer.parseInt(ArchiveBtnStorage.getText());
        } catch (Exception e) {
            new MessageDialog(frame,"Error, Invalid BTN information!  Save NOT done.","OK");
            return;
        }
        if(agencies.size() <1) {
            new MessageDialog(frame,"Error, No agencies available.  Please create them first.","OK");
            return;
        }
        Agency agent = (Agency)AgencyComboBox.getSelectedItem();
//        if(name.equals("")) {
//            new MessageDialog(frame,"Error, invalid 'Customer Name'.","OK");
//            tfCustomerName.requestFocus();
//            return;
//        }
        int i = AllowArchiveAccess.getSelectedIndex();
        Customer newCustomer = new Customer(id, name, addr,agent.getID(), i,btnCount,btnStorage,archiveCount,archiveStorage);
        //add Agency to vector
        int pos = 0;
        if(AddSaveCustomer.getText().equals("Add")) {
            pos = customers.size();
            //check for dup names
            
            for(i = 0; i < pos; ++i) {
                String oldCustomerID = ((Customer)customers.get(i)).getID();
//System.out.println(id+"----"+agent);
                if(id.equalsIgnoreCase(oldCustomerID)) {
                    new MessageDialog(frame,"Error, Agent already in exists!.","OK");
                    return;
                }
            }
            if(dbAccess.addCustomer(newCustomer)) {
                customers.add(newCustomer);
            } else
                return; //add failed
        } else {
            if(dbAccess.updateCustomer(newCustomer)) {
                pos = customerPos;
                customers.remove(customerPos);
                customers.add(customerPos,newCustomer);
            } else
                return; //update failed
        }
        UpdateCustomerComboBox();
        DeleteCustomer.setEnabled(true);
//System.out.println("Delete button enabled");
        DeleteCustomer.repaint();
        CustomerList.setListData(customers);
        updateCustomerList(pos);
    }//GEN-LAST:event_AddSaveCustomerActionPerformed

    private void CustomerExitActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_CustomerExitActionPerformed
        exitForm(null);
    }//GEN-LAST:event_CustomerExitActionPerformed

    private void AgencyListValueChanged(javax.swing.event.ListSelectionEvent evt) {//GEN-FIRST:event_AgencyListValueChanged
        javax.swing.JList list = (javax.swing.JList)evt.getSource();
        agencyPos = list.getSelectedIndex();
//System.out.println("AgencyListValueChanged - agencyPos="+agencyPos);
//if(agencyPos == -1)
//    Thread.dumpStack();
//System.out.println("AgencyListValueChanged - Agency="+list.getSelectedValue());
        if(list.getModel().getSize() > 0) {
            displayAgency((Agency)list.getSelectedValue());
//System.out.println("AgencyListValueChanged - Changed button text to Save");
            SaveAddAgency.setText("Save Changes");
            tfAgencyID.setEditable(false);
            SaveAddAgency.repaint();
        }
    }//GEN-LAST:event_AgencyListValueChanged

    private void AgencyExitActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_AgencyExitActionPerformed
        exitForm(null);
    }//GEN-LAST:event_AgencyExitActionPerformed

    private void btnNewAgencyActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnNewAgencyActionPerformed
        initNewAgency();
    }//GEN-LAST:event_btnNewAgencyActionPerformed

    private void DeleteAgencyActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_DeleteAgencyActionPerformed
        int size = agencies.size();
        if(size > 0) {
            int pos = agencyPos;
            Agency agency = (Agency)agencies.elementAt(agencyPos);
            if(dbAccess.deleteAgency(agency)) {
                agencies.remove(agencyPos--);
                pos=0;
                if(size == 1) {
                    DeleteAgency.setEnabled(false);
                    DeleteAgency.repaint();
                    initNewAgency();
                }
                AgencyList.setListData(agencies);   //note, this deletes all old data
                updateAgencyList(pos);
                UpdateAgencyComboBox();
            }
        }
    }//GEN-LAST:event_DeleteAgencyActionPerformed

    private void SaveAddAgencyActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_SaveAddAgencyActionPerformed
        String id = tfAgencyID.getText();
        id = id.trim();
        if(id.equals("")) {
            new MessageDialog(frame,"Error, invalid 'Agency ID'.","OK");
            tfAgencyID.requestFocus();
            return;
        }
        String name = tfAgencyName.getText().trim();
        if(name.equals("")) {
            new MessageDialog(frame,"Error, invalid 'Agency Name'.","OK");
            tfAgencyName.requestFocus();
            return;
        }
        String pName = tfPluralName.getText().trim();
        String addr = taAgencyAddress.getText();
        String contact = tfContactName.getText();
        String phone = tfContactPhone.getText();
        String fax = tfContactFax.getText();
        String secondContact = tf2ndContact.getText();
        
        Agency newAgency = new Agency(id, name, addr,contact,phone,fax,secondContact,pName);
        //add Agency to vector
        int pos = 0;
        if(SaveAddAgency.getText().equals("Add")) {
            pos = agencies.size();
            //check for dup names
            for(int i = 0; i < pos; ++i) {
                String agent = ((Agency)agencies.get(i)).getID();
//                String agent = agencies.get(i).toString();
//System.out.println(id+"----"+agent);
                if(id.equalsIgnoreCase(agent)) {
                    new MessageDialog(frame,"Error, Agent already in exists!.","OK");
                    return;
                }
            }
            if(dbAccess.addAgency(newAgency)) {
                agencies.add(newAgency);
            } else
                return; //add failed
        } else {
            if(dbAccess.updateAgency(newAgency)) {
                pos = agencyPos;
                agencies.remove(agencyPos);
                agencies.add(agencyPos,newAgency);
            } else
                return; //update failed
        }
        newAgency.writeIndexHTML(frame);
        UpdateAgencyComboBox();
        DeleteAgency.setEnabled(true);
//System.out.println("Delete button enabled");
        DeleteAgency.repaint();
        AgencyList.setListData(agencies);
        updateAgencyList(pos);
    }//GEN-LAST:event_SaveAddAgencyActionPerformed

    private void jButton4ActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jButton4ActionPerformed
        exitForm(null);
    }//GEN-LAST:event_jButton4ActionPerformed

    private void BtnCustomerListValueChanged(javax.swing.event.ListSelectionEvent evt) {//GEN-FIRST:event_BtnCustomerListValueChanged
        if(evt.getValueIsAdjusting())
            return;							//just scrolling down, or mouse down event, not a mouse UP event
        javax.swing.JList list = (javax.swing.JList)evt.getSource();
        btnCustomerPos = list.getSelectedIndex();
//        if(btnCustomerPos == -1) {
//            new MessageDialog(frame,"Error, Please SELECT a Customer to be deleted","OK");
//            return;
//        }
        if(list.getModel().getSize() > 0 && (btnCustomerPos >= 0)) {
            Customer customer = (Customer)list.getSelectedValue();
            btnCompany.setText(customer.getName());
            String subDir = BTNfile.getOriginalDir(customer.getID());
            String szDir = BTNfile.homeDir+subDir;
            btnStoredAt.setText("Original BTN's in "+szDir);
            File[] pStr;
            int max = -1;
            int i;
            btnFiles = new Vector();
            
            File dir = new File(szDir);
            pStr = dir.listFiles();
//zzz
            Vector SOBtns = new Vector();

            if(pStr != null && evt.getValueIsAdjusting()==false) {
                serviceOrder = new SOActivity(szDir,frame,customer,SOBtns);
                monthService = new MonthlyService(szDir,frame,customer,btnFiles);
            }
                
//System.out.println("BTN count = "+btnFiles.size());
            BtnList.setListData(btnFiles);
            BtnCount.setText("Monthly Service # = "+btnFiles.size());
            
            SOList.setListData(SOBtns);
            SOCount.setText("Service Orders: "+SOBtns.size());
//            SOList.repaint();
        }
    }//GEN-LAST:event_BtnCustomerListValueChanged

    private void ConvertAllBTNActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_ConvertAllBTNActionPerformed
        if(serviceOrder != null) {
//System.out.println("hack!!! monthly service commented out!!!!!");
            monthService.writeHTMLs(dbAccess);//dbUpdate during processing of writeHTMLs
            serviceOrder.writeHTMLs(dbAccess);	//dbUpdate during processing of writeHTMLs
        }

//        int cnt = btnFiles.size();
//        for(int i=0; i < cnt; ++i) {
//            BTNfile file = (BTNfile)btnFiles.elementAt(i);
//            file.convertToHTML(dbAccess,agencies);
//        }
    }//GEN-LAST:event_ConvertAllBTNActionPerformed

    private void DeleteSelectedBTNActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_DeleteSelectedBTNActionPerformed
        int[] cnt = BtnList.getSelectedIndices();
//zzz deleteBTN
        for(int i=cnt.length-1; i >=0; --i) {   //loop from bottom to top
            int idx = cnt[i];
            BTNfile btnFile = (BTNfile)btnFiles.elementAt(idx);
            System.out.println(btnFile.detailInputFile);
            btnFile.delete(dbAccess);
            btnFiles.remove(idx);
        }
        BtnList.setListData(btnFiles);
        BtnCount.setText("BTN count = "+btnFiles.size());
        BtnList.repaint();
    }//GEN-LAST:event_DeleteSelectedBTNActionPerformed

    private void fixBtnButtonActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_fixBtnButtonActionPerformed
//zzz fix btn
        BTN btn;
//loop thru all BTN's in the databse & confirm that they all have summary, detain, tab & txt files
        Customer customer = (Customer)BtnCustomerList.getSelectedValue();
        if(customer == null) {
            new MessageDialog(frame,"Error, please choose a customer.","OK");
            return;
        }
        String customerID = customer.getID();
        if(customerID == null || customerID.length() < 1) {
            new MessageDialog(frame,"Error, please choose a customer.","OK");
            return;
        }
        dbAccess.resetBTNbyCustomerID(customerID);    //really should filter by customerID, not later
        System.out.println("Test all BTN's that are owned by " + customer.getName() + " (" + customerID + ")");
        int fixCount = 0;
        Vector oldBtns = new Vector();
        Vector newBtnString = new Vector();
        
        BTNfile btnFile = null;
        while((btn = dbAccess.nextBTN()) != null) {
//start:            
            String nextCustomerID = btn.getCustomerID();
            if(!nextCustomerID.equals(customerID))
                break;
//            String rootName = btn.getBTN();
//            System.out.println(rootName);
            btn.setMonthlyService(0.0);
            btn.setServiceOrders(0.0);
            String szBtn = btn.getBTN();
            int pos;
            boolean changed = false;
            if(szBtn.startsWith("o") || szBtn.startsWith("O")) {
                szBtn = szBtn.substring(1);
                changed = true;
            }
            if((pos = szBtn.indexOf('-') ) != -1) {
                szBtn = szBtn.substring(0,pos) + szBtn.substring(pos+1);
                changed = true;
            }
            if(szBtn.indexOf('b') != -1 || szBtn.indexOf('B') != -1) {
                szBtn = szBtn.replaceAll("[bB]","1");
                changed = true;
            }
            if(szBtn.indexOf('c') != -1 || szBtn.indexOf('C') != -1) {
                szBtn = szBtn.replaceAll("[cC]","2");
                changed = true;
            }
            if(szBtn.indexOf('d') != -1 || szBtn.indexOf('D') != -1) {
                szBtn = szBtn.replaceAll("[dD]","3");
                changed = true;
            }
            if(szBtn.indexOf('e') != -1 || szBtn.indexOf('E') != -1) {
                szBtn = szBtn.replaceAll("[eE]","4");
                changed = true;
            }
            if(szBtn.indexOf('f') != -1 || szBtn.indexOf('F') != -1) {
                szBtn = szBtn.replaceAll("[fF]","5");
                changed = true;
            }
            if(szBtn.indexOf('g') != -1 || szBtn.indexOf('G') != -1) {
                szBtn = szBtn.replaceAll("[gG]","6");
                changed = true;
            }
            if(szBtn.indexOf('h') != -1 || szBtn.indexOf('H') != -1) {
                szBtn = szBtn.replaceAll("[hH]","7");
                changed = true;
            }
            if(szBtn.indexOf('j') != -1 || szBtn.indexOf('J') != -1) {
                szBtn = szBtn.replaceAll("[jJ]","8");
                changed = true;
            }
            if(szBtn.indexOf('k') != -1 || szBtn.indexOf('K') != -1) {
                szBtn = szBtn.replaceAll("[kK]","9");
                changed = true;
            }
//put my own try catch here ???
            if(!changed)
                dbAccess.updateBTN(btn);
            else {
                oldBtns.add(btn);
                newBtnString.add(szBtn);
//zzz
System.out.println("saving - old ("+btn.btn+") as  new ("+szBtn+")");                
            }
        }

        int siz = oldBtns.size();
        if(siz != newBtnString.size()) {
             new MessageDialog(frame,"Error, new and old btn counts are different.  No conversion done.","OK");
            return;
        }
        int i;
        for(i=0; i < siz; ++i) {
            BTN tmpBtn = (BTN)oldBtns.elementAt(i);
            String orgBtnValue = tmpBtn.getBTN();
            tmpBtn.btn = (String)newBtnString.elementAt(i);

            if(dbAccess.BTNexists(tmpBtn.btn) ) {
//				if(!dbAccess.updateBTN(tmpBtn)) {
//System.out.println("Error, failed to update btn "+tmpBtn.getBTN());                
//..             new MessageDialog(frame,"Error, failed to update btn "+tmpBtn.getBTN(),"OK");
//             //don't remove existing button, if new one failed to update
//				}
            } else {
System.out.println("adding new btn "+tmpBtn.getBTN());                
            	if(!dbAccess.addBTN(tmpBtn)) {
System.out.println("Error, failed to add btn "+tmpBtn.getBTN());                
	             new MessageDialog(frame,"Error, failed to add btn "+tmpBtn.getBTN(),"OK");
	             //don't remove existing button, if new one failed to add
    	         continue;
			 	}
            }
            tmpBtn.btn = orgBtnValue;
System.out.println("Removing btn "+tmpBtn.getBTN());                
            if(!dbAccess.deleteBTN(tmpBtn)) {
                System.out.println("Error, failed to remove btn "+tmpBtn.getBTN());                
                new MessageDialog(frame,"Error, failed to remove btn "+tmpBtn.getBTN(),"OK");                
            }
            
        }
        
        return;

    }//GEN-LAST:event_fixBtnButtonActionPerformed

    private void jButton1ActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jButton1ActionPerformed
        BTN btn;
        AMC amc;
        dbAccess.resetBTN();
        int cnt = 0;
        String emptyAMC = "000000000000";
        while((btn = dbAccess.nextBTN()) != null) {
            String szBtn = btn.getBTN();
            String szAMC = btn.getAMC();
            boolean found=false;
            dbAccess.resetAMC(szBtn);
            int open = 0;
            int inProgress = 0;
            int closed = 0;
            while((amc = dbAccess.nextAMC()) != null) {
                found = true;
                String status = amc.getStatus();
                if(status.equals("Open"))
                    ++open;
                else if(status.equals("In Progress"))
                    ++inProgress;
                else if(status.equals("Closed"))
                    ++closed;
            }
            if(found) {
                String str = AMC.buildStatusString(open, inProgress, closed);
                if(!str.equals(szAMC)) {
                    ++cnt;
                    System.out.print(cnt+") btn=" + szBtn + " old AMC " + szAMC);
                    System.out.println("  (" +open + ") (" + inProgress + ") (" + closed + ") new AMC= "+str);
                    btn.setAMC(str);
                    dbAccess.updateBTN(btn);
                }
            } else {
                if(!szAMC.equals(emptyAMC)) {
                    ++cnt;
                    System.out.println(cnt+") RESET btn=" + szBtn + " oldAMC=" + szAMC);
                    btn.setAMC(emptyAMC);
                    dbAccess.updateBTN(btn);
                }
            }
//            btn = dbAccess.nextBTN();
        }
//?????
    }//GEN-LAST:event_jButton1ActionPerformed

    private void jButton2ActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jButton2ActionPerformed
        exitForm(null);
    }//GEN-LAST:event_jButton2ActionPerformed
/***********************************************/
/*          Customer Tab   buttons             */
/***********************************************//***********************************************/
/*          Agency Tab   buttons             */
/***********************************************//***********************************/
/*  Work routines for Agency       */
/***********************************/
    private void initNewAgency(){
        tfAgencyID.setText("");
        tfAgencyName.setText("");
        tfPluralName.setText("");
        taAgencyAddress.setText("");
        tfContactName.setText("");
        tfContactPhone.setText("");
        tfContactFax.setText("");
        tf2ndContact.setText("");
//System.out.println("Changed button text to Add");        
        SaveAddAgency.setText("Add");
        tfAgencyID.setEditable(true);
        SaveAddAgency.repaint();
    }
    public void displayAgency(Agency agency){
        if(agency != null) {
            tfAgencyID.setText(agency.getID());
            tfAgencyName.setText(agency.getName());
            tfPluralName.setText(agency.getPluralName());
            taAgencyAddress.setText(agency.getAddress());
            tfContactName.setText(agency.getContact());
            tfContactPhone.setText(agency.getPhone());
            tfContactFax.setText(agency.getFax());
            tf2ndContact.setText(agency.get2ndContact());
        } else {
//            System.out.println("Oops, null agency in displayAgency");
        }
    }
    
    private void UpdateAgencyComboBox() {
//System.out.println("removing all items from agency combo box");        
        AgencyComboBox.removeAllItems();
        for(int i=0; i < agencies.size(); ++i) {
            AgencyComboBox.addItem(agencies.elementAt(i));
        }
    }
/***********************************/
/*  Work routines for Customer     */
/***********************************/
    private void initNewCustomer(){
        tfCustomerID.setText("");       // CustomerID:
        tfCustomerID.setEditable(true);
        tfCustomerName.setText("");     // Customer Name:
        taCustomerAddress.setText("");  // Customer Address:
        AgencyComboBox.setSelectedIndex(agencies.size()> 0? 0:-1);
        AllowArchiveAccess.setSelectedIndex(Customer.NO);    //deafult archive access
        CustomerBtnCount.setText("0");
        CustomerBtnStorage.setText("0");
        ArchiveBtnCount.setText("0");
        ArchiveBtnStorage.setText("0");
        AddSaveCustomer.setText("Add");
        AddSaveCustomer.repaint();
    }

    public void displayCustomer(Customer customer){
        if(customer != null) {
            tfCustomerID.setText(customer.getID());
            tfCustomerName.setText(customer.getName());
            taCustomerAddress.setText(customer.getAddress());
            String agentID = customer.getAgencyID();
            int idx = 0;
            int max = agencies.size();
            for(int i = 0; i < max; ++i) {
                Agency agency = (Agency)agencies.elementAt(i);
                if(agency.getID().equals(agentID)) {
                    idx = i;
                    break;
                }
            }
        
            CustomerBtnCount.setText(""+customer.getBTNCount());
            CustomerBtnStorage.setText(""+customer.getBTNStorage());
            ArchiveBtnCount.setText(""+customer.getArchiveBtnCount());
            ArchiveBtnStorage.setText(""+customer.getArchiveBtnStorage());

            AgencyComboBox.setSelectedIndex(idx);
            AgencyComboBox.repaint();
            AllowArchiveAccess.setSelectedIndex(customer.getArchiveAccess());    // YES/NO are indexes
            AllowArchiveAccess.repaint();
        } else {
//            System.out.println("Oops, null customer in displayCustomer");
        }
    }
    private void UpdateCustomerComboBox() {
//System.out.println("removing all items from customer combo box");        
        CustomerComboBox.removeAllItems();
        for(int i=0; i < customers.size(); ++i) {
            CustomerComboBox.addItem(customers.elementAt(i));
        }
    }

/***************************************/
/*  Work routines for Login/Password   */
/***************************************/
    public void displayUser(User user){
        if(user != null) {
            tfLoginName.setText(user.getLoginName());
            tfPassword.setText(user.getPassword());
//set CustomerID ComboBox            
            String customerID = user.getCustomerID();
            int idx = 0;
            int max = customers.size();
            for(int i = 0; i < max; ++i) {
                Customer customer = (Customer)customers.elementAt(i);
                if(customer.getID().equals(customerID)) {
                    idx = i;
                    break;
                }
            }
            CustomerComboBox.setSelectedIndex(idx);
            CustomerComboBox.repaint();
            
            tfLoginUserName.setText(user.getUserName());
            LoginAdministrator.setSelectedIndex(user.getAdministrator());
            LoginAdministrator.repaint();
            NotifyLogin.setSelectedIndex(user.getNotifyAboutLogins());
            NotifyLogin.repaint();
            NotifyChange.setSelectedIndex(user.getNotifyAboutChanges());
            NotifyChange.repaint();
            tfLoginDept.setText(user.getDepartment());
            tfLoginEmail.setText(user.getEmail());
            tfLoginPhone.setText(user.getPhone());
            tfLoginMobile.setText(user.getMobil());
            String num = "" + user.getAccessCount();
            tfAccessCount.setText(num);
        } else {
//            System.out.println("Oops, null customer in displayCustomer");
        }
    }

    private void initNewUser() {
        tfLoginName.setText("");
        tfPassword.setText("");
        boolean hasCustomers = (customers.size() > 0);
        CustomerComboBox.setSelectedIndex(hasCustomers ? User.NO : -1);
        tfLoginUserName.setText("");
        LoginAdministrator.setSelectedIndex(hasCustomers ? User.NO : -1);
        NotifyLogin.setSelectedIndex(hasCustomers ? User.NO : -1);
        NotifyChange.setSelectedIndex(hasCustomers ? User.NO : -1);
        tfLoginDept.setText("");
        tfLoginEmail.setText("");
        tfLoginPhone.setText("");
        tfLoginMobile.setText("");
        tfAccessCount.setText("");
        
        AddSaveUser.setText("Add");
        tfLoginName.setEditable(true);
        AddSaveUser.repaint();
}    
    /** Exit the Application */
    private void exitForm(java.awt.event.WindowEvent evt) {//GEN-FIRST:event_exitForm
        if(dbAccess != null)
            dbAccess.close();
        System.exit(0);
    }//GEN-LAST:event_exitForm
    
    /**
     * @param args the command line arguments
     */
    public static void main(String args[]) {
        new test1().show();
    }
    
    
    // Variables declaration - do not modify//GEN-BEGIN:variables
    private javax.swing.JButton AddSaveCustomer;
    private javax.swing.JButton AddSaveUser;
    private javax.swing.JComboBox AgencyComboBox;
    private javax.swing.JButton AgencyExit;
    private javax.swing.JList AgencyList;
    private javax.swing.JComboBox AllowArchiveAccess;
    private javax.swing.JLabel ArchiveBtnCount;
    private javax.swing.JLabel ArchiveBtnStorage;
    private javax.swing.JLabel BtnCount;
    private javax.swing.JList BtnCustomerList;
    private javax.swing.JList BtnList;
    private javax.swing.JList BtnList1;
    private javax.swing.JButton ConvertAllBTN;
    private javax.swing.JButton CreateNewCustomer;
    private javax.swing.JButton CreateNewUser;
    private javax.swing.JLabel CustomerBtnCount;
    private javax.swing.JLabel CustomerBtnCount1;
    private javax.swing.JLabel CustomerBtnCount3;
    private javax.swing.JLabel CustomerBtnStorage;
    private javax.swing.JComboBox CustomerComboBox;
    private javax.swing.JButton CustomerExit;
    private javax.swing.JList CustomerList;
    private javax.swing.JButton DeleteAgency;
    private javax.swing.JButton DeleteCustomer;
    private javax.swing.JButton DeleteSelectedBTN;
    private javax.swing.JButton DeleteUser;
    private javax.swing.JLabel ExistingAgencies;
    private javax.swing.JLabel ExistingCustomers;
    private javax.swing.JTextField IPAddrField;
    private javax.swing.JComboBox LoginAdministrator;
    private javax.swing.JComboBox NotifyChange;
    private javax.swing.JComboBox NotifyLogin;
    private javax.swing.JLabel SOCount;
    private javax.swing.JList SOList;
    private javax.swing.JTextField SQLPasswordField;
    private javax.swing.JTextField SQLUserField;
    private javax.swing.JButton SaveAddAgency;
    private javax.swing.JLabel btnCompany;
    private javax.swing.JButton btnExit;
    private javax.swing.JButton btnNewAgency;
    private javax.swing.JLabel btnStoredAt;
    private javax.swing.JLabel existingUsers;
    private javax.swing.JButton fixBtnButton;
    private javax.swing.JButton jButton1;
    private javax.swing.JButton jButton2;
    private javax.swing.JButton jButton3;
    private javax.swing.JButton jButton4;
    private javax.swing.JLabel jLabel1;
    private javax.swing.JLabel jLabel10;
    private javax.swing.JLabel jLabel11;
    private javax.swing.JLabel jLabel12;
    private javax.swing.JLabel jLabel13;
    private javax.swing.JLabel jLabel14;
    private javax.swing.JLabel jLabel15;
    private javax.swing.JLabel jLabel16;
    private javax.swing.JLabel jLabel17;
    private javax.swing.JLabel jLabel18;
    private javax.swing.JLabel jLabel19;
    private javax.swing.JLabel jLabel2;
    private javax.swing.JLabel jLabel20;
    private javax.swing.JLabel jLabel21;
    private javax.swing.JLabel jLabel22;
    private javax.swing.JLabel jLabel23;
    private javax.swing.JLabel jLabel24;
    private javax.swing.JLabel jLabel25;
    private javax.swing.JLabel jLabel26;
    private javax.swing.JLabel jLabel27;
    private javax.swing.JLabel jLabel28;
    private javax.swing.JLabel jLabel29;
    private javax.swing.JLabel jLabel3;
    private javax.swing.JLabel jLabel30;
    private javax.swing.JLabel jLabel31;
    private javax.swing.JLabel jLabel32;
    private javax.swing.JLabel jLabel33;
    private javax.swing.JLabel jLabel34;
    private javax.swing.JLabel jLabel35;
    private javax.swing.JLabel jLabel36;
    private javax.swing.JLabel jLabel37;
    private javax.swing.JLabel jLabel38;
    private javax.swing.JLabel jLabel39;
    private javax.swing.JLabel jLabel4;
    private javax.swing.JLabel jLabel40;
    private javax.swing.JLabel jLabel41;
    private javax.swing.JLabel jLabel42;
    private javax.swing.JLabel jLabel43;
    private javax.swing.JLabel jLabel44;
    private javax.swing.JLabel jLabel5;
    private javax.swing.JLabel jLabel6;
    private javax.swing.JLabel jLabel7;
    private javax.swing.JLabel jLabel8;
    private javax.swing.JLabel jLabel9;
    private javax.swing.JMenu jMenu1;
    private javax.swing.JMenuBar jMenuBar1;
    private javax.swing.JPanel jPanel1;
    private javax.swing.JPanel jPanel10;
    private javax.swing.JPanel jPanel11;
    private javax.swing.JPanel jPanel12;
    private javax.swing.JPanel jPanel13;
    private javax.swing.JPanel jPanel14;
    private javax.swing.JPanel jPanel15;
    private javax.swing.JPanel jPanel2;
    private javax.swing.JPanel jPanel3;
    private javax.swing.JPanel jPanel4;
    private javax.swing.JPanel jPanel5;
    private javax.swing.JPanel jPanel6;
    private javax.swing.JPanel jPanel7;
    private javax.swing.JPanel jPanel8;
    private javax.swing.JPanel jPanel9;
    private javax.swing.JScrollPane jScrollPane1;
    private javax.swing.JScrollPane jScrollPane2;
    private javax.swing.JScrollPane jScrollPane3;
    private javax.swing.JScrollPane jScrollPane4;
    private javax.swing.JScrollPane jScrollPane5;
    private javax.swing.JScrollPane jScrollPane7;
    private javax.swing.JList loginList;
    private javax.swing.JTextArea taAgencyAddress;
    private javax.swing.JTextArea taCustomerAddress;
    private javax.swing.JTabbedPane tabPassword;
    private javax.swing.JTextField tf2ndContact;
    private javax.swing.JTextField tfAccessCount;
    private javax.swing.JTextField tfAgencyID;
    private javax.swing.JTextField tfAgencyName;
    private javax.swing.JTextField tfContactFax;
    private javax.swing.JTextField tfContactName;
    private javax.swing.JTextField tfContactPhone;
    private javax.swing.JTextField tfCustomerID;
    private javax.swing.JTextField tfCustomerName;
    private javax.swing.JTextField tfLoginDept;
    private javax.swing.JTextField tfLoginEmail;
    private javax.swing.JTextField tfLoginMobile;
    private javax.swing.JTextField tfLoginName;
    private javax.swing.JTextField tfLoginPhone;
    private javax.swing.JTextField tfLoginUserName;
    private javax.swing.JTextField tfPassword;
    private javax.swing.JTextField tfPluralName;
    // End of variables declaration//GEN-END:variables
    private javax.swing.JPanel jPanel16;
}
