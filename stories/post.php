<?php
	$file = $_REQUEST['param1'];
	$c = file_get_contents($file);
	$c++;
	$fp = fopen($file, "r+");
	@fputs($fp,$c);
	fclose($fp);
?>