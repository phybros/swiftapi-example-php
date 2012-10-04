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
		$socket = new TSocket('localhost', 21111);
		
		// have to set this if using the installPlugin method 
		// because sometimes it can take a while to complete.
		$socket->setRecvTimeout(10000);
		
		// note: you must use the TFramedTransport if using SwiftApi 0.5 or greater
		// for versions prior to 0.5, use TBufferedTransport
		$transport = new TFramedTransport($socket);
		//$transport = new TBufferedTransport($socket);
		
		$protocol = new TBinaryProtocol($transport);

		echo "Creating client...<br />";
		
		//create a new SwiftApiClient object
		$client= new SwiftApiClient($protocol, $protocol);
		$transport->open();
		
		$op = $_POST['operation'];
		switch($op) {
			case "getServerVersion":
				$serverVersion = $client->getServerVersion(getAuthString("getServerVersion"));
				echo "Server Version: $serverVersion";
				break;
			case "installPlugin":
				// let's install MobBountyReloaded
				$downloadUrl = "http://dev.bukkit.org/media/files/633/213/MobBountyReloaded_v291.zip";
				// the md5 can be found on the File download page on bukkitdev
				$md5 = "926308ea3b44c81e126d5b0289117c48";
				
				$result = $client->installPlugin(getAuthString("installPlugin"), $downloadUrl, $md5);		
				echo $result ? "Plugin installed" : "Plugin installation failed";
				break;
			case "reloadServer":
				// reload the server's configuration
				$client->reloadServer(getAuthString("reloadServer"));
				break;
			default:
				break;
		}
		
		//close the connection
		$transport->close();
	}
?>

<form action="" method="post">
	<input name="submit" type="submit" value="Get Server Version" />
	<input type="hidden" name="operation" value="getServerVersion" />
</form>
<form action="" method="post">
	<input name="submit" type="submit" value="Install Plugin" />
	<input type="hidden" name="operation" value="installPlugin" />
</form>
<form action="" method="post">
	<input name="submit" type="submit" value="Reload Server" />
	<input type="hidden" name="operation" value="reloadServer" />
</form>