<?php
	// change this to wherever you put the thrift library
	$GLOBALS['THRIFT_ROOT'] = dirname(__FILE__) . "/thrift";
	
	// you have to put your gen-php code into the packages folder
	$swiftroot = $GLOBALS['THRIFT_ROOT'] . "/packages";
	
	//swiftapi includes
	require_once $swiftroot . "/SwiftApi/SwiftApi.php";
	require_once $swiftroot . "/SwiftApi/SwiftApi_types.php";
	require_once $swiftroot . "/Errors/Errors_types.php";
	
	//thrift includes
	require_once $GLOBALS['THRIFT_ROOT'] . "/transport/TSocket.php";
	require_once $GLOBALS['THRIFT_ROOT'] . "/transport/TFramedTransport.php";
	require_once $GLOBALS['THRIFT_ROOT'] . "/protocol/TBinaryProtocol.php";

	// Create an authString!
	function getAuthString($methodName) {
		$username = "admin";
		$password = "password";
		$salt = "saltines";
	
		$toHash = $username . $methodName . $password . $salt;
		
		return hash("sha256", $toHash);
	}
	
	if($_POST) {
		echo "Opening connection...<br />";
		//open a new connection to the server
		$socket = new TSocket('your.bukkitserver.org', 21111);
		
		// note: you must use the TFramedTransport if using SwiftApi 0.5 or greater
		// for versions prior to 0.5, use TBufferedTransport
		//$transport = new TFramedTransport($socket);
		$transport = new TBufferedTransport($socket);
		
		$protocol = new TBinaryProtocol($transport);

		echo "Creating client...<br />";
		
		//create a new SwiftApiClient object
		$client= new SwiftApiClient($protocol, $protocol);
		$transport->open();
		
		echo "Server Version: " . $client->getServerVersion(getAuthString("getServerVersion"));		
		
		//close the connection
		$transport->close();
	}
?>

<form action="" method="post">
	<input name="submit" type="submit" value="Get Server Version" />
</form>