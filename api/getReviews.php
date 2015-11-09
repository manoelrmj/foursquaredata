<?php
	include("_assets/php/classes/connection-class.php");

	$db = new SQLiteConnection("../sqlite_databases/reviews.db");
	
	if(!$db){
		echo "Erro ao acessar base de dados:\n";
		echo $db->lastErrorMsg();
	}
	
	// get parameters' id from URL
	$parameters = parse_url($_SERVER['REQUEST_URI']);
	if(isset($parameters['query']))
		parse_str($parameters['query'], $query);
	if(isset($query['product']))
		$product = $query['product'];

	$arrResponse = array();
	$rows = array();
	$reviews = array();
	
	if(isset($product)){
		$query = "SELECT * FROM reviews WHERE product = '" . $product . "'";
		$ret = $db->query($query);
		while($row = $ret->fetchArray(SQLITE3_ASSOC))
			array_push($reviews, $row);
		//var_dump($reviews);
		// Para cada review obtido, inserir as sentnças e os aspectos
		foreach($reviews as &$r){
			$sentences = array();
			$query = "SELECT * FROM sentences WHERE idReview = " . $r['idReview'];
			$ret = $db->query($query);
			while($row = $ret->fetchArray(SQLITE3_ASSOC))
				array_push($sentences, $row);
			foreach ($sentences as &$s) {
				$aspects = array();
				$query = "SELECT A.aspect FROM aspectsSentences AS AST JOIN aspects AS A WHERE idSentence = " . $s['idSentence'];
				$ret = $db->query($query);
				while($row = $ret->fetchArray(SQLITE3_ASSOC))
					array_push($aspects, $row);
				$s['aspects'] = $aspects;
			}
			$r['sentences'] = $sentences;
		}
		//var_dump($reviews);
		echo json_encode($reviews);
	}else{
		$arrMessage = array();
		array_push($arrMessage, "Parametro nao especificado");
		echo json_encode($arrMessage);
		exit();
	}
?>