<?php
session_start();
?>
<html>
    <head>
        <meta charset="utf-8">
        <title>ADMIN</title>
        <link rel="shortcut icon" type="image/png" href="/favicon.png"/>
        <link rel="stylesheet" href="style.css">
        <link href='https://fonts.googleapis.com/css?family=Lato:400,900italic,900,300' rel='stylesheet' type='text/css'>
    </head>
    <body>
    	<div id="backgroundCover" onclick="hideLogin()"></div>
		<div id="container">
	        <section>
	            <h1 id="titleCard">ADMIN ACCOUNT</h1>
	            <form method="POST">
	            	<button class="buttStyle" name="buttLink" id="buttLink" type="submit">LINK ALL USERS</button></br>
	            </form>
	        </section>
	    </div>
        </div>
    </body>
</html>


<?php
    //Checks for form requesting post
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

	    //Assign users targets
        if (isset($_POST['buttLink'])) {
            //All users except admin@lakesideschool.org
        	$query = "SELECT id FROM SeniorNames WHERE email <> 'admin@lakesideschool.org'";
            $targetAssign = querySQL($query, $db);
            //2d to 1d array
            $targetList = array();
            for($yy=0;$yy<count($targetAssign);$yy++) {
            	$targetList[$yy]=$targetAssign[$yy][0];
            }

           	//Sets user scores to 0
 			$sql = "UPDATE SeniorNames SET tagCount=0, counterCount=0, toTag=0, toCounter=0, deadConfirm=0, tagConfirm=0, counterConfirm=0";
		    executeSQL($sql, $db);

            //Uses fisher-yates shuffle algorithm to randomize order in new array targetOrder
            //targetOrder is ordered like [2,1,3,5], ie. 2 is tagging 1, 1 is tagging 3, 3 is tagging 5, 5 is tagging 1 etc.
            $targetOrder = array();
		    $targetOrder = array_values($targetList);
            $seed = 1;
            @mt_srand($seed);
		    for ($ii=count($targetOrder)-1;$ii>0;$ii--)
		    {
		        $index = @mt_rand(0, $ii);
		        $tmp = $targetOrder[$ii];
		        $targetOrder[$ii] = $targetOrder[$index];
		        $targetOrder[$index] = $tmp;
		    }
           	
           	//Sets tag targets off of target order array, counter is just the opposite
            $lastIndex = count($targetOrder)-1;
            for($zz=0;$zz<count($targetList);$zz++){
            	if($zz==$lastIndex){
            		break;
            	}
            	$nextIndex = $zz+1;
            	$sql = "UPDATE SeniorNames SET toTag=$targetOrder[$nextIndex] WHERE id=$targetOrder[$zz]";
		        executeSQL($sql, $db);
		        $sql = "UPDATE SeniorNames SET toCounter=$targetOrder[$zz] WHERE id=$targetOrder[$nextIndex]";
		        executeSQL($sql, $db);
            }
            //Case for last index
            $sql = "UPDATE SeniorNames SET toTag=$targetOrder[0] WHERE id=$targetOrder[$lastIndex]";
		    executeSQL($sql, $db);
		    $sql = "UPDATE SeniorNames SET toCounter=$targetOrder[$lastIndex] WHERE id=$targetOrder[0]";
		    executeSQL($sql, $db);
		    echo "<script type='text/javascript'>alert('All users have been assigned a target!');</script>";
        }
    }
?>