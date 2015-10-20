<?php
	class SQLiteConnection extends SQLite3{
		function __construct($database){
			$this->open($database);
		}
	}
?>