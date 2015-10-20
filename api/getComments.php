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
	
	if(isset($place_id) && isset($aspect_id)){
		$query = "SELECT S.idSentence, S.idComment, S.sentencePolarity, S.sentence, C.idComment, C.idPlace, C.totalPolarity FROM sentences S JOIN comments C ON S.idComment = C.idComment WHERE S.idComment IN (SELECT S.idComment FROM 'sentences' AS S JOIN 'aspectsSentences' AS AST ON S.idSentence = AST.idSentence WHERE idAspect = " . $aspect_id . ") AND C.idPlace = '". $place_id . "' ORDER BY S.idComment ASC";
	}elseif(isset($place_id)){
		$query = "SELECT S.idSentence, S.idComment, S.sentencePolarity, S.sentence, C.idPlace, C.totalPolarity FROM sentences AS S JOIN comments AS C ON S.idComment = C.idComment WHERE C.idPlace = '" . $place_id . "' ORDER BY S.idComment ASC";
	}elseif(isset($aspect_id)){
		$query = "SELECT S.idSentence, S.idComment, S.sentencePolarity, S.sentence, C.idComment, C.idPlace, C.totalPolarity FROM sentences S JOIN comments C ON S.idComment = C.idComment WHERE S.idComment IN (SELECT S.idComment FROM 'sentences' AS S JOIN 'aspectsSentences' AS AST ON S.idSentence = AST.idSentence WHERE idAspect = " . $aspect_id . ") ORDER BY S.idComment ASC";
	}else{
		$arrMessage = array();
		array_push($arrMessage, "Nenhum parametro especificado");
		echo json_encode($arrMessage);
		exit();
	}

	$ret = $db->query($query);
	$comments = array();
	$sentences = array();
	while($row = $ret->fetchArray(SQLITE3_ASSOC)){
		if(!isset($idComment)) // Primeira iteração, inicializa $idComment
			$idComment = $row['idComment'];
		if($row['idComment'] != $idComment){ // Um novo conjunto de sentences com idComment diferente foi encontrada
			$comments[$idComment] = $sentences;
			$sentences = array();
			array_push($sentences, $row);
			$idComment = $row['idComment'];
		}else{ // Outra(s) sentenças com o mesmo idComment foram encontradas, portanto pertencem ao mesmo comentário
			array_push($sentences, $row);
			$idComment = $row['idComment'];
		}			
	}
	// Após a última iteração, atribuir ao vetor resultante $comments as últimas sentenças
	$comments[$idComment] = $sentences;
	//var_dump($comments);
	echo json_encode($comments)
?>