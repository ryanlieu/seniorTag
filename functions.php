<?php
function textName($targetName){
	$firstName = current(explode(".", $targetName));
    $sub = substr($targetName, strpos($targetName,".")+strlen("."),strlen($targetName));
    $lastName = substr($sub,0,strpos($sub,"@"));
    return ucfirst($firstName).' '.ucfirst($lastName);
}

function querySQL($query, $db){
	$results = $db->query($query);
    $rows = Array();
    for ($ii = 0; $ii < $results->rowCount(); $ii++) {
        $arow = $results->fetch(PDO::FETCH_NUM);
        $rows[$ii] = $arow; 
    }
    return $rows;
}

function executeSQL($sql, $db){
    $statement = $db->prepare($sql);
    $statement->execute();
}
?>