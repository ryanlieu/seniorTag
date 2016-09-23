<?php
session_start();
?>
<html>
    <head>
        <meta charset="utf-8">
        <title>YOU</title>
        <link rel="stylesheet" href="tagStyle.css">
        <link rel="shortcut icon" type="image/png" href="/favicon.png"/>
        <link href='https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic' rel='stylesheet' type='text/css'>       
    </head>
    <body>
        <div id="navBar">
            <h1>SENIOR TAG</h1>
            <ul>
                <li><a href="tagPage.php">TARGET</a></li>
                <li><a href="userPage.php" id="currentPage">YOU</a></li>
                <li><a href="leaderboardPage.php">LEADERBOARD</a></li>
                <li><a href="rulesPage.php">RULES</a></li>
            </ul>
        </div>

        <div id="tagSection" style="margin-left:15vw;">
            <div id="tagCard">
                <div id="cardHeader">
                    <h1>YOUR INFO</h1>
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
    include 'functions.php';

    //Carries on user login
    $userID=$_SESSION["userInfoArray"][0][0];

    //PDO import
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

    //Pulls user info from user login
    $query = "SELECT * FROM SeniorNames WHERE id = '$userID'";
    $userInfo=array();
    $userInfo=querySQL($query, $db);

    //Loads user info to be displayed
    $userID = $userInfo[0][0];
    $userName = $userInfo[0][1];
    $userStudentID = $userInfo[0][3];
    $userTags = $userInfo[0][4];
    $userTagCounters = $userInfo[0][5];
    $useruser = $userInfo[0][6];

    //Displays user info
    $userStudentID = "url(tagImages/" . $userStudentID . ".jpg)";
    $firstName = current(explode(".", $userName));
    $sub = substr($userName, strpos($userName,".")+strlen("."),strlen($userName));
    $lastName = substr($sub,0,strpos($sub,"@"));
    $fullName = strtoupper($firstName) . " " . strtoupper($lastName);
    echo "<script type='text/javascript'>document.getElementById('userName').innerHTML = '$fullName'</script>";
    echo "<script type='text/javascript'>document.getElementById('tagNumber').innerHTML = '$userTags'</script>";
    echo "<script type='text/javascript'>document.getElementById('tagCounterNumber').innerHTML = '$userTagCounters'</script>";
    echo "<script type='text/javascript'>document.getElementById('imageDiv').style.backgroundImage = '$userStudentID'</script>";
    
?>