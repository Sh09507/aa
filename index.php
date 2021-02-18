<?php 
	// Author: Sabrina Hill
	// Due Date:   2/9/2021
	// Attacking Authentication
	// It took about 21 minutes to brute force the Username and Password
$login = False;
$username = "";
$password = "";
//Connect to DB
require_once 'database.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	try {
        $query = 'SELECT failed_attempts FROM Attacking_Authentication WHERE IP=:ip ORDER BY ID DESC LIMIT 1;';
        $dbquery = $myDBconnection -> prepare($query);
        $dbquery -> bindValue(':ip', $_SERVER['REMOTE_ADDR']);
        $dbquery -> execute();
        $result = $dbquery -> fetch();
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
		echo "<p>An error occurred while trying to retrieve data from the table: $error_message </p>";
    } if ($result = 3) {
		sleep(43200);
	}
	$username = $_POST['username'];
	$password = $_POST['password'];
	if ($_POST['username'] === 'ansible' && $_POST['password'] === 'abc123') {
		$login = True;
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
	} else {
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
		if (empty($result)) {
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
			$result = $result + 1 ;
			try {
				$query = 'INSERT INTO Attacking_Authentication (IP, user_name, password, date, failed_attempts) VALUES (:ip, :user, :pass, NOW(), :result);';
				$dbquery = $myDBconnection -> prepare($query);
				$dbquery -> bindValue(':ip', $_SERVER['REMOTE_ADDR']);
				$dbquery -> bindValue(':user', $username); 
				$dbquery -> bindValue(':pass', $password);
				$dbquery -> bindValue(':result', $result);
				$dbquery -> execute();
			} catch (PDOException $e) {
				$error_message = $e->getMessage();				
				echo "<p>An error occurred while trying to retrieve data from the table: $error_message </p>";
			}if ($result = 3) {
				sleep(43200);
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