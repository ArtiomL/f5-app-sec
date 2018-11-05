<?php 



function recursiveRemove($dir) {
    $structure = glob(rtrim($dir, "/").'/*');
    if (is_array($structure)) {
        foreach($structure as $file) {
            if (is_dir($file)) recursiveRemove($file);
            elseif (is_file($file)) unlink($file);
        }
    }
    rmdir($dir);
}

$dir = getcwd() . '/config_files/';
$reports_structure = glob(rtrim($dir, "/").'/*');

if (is_array($reports_structure)) 
{
	foreach($reports_structure as $file) 
	{
		if (is_dir($file)) recursiveRemove($file);
		elseif (is_file($file)) unlink($file);
	}
}
Header ("Location:index.php?error=0");
?>