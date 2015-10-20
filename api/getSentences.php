<?php
	include("_assets/php/classes/connection-class.php");

	$db = new SQLiteConnection("../sqlite_databases/foursquare.db");
	
	if(!$db){
		echo "Erro ao acessar base de dados:\n";
		echo $db->lastErrorMsg();
	}
	
	// get parameters' id from URL
	$parameters = parse_url($_SERVER['REQUEST_URI']);
	if(isset($parameters['query']))
		parse_str($parameters['query'], $query);
	if(isset($query['place']))
		$place_id = $query['place'];
	if(isset($query['place_name']))
		$place_name = $query['place_name'];	
	if(isset($query['aspect']))
		$aspect_id = $query['aspect'];	

	$query = "SELECT * FROM 'sentences' AS S ";
	if(isset($place_id) && isset($aspect_id)){
		$query .= "JOIN 'comments' AS C ON S.idComment = C.idComment JOIN 'aspectsSentences' AS AST ON S.idSentence = AST.idSentence WHERE idPlace = '" . $place_id . "' AND idAspect = " . $aspect_id;
	}elseif(isset($place_name) && isset($aspect_id)){
		$query .= "JOIN 'comments' AS C ON S.idComment = C.idComment JOIN 'aspectsSentences' AS AST ON S.idSentence = AST.idSentence JOIN 'places' AS P ON C.idPlace = P.idPlace WHERE name = '" . $place_name . "' AND idAspect = " . $aspect_id;
	}elseif(isset($place_id)){
		$query .= "JOIN 'comments' AS C ON S.idComment = C.idComment WHERE idPlace = '" . $place_id . "'";
	}elseif(isset($place_name)){
		$query .= "JOIN 'comments' AS C ON S.idComment = C.idComment JOIN 'places' AS P ON C.idPlace = C.idPlace WHERE name = '". $place_name . "'";
	}elseif(isset($aspect_id)){
		$query .= "JOIN 'aspectsSentences' AS AST ON S.idSentence = AST.idSentence WHERE idAspect = " . $aspect_id;
	}else{
		$arrMessage = array();
		array_push($arrMessage, "Nenhum parametro especificado");
		echo json_encode($arrMessage);
		exit();
	}
	
	$ret = $db->query($query);
	$rows = array();
	while($row = $ret->fetchArray(SQLITE3_ASSOC)){
		array_push($rows, $row);
	}	
	//var_dump($rows);
	echo json_encode($rows)
?>