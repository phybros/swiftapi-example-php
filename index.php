<?php
	/**
	 *	The PHP SwiftApi Test Suite
	 *  ===========================
	 */
	 
	/* Change These to match your server's settings */
	$host = "localhost";
	$port = 21111;
	$username = "admin";
	$password = "password";
	$salt = "saltines";
	 
	/*
	 *	Author: Will Warren (https://github.com/phybros)
	 *
	 *	To call SwiftApi methods, there are 7 steps:
	 *		1. Create a connection to the server
	 *			1.1 Optionally change settings on the socket_accept
	 *		2. Create the Transport
	 *		3. Create the Protocol
	 *		4. Create a new SwiftApiClient object using the protocol
	 *		5. Open the socket
	 *		6. Call methods using the $client object
	 *		7. Close the connection
	 */

 
	// change this to wherever you put the thrift library
	$THRIFT_ROOT = dirname(__FILE__);
	
	require_once $THRIFT_ROOT . '/Thrift/ClassLoader/ThriftClassLoader.php';
	use Thrift\ClassLoader\ThriftClassLoader;

	$loader = new ThriftClassLoader();
	$loader->registerNamespace('Thrift', $THRIFT_ROOT);
	$loader->registerDefinition('Thrift', $THRIFT_ROOT . '/packages');
	$loader->register();

	use Thrift\Transport\TSocket;
	use Thrift\Transport\TFramedTransport;
	use Thrift\Protocol\TBinaryProtocol;

	require_once $THRIFT_ROOT . '/Thrift/packages/org/phybros/thrift/SwiftApi.php';
	require_once $THRIFT_ROOT . '/Thrift/packages/org/phybros/thrift/Types.php';

	// Create an authString!
	function getAuthString($methodName) {
		global $username;
		global $password;
		global $salt;
	
		$toHash = $username . $methodName . $password . $salt;
		
		return hash("sha256", $toHash);
	}
	
	function pre_r($v) {
		echo "<pre>";
		print_r($v);
		echo "</pre>";
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>SwiftApi Test Suite</title>
		<style type="text/css">
			body {
				font-family: "Courier New", monospace;
				font-size: 13px;
			}
			.error {
				color: red;
			}
			.result {
				background: #EEE;
				padding: 5px 15px;
				margin: 20px;
			}
			small {
				color: #AAA;
				font-size: 13px;
				font-weight: normal;
			}
		</style>
	</head>
	<body>
		<h1>SwiftApi Test Suite</h1>
		<p>A collection of exmples of how to call methods using <a href="https://github.com/phybros/swiftapi">SwiftApi</a>.<br />
		Edit this file to suit your server's details.<br />Current settings:
			<ul>
				<li>Host: <?php echo $host; ?></li>
				<li>Port: <?php echo $port; ?></li>
				<li>Username: <?php echo $username; ?></li>
				<li>Password: ********</li>
				<li>Salt: ************</li>
			</ul>
		</p>
		<hr />
		
	
<?php	
	if($_POST) {
		try {
			$op = $_POST['operation'];
			echo "<a name=\"results\"><h1>$op Results</h1></a>";
			
			echo "Creating socket...";
			
			// 1. Create a connection to the server
			$socket = new TSocket($host, $port);
			echo "Done<br />";
			
			// 1.1 Optionally change settings on the socket
			$socket->setRecvTimeout(10000);			
			// You have to set this if using the installPlugin method for example
			// because sometimes it can take a while to complete.
			
			// 2. Create the Transport
			$transport = new TFramedTransport($socket);
			// note: you must use the TFramedTransport if using SwiftApi 0.5 or greater
			// for versions prior to 0.5, use TBufferedTransport
			
			// 3. Create the Protocol
			$protocol = new TBinaryProtocol($transport);

			echo "Creating client...";
			
			// 4. Create a new SwiftApiClient object using the protocol
			$client = new org\phybros\thrift\SwiftApiClient($protocol, $protocol);
			
			echo "Done<br />";
			
			echo "Opening connection...";
			
			// 5. Open the socket (this is where errors are generated if there are connectivity issues)
			$transport->open();			
			echo "Done<br />";
			echo "<div class=\"result\"><p><strong>Result of $op</strong></p>";
			
			// 6. Call methods using the $client object
			switch($op) {
				/******************************** announce *******************************/
				case "announce":		
					//get the message
					$message = $_POST['message'];
					
					//send the message
					$r = $client->announce(getAuthString("announce"), $message);
					var_dump($r);
					break;
				/***************************** deOp *****************************/
				case "deOp":
					$playerName = $_POST['playerName'];
					$notify = isset($_POST['notify']);
					$r = $client->deOp(getAuthString("deOp"), $playerName, $notify);
					var_dump($r);
					break;
				/***************************** getBukkitVersion *****************************/
				case "getBukkitVersion":
					$bukkitVersion = $client->getBukkitVersion(getAuthString("getBukkitVersion"));
					var_dump($bukkitVersion);
					break;
				/***************************** getConsoleMessages *****************************/
				case "getConsoleMessages":
					$since = empty($_POST['since']) ? 0 : $_POST['since'];
					$r = $client->getConsoleMessages(getAuthString("getConsoleMessages"), (float) $since);
					var_dump($r);
					break;
				/***************************** getFileContents ****************************/
				case "getFileContents":
					//the file name
					$fileName = $_POST['fileName'];
					
					//get the file's contents
					$contents = $client->getFileContents(getAuthString("getFileContents"), $fileName);
					pre_r($contents);
					break;
				/***************************** getOfflinePlayer ****************************/
				case "getOfflinePlayer":
					$playerName = $_POST['playerName'];
					
					//get the offline players
					$r = $client->getOfflinePlayer(getAuthString("getOfflinePlayer"), $playerName);
					var_dump($r);
					break;
				/***************************** getOfflinePlayers ****************************/
				case "getOfflinePlayers":
					//get the offline players
					$r = $client->getOfflinePlayers(getAuthString("getOfflinePlayers"));
					var_dump($r);
					break;
				/***************************** getOps ****************************/
				case "getOps":
					//get the ops
					$r = $client->getOps(getAuthString("getOps"));
					var_dump($r);
					break;
				/***************************** getPlayers ****************************/
				case "getPlayers":
					//get the players
					$r = $client->getPlayers(getAuthString("getPlayers"));
					pre_r($r);
					//var_dump($r);
					break;
				/***************************** getPlugins ****************************/
				case "getPlugins":
					//get the ops
					$r = $client->getPlugins(getAuthString("getPlugins"));
					var_dump($r);
					break;
				/***************************** getServer *****************************/
				case "getServer":
					$r = $client->getServer(getAuthString("getServer"));
					var_dump($r);
					break;
				/***************************** getServerVersion *****************************/
				case "getServerVersion":
					$r = $client->getServerVersion(getAuthString("getServerVersion"));
					var_dump($r);
					break;
				/****************************** installPlugin ******************************/
				case "installPlugin":
					// install a plugin from bukkitdev
					$downloadUrl = $_POST['downloadUrl'];
					// the md5 can be found on the File download page on bukkitdev
					$md5 = $_POST['md5'];
					
					$result = $client->installPlugin(getAuthString("installPlugin"), $downloadUrl, $md5);		
					var_dump($result);
					break;
				/***************************** op *****************************/
				case "op":
					$playerName = $_POST['playerName'];
					$notify = isset($_POST['notify']);
					$r = $client->op(getAuthString("op"), $playerName, $notify);
					var_dump($r);
					break;
				/****************************** reloadServer ******************************/
				case "reloadServer":
					// reload the server's configuration
					$client->reloadServer(getAuthString("reloadServer"));
					echo "Results not applicable, reloadServer is a oneway (void) method";
					break;
				/****************************** reloadServer ******************************/
				case "runConsoleCommand":
					$command = $_POST['command'];
					$client->runConsoleCommand(getAuthString("runConsoleCommand"), $command);
					echo "Results not applicable, runConsoleCommand is a oneway (void) method";
					break;
				/***************************** setFileContents ****************************/
				case "setFileContents":
					//the file name
					$fileName = $_POST['fileName'];
					$fileContents = $_POST['fileContents'];
					
					//get the file's contents
					$r = $client->setFileContents(getAuthString("setFileContents"), $fileName, $fileContents);
					var_dump($r);
					break;
				/***************************** setWorldTime *****************************/
				case "setWorldTime":
					$worldName = $_POST['worldName'];
					$time = $_POST['time'];
					$r = $client->setWorldTime(getAuthString("setWorldTime"), $worldName, $time);
					var_dump($r);
					break;
				/********************************* no op *********************************/					
				default:
					break;
			}
			
			// 7. Close the connection
			echo "</div>Closing connection...";
			$transport->close();			
			echo "Done<br /><br />";
		} catch (org\phybros\thrift\EAuthException $aex) {
			echo "</div><p><strong class=\"error\">Error:</strong><br /><br />" . $aex->errorMessage . "</p>";
		} catch (org\phybros\thrift\EDataException $dex) {
			echo "</div><p><strong class=\"error\">Data Error:</strong><br /><br />" . $dex->errorMessage . "</p>";
		} catch (Exception $e) {
			echo "</div><p><strong class=\"error\">Unknown Error:</strong><br /><br />" . $e->getMessage() . "</p>";
			echo "<p><strong class=\"error\">Trace:</strong><br /><pre>" . $e->getTraceAsString() . "</pre></p>";
			var_dump($e);
		}

		echo "<hr />";
	}
?>
		<h2>announce<small>(authString, message)</small></h2>
		<form action="#results" method="post">
			<p>Message<br /><input type="text" name="message" /></p>
			<input name="submit" type="submit" value="Execute" />
			<input type="hidden" name="operation" value="announce" />
		</form>
		<hr />

		<h2>deOp<small>(authString, playerName)</small></h2>
		<form action="#results" method="post">
			<p>Player Name<br /><input type="text" name="playerName" /></p>
			<p>Notify?<br /><input type="checkbox" name="notify" value="Y" /></p>
			<input name="submit" type="submit" value="Execute" />
			<input type="hidden" name="operation" value="deOp" />
		</form>
		<hr />
		
		<h2>getBukkitVersion<small>(authString)</small></h2>
		<form action="#results" method="post">
			<input name="submit" type="submit" value="Execute" />
			<input type="hidden" name="operation" value="getBukkitVersion" />
		</form>
		<hr />
		
		<h2>getConsoleMessages<small>(authString, since)</small></h2>
		<form action="#results" method="post">
			<p>Since<br /><input type="text" name="since" /></p>
			<input name="submit" type="submit" value="Execute" />
			<input type="hidden" name="operation" value="getConsoleMessages" />
		</form>
		<hr />
		
		<h2>getFileContents<small>(authString, fileName)</small></h2>
		<form action="#results" method="post">
			<p>File Name<br /><input type="text" name="fileName" /></p>
			<input name="submit" type="submit" value="Execute" />
			<input type="hidden" name="operation" value="getFileContents" />
		</form>
		<hr />
		
		<h2>getOfflinePlayer<small>(authString, offlinePlayerName)</small></h2>
		<form action="#results" method="post">
			<p>Player Name<br /><input type="text" name="playerName" /></p>
			<input name="submit" type="submit" value="Execute" />
			<input type="hidden" name="operation" value="getOfflinePlayer" />
		</form>
		<hr />
		
		<h2>getOfflinePlayers<small>(authString)</small></h2>
		<form action="#results" method="post">
			<input name="submit" type="submit" value="Execute" />
			<input type="hidden" name="operation" value="getOfflinePlayers" />
		</form>
		<hr />
		
		<h2>getOps<small>(authString)</small></h2>
		<form action="#results" method="post">
			<input name="submit" type="submit" value="Execute" />
			<input type="hidden" name="operation" value="getOps" />
		</form>
		<hr />
		
		<h2>getPlayers<small>(authString)</small></h2>
		<form action="#results" method="post">
			<input name="submit" type="submit" value="Execute" />
			<input type="hidden" name="operation" value="getPlayers" />
		</form>
		<hr />
		
		<h2>getPlugins<small>(authString)</small></h2>
		<form action="#results" method="post">
			<input name="submit" type="submit" value="Execute" />
			<input type="hidden" name="operation" value="getPlugins" />
		</form>
		<hr />
		
		<h2>getServer<small>(authString)</small></h2>
		<form action="#results" method="post">
			<input name="submit" type="submit" value="Execute" />
			<input type="hidden" name="operation" value="getServer" />
		</form>
		<hr />
		
		<h2>op<small>(authString, playerName)</small></h2>
		<form action="#results" method="post">
			<p>Player Name<br /><input type="text" name="playerName" /></p>
			<p>Notify?<br /><input type="checkbox" name="notify" value="Y" /></p>
			<input name="submit" type="submit" value="Execute" />
			<input type="hidden" name="operation" value="op" />
		</form>
		<hr />
		
		<h2>getServerVersion<small>(authString)</small></h2>
		<form action="#results" method="post">
			<input name="submit" type="submit" value="Execute" />
			<input type="hidden" name="operation" value="getServerVersion" />
		</form>
		<hr />
		
		<h2>installPlugin<small>(authString, downloadUrl, md5)</small></h2>
		<form action="#results" method="post">
			<p>Download Url<br /><input type="text" name="downloadUrl" /></p>
			<p>MD5 File Hash<br /><input type="text" name="md5" /></p>
			<input name="submit" type="submit" value="Execute" />
			<input type="hidden" name="operation" value="installPlugin" />
		</form>
		<hr />
		
		<h2>reloadServer<small>(authString)</small></h2>
		<form action="#results" method="post">
			<input name="submit" type="submit" value="Execute" />
			<input type="hidden" name="operation" value="reloadServer" />
		</form>
		<hr />

		<h2>runConsoleCommand<small>(authString, command)</small></h2>
		<form action="#results" method="post">
			<p>Command<br /><input type="text" name="command" /></p>
			<input name="submit" type="submit" value="Execute" />
			<input type="hidden" name="operation" value="runConsoleCommand" />
		</form>
		<hr />
		
		<h2>setFileContents<small>(authString, fileName, fileContents)</small></h2>
		<form action="#results" method="post">
			<p>File Name<br /><input type="text" name="fileName" /></p>
			<p>File Contents<br /><textarea name="fileContents" rows="20" cols="80"></textarea></p>
			<input name="submit" type="submit" value="Execute" />
			<input type="hidden" name="operation" value="setFileContents" />
		</form>
		<hr />

		<h2>setWorldTime<small>(authString, worldName, time)</small></h2>
		<form action="#results" method="post">
			<p>World Name<br /><input type="text" name="worldName" /></p>
			<p>Time<br /><input type="text" name="time" /></p>
			<input name="submit" type="submit" value="Execute" />
			<input type="hidden" name="operation" value="setWorldTime" />
		</form>
		<hr />
		
	</body>
</html>