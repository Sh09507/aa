<?php 
	// Author: Sabrina Hill
	// Due Date:   2/9/2021
	// Attacking Authentication
	// It took about 21 minutes to brute force the Username and Password
	// I used a database to keep track of the IP of users failing login attempts more than 3 times, when they have attepemted over the limited chances they are locked out from loging in again for 12 hours. After the 12 hours they can try again 3 more times. The database records the username and passwords used to attempt logging in and when they tried logging in.
$login = False;
$username = "";
$password = "";
//Connects to Database
require_once 'database.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	//Will check if the user already has failed login attempts logged in the database. If they have 3 failed attempts logged in the data base the IP address will be locked out for 12 hours. Each failed attempt is logged individually, so the queary pulls the most recent attempt of that IP address in decending order.
	try {
        $query = 'SELECT failed_attempts FROM Attacking_Authentication WHERE IP=:ip ORDER BY ID DESC LIMIT 1;';
        $dbquery = $myDBconnection -> prepare($query);
        $dbquery -> bindValue(':ip', $_SERVER['REMOTE_ADDR']);
        $dbquery -> execute();
        $result = $dbquery -> fetch();
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
		echo "<p>An error occurred while trying to retrieve data from the table: $error_message </p>";
    } 
	if (!empty($result["failed_attempts"])) {
		if ($result["failed_attempts"] >= 3) {
			//sleep looks like infinite loading, code stalls for 12 hours
			sleep(43200);
		}
	}
	$username = $_POST['username'];
	$password = $_POST['password'];
	if ($_POST['username'] === 'ansible' && $_POST['password'] === 'abc123') {
		$login = True;
		//A successful login will result in the failed attempt count as 0, thus no penalty
		try {
            $query = 'INSERT INTO Attacking_Authentication (IP, user_name, password, date, failed_attempts) VALUES (:ip, :user, :pass, NOW(), 0);';
            $dbquery = $myDBconnection -> prepare($query);
            $dbquery -> bindValue(':ip', $_SERVER['REMOTE_ADDR']);
            $dbquery -> bindValue(':user', $username); 
            $dbquery -> bindValue(':pass', $password);
            $dbquery -> execute();
		} catch (PDOException $e) {
			$error_message = $e->getMessage();				
			echo "<p>An error occurred while trying to retrieve data from the table: $error_message </p>";
		}
	// else statement for a failed login
	} else {
		// failed login will result in quearing the database to check the current count of failed attempts
		try {
            $query = 'SELECT failed_attempts FROM Attacking_Authentication WHERE IP=:ip ORDER BY ID DESC LIMIT 1;';
            $dbquery = $myDBconnection -> prepare($query);
            $dbquery -> bindValue(':ip', $_SERVER['REMOTE_ADDR']);
            $dbquery -> execute();
            $result = $dbquery -> fetch();
        } catch (PDOException $e) {
            $error_message = $e->getMessage();
            echo "<p>An error occurred while trying to retrieve data from the table: $error_message </p>";
        }
		// if no attempts have been logged then the database will be quearied to set the failed attempt count to 1
		if (empty($result["failed_attempts"])) {
			try {
				$query = 'INSERT INTO Attacking_Authentication (IP, user_name, password, date, failed_attempts) VALUES (:ip, :user, :pass, NOW(), 1);';
				$dbquery = $myDBconnection -> prepare($query);
				$dbquery -> bindValue(':ip', $_SERVER['REMOTE_ADDR']);
				$dbquery -> bindValue(':user', $username); 
				$dbquery -> bindValue(':pass', $password);
				$dbquery -> execute();
			} catch (PDOException $e) {
				$error_message = $e->getMessage();				
				echo "<p>An error occurred while trying to retrieve data from the table: $error_message </p>";
			}
		} else {
			// If the user as failed attempts logged then the database takes the current failed attempts count and adds 1 
			$result["failed_attempts"] = $result["failed_attempts"] + 1 ;
			try {
				$query = 'INSERT INTO Attacking_Authentication (IP, user_name, password, date, failed_attempts) VALUES (:ip, :user, :pass, NOW(), :result);';
				$dbquery = $myDBconnection -> prepare($query);
				$dbquery -> bindValue(':ip', $_SERVER['REMOTE_ADDR']);
				$dbquery -> bindValue(':user', $username); 
				$dbquery -> bindValue(':pass', $password);
				$dbquery -> bindValue(':result', $result["failed_attempts"]);
				$dbquery -> execute();
			} catch (PDOException $e) {
				$error_message = $e->getMessage();				
				echo "<p>An error occurred while trying to retrieve data from the table: $error_message </p>";
			}
		}
	}
}

?>
<!doctype html>
<html lang="en-US">
	<head>
		<title>Login page</title>
		<meta name="description" content="Login page">
		<meta name="author" content="Russell Thackston">
		
		<style>
			body {
			  background-color: linen;
			  padding: 10px;
			}

			fieldset{
				max-width: 300px;
				border-radius: 10px;
			}
			
			label{
				width: 75px;
				display: inline-block;
				padding: 5px;
			}
		</style>
		
	</head>
	<body>
		<?php if ($login) { ?>
			<div>
				Login successful.
			</div>
		<?php } ?>
		<form action="index.php" method="post">
			<fieldset>
				<legend>Login</legend>
				<label for="username">Username</label>
				<input name="username" id="username" type="text" value="<?php echo $username; ?>">
				<br>
				<label for="password">Password</label>
				<input name="password" id="password" type="password" value="<?php echo $password; ?>">
				<br>
				<input type="submit" value="Login">
			</fieldset>
		</form>
	</body>
</html>