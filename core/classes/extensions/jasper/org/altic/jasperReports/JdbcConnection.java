package org.altic.jasperReports;
	
	import java.io.FileInputStream;
	import java.io.InputStream;
	import java.sql.Connection;
	import java.sql.DriverManager;
	import java.sql.SQLException;
	import java.util.Properties;
	
	
	/**
	 * @author ccharly
	 *
	 */
	public class JdbcConnection {
	
		private String driver;  		// "oracle.jdbc.driver.OracleDriver";
		private String connectString;	// "jdbc:oracle:thin:@YOUR_ORACLE_HOST:1521:YOUR_SID";
		private String user; 			//"YOUR_ORACLE_USER_NAME";
		private String password; 		// "YOUR_ORACLE_PASSWORD";
		
		
		public JdbcConnection(){
			//loadPropertiesConnection();
		}
		
		public JdbcConnection(String driver, String connectString, String user, String password){
			this.driver = driver;
			this.connectString = connectString;
			this.user = user;
			this.password = password;
		}
		
		
	    public void loadPropertiesConnection(){
		
		this.driver = "";
			this.connectString = "";
			this.user = "";
			this.password = "";
			
		try {
			Properties props = new Properties();
			InputStream resourceAsStream = getClass().getResourceAsStream("/connection.properties");
			props.load(resourceAsStream);
			this.setDriver(props.getProperty("driver"));
			this.setConnectString(props.getProperty("connectString"));
			this.setUser(props.getProperty("user"));
			this.setPassword(props.getProperty("password"));
		}catch(Exception e){
			e.printStackTrace();
		}
	    }
	    
	    
	    public Connection getConnection()
		{
		try {
			//Change these settings according to your local configuration
				Class.forName(this.getDriver());
				Connection conn = DriverManager.getConnection(
						this.getConnectString(), 
						this.getUser(), 
						this.getPassword());
				return conn;
		}catch(ClassNotFoundException e){
			e.printStackTrace();
		}catch(SQLException e){
			e.printStackTrace();
		}
		return null;
		}
	
	
	    public String toString(){
		return 	" Driver : " + this.getDriver() + " | " + 
				" ConnectString : " + this.getConnectString()  + " | " +
				" User : " + this.getUser()  + " | " +
				" Password : " + this.getPassword();
	    }
	    
		public String getConnectString() {
			return connectString;
		}
	
	
		public void setConnectString(String connectString) {
			this.connectString = connectString;
		}
	
	
		public String getDriver() {
			return driver;
		}
	
	
		public void setDriver(String driver) {
			this.driver = driver;
		}
	
	
		public String getPassword() {
			return password;
		}
	
	
		public void setPassword(String password) {
			this.password = password;
		}
	
	
		public String getUser() {
			return user;
		}
	
	
		public void setUser(String user) {
			this.user = user;
		}
	    
	    
	    
	}