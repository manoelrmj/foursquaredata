<?php
	$response = file_get_contents("http://sentiplace.esy.es/api/getSentences.php?aspect=12");
	$decoded_response = json_decode($response);
	foreach ($decoded_response as $sentence) {
		//var_dump($sentence);
		echo "Sentence ID: " . $sentence->idSentence . '<br>';
		echo "Comment ID: " . $sentence->idComment . '<br>';
		echo "Polarity: " . $sentence->sentencePolarity . '<br>';
		echo "Text: " . $sentence->sentence . '<br>';
		echo "Aspect: " . $sentence->idAspect . '<br>';
		echo "---------- <br>";
	}
?>