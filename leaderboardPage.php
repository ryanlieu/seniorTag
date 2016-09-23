<html>
    <head>
        <meta charset="utf-8">
        <title>LEADERBOARD</title>
        <link rel="stylesheet" href="leaderboardStyle.css">
        <link rel="shortcut icon" type="image/png" href="/favicon.png"/>
        <link href='https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic' rel='stylesheet' type='text/css'>
    </head>
    <body>
        <div id="navBar">
            <h1>SENIOR TAG</h1>
            <ul>
                <li><a href="tagPage.php">TARGET</a></li>
                <li><a href="userPage.php">YOU</a></li>
                <li><a href="leaderboardPage.php" id="currentPage">LEADERBOARD</a></li>
                <li><a href="rulesPage.php">RULES</a></li>
            </ul>
        </div>
        <div id="pageContainer"> 
            <h1>TOP SCORERS</h1>
            <div id="leaderBoard">
                <h1 id="1" class="scoreList">lol</h1>
                <h1 id="2" class="scoreList">lol</h1>
                <h1 id="3" class="scoreList">lol</h1>
                <h1 id="4" class="scoreList">lol</h1>
                <h1 id="5" class="scoreList">lol</h1>
            </div>
        </div>
    </body>
</html>

<?php
    include 'functions.php';
    //calls database
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

    //Selects five highest scorers
    $query = "SELECT * FROM SeniorNames WHERE email <> 'admin@lakesideschool.org' ORDER BY tagCount DESC LIMIT 5 ";
    $rows = querySQL($query, $db);
    //Displays top five scorers using javascript
    for($ii=1;$ii<count($rows)+1;$ii++){
        $currentRank=$ii;
        $fullName=textName($rows[$ii-1][1]);
        $currentUserName=$currentRank.'</br>'.$fullName.'</br>'."tag score:"." ".$rows[$ii-1][4];
        echo "<script type='text/javascript'>document.getElementById('$currentRank').innerHTML = '$currentUserName';</script>";
    }
?>