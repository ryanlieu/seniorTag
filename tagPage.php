<?php
session_start();
?>
<html>
    <head>
        <meta charset="utf-8">
        <title>TARGET</title>
        <link rel="stylesheet" href="tagStyle.css">
        <link rel="shortcut icon" type="image/png" href="/favicon.png"/>
        <link href='https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic' rel='stylesheet' type='text/css'>       
    </head>
    <body>
        <div id="navBar">
            <h1>SENIOR TAG</h1>
            <ul>
                <li><a href="tagPage.php" id="currentPage">TARGET</a></li>
                <li><a href="userPage.php">YOU</a></li>
                <li><a href="leaderboardPage.php">LEADERBOARD</a></li>
                <li><a href="rulesPage.php">RULES</a></li>
            </ul>
        </div>
        <div id="reportButtons">
            <form method="POST">
                <button class="buttStyle" name="buttTag" id="buttTag" type="submit">REPORT TAG</button></br>
                <button class="buttStyle" name="buttDeath" id="buttDeath" type="submit">REPORT DEATH</button></br>
            </form>
        </div>
        
        <div id="cover">
            <h1 id="deadNotification">YOU ARE DEAD</h1>
        </div>
        <div id="tagSection">
            <div id="tagCard">
                <div id="cardHeader">
                    <h1>YOUR TARGET</h1>
                </div>
                <div id="userInfo">
                    <div id="imageDiv"></div>
                    <h2 id="userName">SANJEEV JANARTHANAN</h2>
                </div>
                <div id="tagCount">
                    <h3 id="tagNumber">4</h3>
                    <p>total tags</p>
                </div>
            </div>
        </div>
    </body>
</html>



