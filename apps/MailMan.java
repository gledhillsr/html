//package com.jarlink.mailman;

import javax.mail.*;
import javax.mail.internet.*;
import javax.activation.*;
import java.util.*;

/**
 * Title:        MailMan
 * Description:  Mail Manager
 * Copyright:    Copyright (c) 2001
 * Company:      JarLink
 * @author Jared Allen
 * @version 1.0
 */

public class MailMan {
    private InternetAddress hostAddress;   	//ie mail.phoenixdsl.com
    private InternetAddress fromAddress;	//ie Steve@gledhills.com

    /**
     * MailMan constructor.
     *
     * @param host  The smtp host address.
     * @param from  The return address.
     */
    public MailMan(String host, String from) {
        try {
            hostAddress = new InternetAddress(host);
            fromAddress = new InternetAddress(from);
        } catch(AddressException ex) {
            throw new IllegalArgumentException();
        }
    }

    /**
     * Sets the smtp host address.
     *
     * @param host  The smtp host address.
     */
    public void setHostAddress(String host) {
        try {
            hostAddress = new InternetAddress(host);
        } catch(AddressException ex) {
            throw new IllegalArgumentException();
        }
    }

    /**
     * Gets the smtp host address.
     *
     * @return  The smtp host address.
     */
    public String getHostAddress() {
        return hostAddress.getAddress();
    }

    /**
     * Sets the return address.
     *
     * @param from  The return address.
     */
    public void setFromAddress(String from) {
        try {
            fromAddress = new InternetAddress(from);
        } catch(AddressException ex) {
            throw new IllegalArgumentException();
        }
    }

    /**
     * Gets the return address.
     *
     * @return  The return address.
     */
    public String getFromAddress() {
        return fromAddress.getAddress();
    }

    /**
     * Sends a message with the given subject and message body to the given
     * of recipient.  The message is sent via the pre-set smtp host with the
     * pre-set return address.
     *
     * @param subject       Subject of the message.
     * @param message       The message body.
     * @param recipients    The recipient.
     */
    public void sendMessage(String subject, String message, String recipient) throws MailManException {
        String[] recipients = new String[1];
        recipients[0] = recipient;
        sendMessage(subject, message, recipients);
    }

    /**
     * Sends a message with the given subject and message body to the given list
     * of recipients.  The message is sent via the pre-set smtp host with the
     * pre-set return address.
     *
     * @param subject       Subject of the message.
     * @param message       The message body.
     * @param recipients    An array of strings representing all of the
     *                      recipients.
     */
    public void sendMessage(String subject, String message, String[] recipients) throws MailManException {
        Properties props = new Properties();
        Session session;
        MimeMessage msg;
        InternetAddress[] rcptAddresses = new InternetAddress[recipients.length];
        MimeBodyPart body;
        Multipart mp;

        props.put("mail.smtp.host", hostAddress.getAddress());
        session = Session.getDefaultInstance(props, null);
        msg = new MimeMessage(session);
        try {
            msg.setFrom(fromAddress);
            for(int i = 0; i < rcptAddresses.length; i++) {
                try {
                    rcptAddresses[i] = new InternetAddress(recipients[i]);
                } catch(AddressException ex) {
                    throw new MailManException("Invalid recipient address: " + recipients[i]);
                }
            }
            msg.setRecipients(Message.RecipientType.TO, rcptAddresses);
            msg.setSubject(subject);
            msg.setSentDate(new Date());
            // create and fill the first message part
            body = new MimeBodyPart();
            body.setText(message);
            // create the Multipart and add its parts to it
            mp = new MimeMultipart();
            mp.addBodyPart(body);
            // add the Multipart to the message
            msg.setContent(mp);
            // send the message
            Transport.send(msg);
        } catch(MessagingException ex) {
            throw new MailManException(ex.toString());
        }
    }

    public static void main(String[] args) throws MailManException {
        if(args.length < 5) {
            System.out.println("Invalid arguments");
            System.out.println("java com.jarlink.mailmain.MailMan <host> <from address> <subject> <message> <recipient address>");
            return;
        }
        MailMan mailMan = new MailMan(args[0], args[1]);
        mailMan.sendMessage(args[2], args[3], args[4]);
    }
}