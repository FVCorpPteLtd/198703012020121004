<?php
function encryptURL($qryStr)
{
	$keySalt = "pu5dat1nEsdm2020KueSIOn3ReerrrPP";
	$query = base64_encode(urlencode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($keySalt), $qryStr, MCRYPT_MODE_CBC, md5(md5($keySalt)))));
	return $query;
}

function decryptURL($qryStr)
{
	$keySalt = "pu5dat1nEsdm2020KueSIOn3ReerrrPP";
	$queryString = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($keySalt), urldecode(base64_decode($qryStr)), MCRYPT_MODE_CBC, md5(md5($keySalt))), "\0");   //this line of code decrypt the query string
	return $queryString;
}

function set_file_download($file_path, $file_alias)
{
	if (!file_exists($file_path) || !is_file($file_path)) {
		redirect("/");
	}

	$content_type = mime_content_type($file_path);
	$file_size = filesize($file_path);
	var_dump($content_type);
	var_dump($file_size);
	die;
	$content_disposition = "inline";

	if ($file_size > (10 * 1000000)) { // If bigger than 10 MB, do not attempt to open in browser. Download instead.
		$content_disposition = "attachment";
	}

	header("Content-type: {$content_type}");
	header('Content-Length: ' . $file_size);
	header("Pragma: public");
	header("Expires: -1");
	header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");

	header('Content-Disposition: ' . $content_disposition . '; filename="' . $file_alias . '"'); // The double quote, use to protect the $file_alias format

	// To allow big file
	if (ob_get_level()) {
		ob_end_clean();
	}

	// Chunk download if is a big file
	$chunksize = 1 * (1024 * 1024); // how many bytes per chunk
	if ($file_size > $chunksize) {
		$handle = fopen($file_path, 'rb');
		$buffer = '';
		while (!feof($handle)) {
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			ob_flush();
			flush();
		}
		fclose($handle);
	} else {
		readfile($file_path);
	}
}