<?php

    //Carries on user login
    $userID=$_SESSION["userInfoArray"][0][0];

    //PDO import
    require("config.php");
    include 'functions.php';
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

    //Pulls user info from user login
    $query = "SELECT * FROM SeniorNames WHERE id = '$userID'";
    $userInfo=querySQL($query, $db);
    $userToTag = $userInfo[0][6];
    $userToCounter = $userInfo[0][7];
    $userDeadConfirm = $userInfo[0][8];
    $userTagConfirm = $userInfo[0][9];

    //Pulls target info
    $query = "SELECT * FROM SeniorNames WHERE id = '$userToTag'";
    $targetInfo=querySQL($query, $db);
    $targetID = $targetInfo[0][0];
    $targetToTag = $targetInfo[0][6];
    $targetDeadConfirm = $targetInfo[0][8];

    //Pulls counter info
    $query = "SELECT * FROM SeniorNames WHERE id = '$userToCounter'";
    $counterInfo = querySQL($query, $db);
    $counterID = $counterInfo[0][0];
    $counterToTag = $counterInfo[0][6];
    $counterTagConfirm = $counterInfo[0][9];
    $counterDeadConfirm = $counterInfo[0][8];

    //Index of states: deathConfirm, tagConfirm, counterConfirm are all at -1, 0, or 1. -1 is dead, 0 is currently active 
    //(ie. if tagConfirm=0, they are currently tagging someone, if tagConfirm=1, they are waiting on confirmation)

    //Blocks the page if user is dead
    if($userDeadConfirm==-1){
        echo "<script type='text/javascript'>alert('You are dead! You will not be able to interact with the target page from now on.');</script>";
        echo "<script type='text/javascript'>document.getElementById('cover').style.zIndex = 1;</script>";
        echo "<script type='text/javascript'>document.getElementById('cover').style.backgroundColor='#e74c3c';</script>";
        echo "<script type='text/javascript'>document.getElementById('deadNotification').style.position='default';</script>";
        exit();
    }

    //Alerts user if target has not confirmed tag
    if($targetDeadConfirm==0&&$userTagConfirm==1){
        echo "<script type='text/javascript'>alert('Your target has not confirmed their tag! Please get them to confirm as soon as possible.');</script>";
        echo "<script type='text/javascript'>document.getElementById('cover').style.zIndex = 1;</script>";
        echo "<script type='text/javascript'>document.getElementById('cover').style.backgroundColor='#e74c3c';</script>";
        echo "<script type='text/javascript'>document.getElementById('deadNotification').style.position='default';</script>";
        echo "<script type='text/javascript'>document.getElementById('deadNotification').innerHTML = 'WAITING ON CONFIRMATION';</script>";
    }

    //Alerts user if they confirmed their death but their tagger didn't
    if($userDeadConfirm==1&&$counterTagConfirm==0){
        echo "<script type='text/javascript'>alert('Your counter has not confirmed their tag! Please get them to confirm as soon as possible.');</script>";
        echo "<script type='text/javascript'>document.getElementById('cover').style.zIndex = 1;</script>";
        echo "<script type='text/javascript'>document.getElementById('cover').style.backgroundColor='#e74c3c';</script>";
        echo "<script type='text/javascript'>document.getElementById('deadNotification').style.position='default';</script>";
        echo "<script type='text/javascript'>document.getElementById('deadNotification').innerHTML = 'WAITING ON CONFIRMATION';</script>";
    }

    //Checks if both user and target have confirmed user has tagged them
    //NOTE: This requires the tagger to log in in order to update the database, this makes sense as a dead user probably won't
    //log in again after reporting their death
    if($targetDeadConfirm==1&&$userTagConfirm==1){
        //Alerts user that tag has been confirmed
        echo "<script type='text/javascript'>alert('Your tag has been confirmed! You now have a new target');</script>";
        //Sets users toTag to target's toTag
        $sql = "UPDATE SeniorNames SET toTag=$targetToTag, tagCount=tagCount+1, tagConfirm=0 WHERE id=$userID";
        executeSQL($sql, $db);
        //Sets target's info to negatives in order to determine tagged state
        $sql = "UPDATE SeniorNames SET toTag=-1, toCounter=-1, deadConfirm=-1, tagConfirm=-1, counterConfirm=-1 WHERE id=$targetID";
        executeSQL($sql, $db);
        //Sets users toTag to target's toTag
        $sql = "UPDATE SeniorNames SET toCounter=$userID WHERE id=$targetToTag";
        executeSQL($sql, $db);
        
        //Updates user info to reflect new data
        $query = "SELECT * FROM SeniorNames WHERE id = '$userID'";
        $userInfo=querySQL($query, $db);
        $userToTag = $userInfo[0][6];
       
        //Updates to new target
        $query = "SELECT * FROM SeniorNames WHERE id = '$userToTag'";
        $targetInfo=querySQL($query, $db);
    }

    //Loads target info to be displayed
    $targetID = $targetInfo[0][0];
    $targetName = $targetInfo[0][1];
    $targetStudentID = $targetInfo[0][3];
    $targetTags = $targetInfo[0][4];
    $targetTagCounters = $targetInfo[0][5];
    $targetTarget = $targetInfo[0][6];

    //Displays target info
    $targetStudentID = "url(tagImages/" . $targetStudentID . ".jpg)";
    $fullName=strtoupper(textName($targetName));
    echo "<script type='text/javascript'>document.getElementById('userName').innerHTML = '$fullName'</script>";
    echo "<script type='text/javascript'>document.getElementById('tagNumber').innerHTML = '$targetTags'</script>";
    echo "<script type='text/javascript'>document.getElementById('tagCounterNumber').innerHTML = '$targetTagCounters'</script>";
    echo "<script type='text/javascript'>document.getElementById('imageDiv').style.backgroundImage = '$targetStudentID'</script>";
    
    //Confirmation buttons
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['buttDeath'])) {
            $sql = "UPDATE SeniorNames SET deadConfirm=1 WHERE id=$userID";
            executeSQL($sql, $db);
            echo "<script type='text/javascript'>window.location.href='tagPage.php';</script>";
        }
        if (isset($_POST['buttTag'])) {
            $sql = "UPDATE SeniorNames SET tagConfirm=1 WHERE id=$userID";
            executeSQL($sql, $db);
            echo "<script type='text/javascript'>window.location.href='tagPage.php';</script>";
        }
    }
?>







