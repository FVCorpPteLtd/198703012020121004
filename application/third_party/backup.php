<?php
//Menghasilkan backup DB
backupDatabaseTables('localhost','root','','db_grosir_1405');//disini berarti saya akan membackup DB "codingan"

function backupDatabaseTables($dbHost,$dbUsername,$dbPassword,$dbName,$tables = '*'){

		//menghubungkan & memilih DB
    $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

	//Mendapatkan semua Table
	if($tables == '*'){
		$tables = array();
		$result = $db->query("SHOW TABLES");
		while($row = $result->fetch_row()){
			$tables[] = $row[0];
		}
	}else{
		$tables = is_array($tables)?$tables:explode(',',$tables);
	}
	$return = '';
	//Loop melalui Table
	foreach($tables as $table){
		$result = $db->query("SELECT * FROM $table");
		$numColumns = $result->field_count;

		$return .= "DROP TABLE $table;";

        $result2 = $db->query("SHOW CREATE TABLE $table");
        $row2 = $result2->fetch_row();

		$return .= "\n\n".$row2[1].";\n\n";

		for($i = 0; $i < $numColumns; $i++){
			while($row = $result->fetch_row()){
				$return .= "INSERT INTO $table VALUES(";
				for($j=0; $j < $numColumns; $j++){
					$row[$j] = addslashes($row[$j]);
					$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return .= '"'.$row[$j].'"' ; } else { $return .= '""'; }
					if ($j < ($numColumns-1)) { $return.= ','; }
				}
				$return .= ");\n";
			}
		}
		$return .= "\n\n\n";
	}
	date_default_timezone_set('Asia/Jakarta');
	//simpan file
	$handle = fopen('d:\backup\db-Backup-'.date('Y-m-d  H-i-s').'.sql','w+');
	fwrite($handle,$return);
	fclose($handle);
}
