<?php
//initializes session
session_start();
?>
<html>
    <head>
        <meta charset="utf-8">
        <title>SENIOR TAG</title>
        <link rel="stylesheet" href="style.css">
        <link rel="shortcut icon" type="image/png" href="/favicon.png"/>
        <link href='https://fonts.googleapis.com/css?family=Lato:400,900italic,900,300' rel='stylesheet' type='text/css'>
        <script src="script.js"></script>
    </head>
    <body>
    	<div id="backgroundCover" onclick="hideLogin()"></div>
		<div id="container">
	        <section>
	            <h1 id="titleCard">SENIOR TAG</h1>
	            <button class="buttStyle" id="buttLogin" type="button" onclick="showLogin()">LOG IN</button></br>
	            <button class="buttStyle" id="buttCreate" type="button" onclick="showCreate()">CREATE ACCOUNT</button>
	            <div id="loginForm" class="userInput">
	                <form method="POST">
	                    <p>Email</p>
	                    <input type="text" name="emailLog"/>
	                    <p>Password</p> 
	                    <input type="password" name="passLog" /><br/>
	                    <button type="submit" name="loginButton" id="loginButton">Log In</button>
	                </form>
	            </div>
	            <div id="createForm" class="userInput">
	                <form method="POST">
	                    <p>Email</p>
	                    <input type="text" name="emailCreate"/>
	                    <p>Password</p> 
	                    <input type="password" name="passCreate"/>
	                    <p>Confirm Password</p> 
	                    <input type="password" name="passCreateConfirm"/>
	                    <p>Student ID</p> 
	                    <input type="number" name="studentIDCreate"/ id="studentID">
	                    <button type="submit" name="createButton" id="createButton">Create</button>
	                </form>
	            </div>
	        </section>
	    </div>
        </div>
    </body>
</html>


<?php
    //Checks for form requesting post
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
       	//imports functions
       	include 'functions.php';

       	//Initialization of database (config references to SeniorTag database with port 8889)
       	require("config.php");
       	try {
	       	//New PDO from database
	        $db = new PDO("mysql:dbname=" . $GLOBALS["database"] .                  
	        ";host=" . $GLOBALS["hostname"] . ";port=" . $GLOBALS["port"], 
	        $GLOBALS["username"], $GLOBALS["password"]);
	        $db->setAttribute(PDO::ATTR_ERRMODE, 
	                          PDO::ERRMODE_EXCEPTION);
	    }
	    catch (PDOException $ex) {
	        echo $ex->getMessage();
	    }
	    catch(PDOException $e) {
	        echo $e->getMessage();
	    }

	    //CREATE BUTTON
        if (isset($_POST['createButton'])) {
        	//Imports variables for account creation from form
	    	$emailCreate = trim($_POST["emailCreate"]);
	       	$passwordCreate = trim($_POST["passCreate"]);
	        $passwordConfirmCreate = trim($_POST["passCreateConfirm"]);
	       	$studentIDCreate = $_POST["studentIDCreate"];
	       	//Default empty variables on account creation
	       	$tagCount = 0;
	       	$counterCount = 0;
	       	$toTag = 0;
	       	$toCounter = 0;
	       	$deadConfirm = 0;
	       	$tagConfirm = 0;
	       	$counterConfirm = 0;

        	//Checks for empty fields or for number validation
        	if(empty($emailCreate)||empty($passwordCreate)||empty($passwordConfirmCreate)||empty($studentIDCreate)) {
	            echo "<script type='text/javascript'>alert('Please fill out all inputs correctly');</script>";
	            exit();
       	 	}
       	 	//Checks for password confirmation
       	 	$passwordCreate = trim($_POST["passCreate"]);
       	 	if($passwordCreate != $passwordConfirmCreate) {
       	 		echo "<script type='text/javascript'>alert('Please enter the correct password in both fields');</script>";
       	 		exit();
       	 	}
       	 	//Checks for lakeside email at the end of string
       	 	if(strpos($emailCreate,'@lakesideschool.org') == false) {
			    echo "<script type='text/javascript'>alert('Please enter a Lakeside email');</script>";
			    exit();
			}

			//Checks if email is taken
			$query = "SELECT * FROM SeniorNames";
            $rows = querySQL($query, $db);
            for($ii=0;$ii<count($rows);$ii++){
            	echo $emailCreate;
            	if($emailCreate==$rows[$ii][1]){
            		echo "<script type='text/javascript'>alert('That student email is taken!');</script>";
			    	exit();
            	}
            }

			//Hashes password
			$passwordCreate = trim($_POST["passCreate"]);
	        $passwordCreate = password_hash($passwordCreate, PASSWORD_DEFAULT);
	        //SQL database insertion, scrubs input correctly
	        $sql = "INSERT INTO SeniorNames (email, password, studentID, tagCount, counterCount, toTag, toCounter, deadConfirm, tagConfirm, counterConfirm)
	        VALUES ('$emailCreate', '$passwordCreate', '$studentIDCreate', '$tagCount', '$counterCount', '$toTag', '$toCounter', '$deadConfirm', '$tagConfirm', '$counterConfirm')";
	        $statement = $db->prepare($sql);
	        $statement->execute(array('email' => $emailCreate, 'password' => $passwordCreate, 'passwordConfirm' => $passwordConfirmCreate, 'studentID' => $studentIDCreate, 'tagCount' => $tagCount, 'counterCount' => $counterCount, 'toTag' => $toTag, 'toCounter' => $toCounter, 'deadConfirm' => $deadConfirm, 'tagConfirm' => $tagConfirm, 'counterConfirm' => $counterConfirm));
        	echo "<script type='text/javascript'>alert('Account created!');</script>";
	    }

	    //LOGIN BUTTON
	    else if (isset($_POST['loginButton'])) {
	    	//Imports variables for login
	    	$emailLogin = trim($_POST["emailLog"]);
	    	$passwordLogin = trim($_POST["passLog"]);

	    	//Checks for empty variables
	    	if(empty($emailLogin)||empty($passwordLogin)){
	    		echo "<script type='text/javascript'>alert('Please enter a username and password');</script>";
	            exit();
	    	}

	    	//Verifies email and password
	    	$query = "SELECT * FROM SeniorNames WHERE email = '$emailLogin'";
            $rows = querySQL($query, $db);
            //If login is correct
            if(password_verify($passwordLogin, $rows[0][2])){
            	session_start();
            	$_SESSION["userInfoArray"] = $rows;
            	//Checks for admin login, directs to login page
            	if($rows[0][1]=="admin@lakesideschool.org"){
            		echo "<script type='text/javascript'>window.location.href='adminPage.php';</script>";
            	}
            	//Otherwise, sends to normal tag info page
            	else{
            		echo "<script type='text/javascript'>window.location.href='tagPage.php';</script>";
            	}
            }
            //If login is incorrect
            else{
				echo "<script type='text/javascript'>alert('Password is incorrect');</script>";
				exit();
            }
            
	    }
    }
?>