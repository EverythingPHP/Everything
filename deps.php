<?php
include '_functions_everything.php';
if(!file_exists('deps'))
    mkdir('deps');
if(!file_exists('deps/INDEX.db'))
    deps_update();

$syntax = "Syntax: php deps.php [action] (argument)\n\nphp deps.php list\nphp deps.php update\nphp deps.php list 15\nphp deps.php install PACKAGE\nphp deps.php remove PACKAGE\n";
$operations = ['list', 'install', 'remove', 'update'];
$operations_args = ['install', 'remove'];

if(count($argv) < 2){
    die("Not enough arguments.\n".$syntax);
}

$operation = $argv[1];
if (count($argv) >=3)
    $arg = $argv[2];
else
    $arg = NULL;


if(!in_array($operation, $operations))
    die("Operation not supported.\n".$syntax);

if(is_null($arg) and in_array($operation, $operations_args))
    die("No argument provided.\n".$syntax);

$al_file = 'deps/autoload.php';
$db = new SQLite3('deps/INDEX.db');
switch($operation){
    case 'update':
        deps_update();
        break;
    case 'list':
        $limit = (!is_null($arg) and $operation == 'list') ? 'LIMIT '.strval(intval($arg)) : '';
	$packages = $db->query('SELECT * FROM packages '.$limit);
	while ($res=$packages->fetchArray(1))
	{
	    echo $res['name'].' | '.$res['title'].' | '.$res['desc']."\n";
	}
	echo "\n";
	break;
    case 'install':
	$package = $db->query("SELECT * FROM packages WHERE name = '".$db->escapeString($arg)."'");
	if(!$package->numColumns()>=1)
	    die("Package doesn't exist.\n");
	$package = $package->fetchArray(1);
	echo 'Downloading..';
	$folder= 'deps/'.basename($package['name']);
	$zip = $folder.'/tmppackage.zip';
	if(file_exists($folder))
	    die("Package is already installed.\n");
	mkdir($folder);
	file_put_contents($zip, file_get_contents($package['package']));
	unzip($zip, $folder.'/'.basename($package['name']));
	unlink($zip);
	echo "Downloaded the main package, adding to autoload\n";
	$al = (file_exists($al_file)) ? file_get_contents($al_file) : '';
	file_put_contents($al_file, $al."\n<?php /* MARK_".$package['name']." */  ?>\n\n".file_get_contents($package['meta_url'])."\n<?php /* END_".$package['name']." */  ?>");
	echo "Package ".$package['name']." is now installed.\n";
	break;
    case 'remove':
	$folder= 'deps/'.basename($arg);
	if(!file_exists($folder))
	    die("Package is not installed.\n");
	recurseRmdir($folder);
	try{
	    $file = file_get_contents($al_file);
	    $head = "<?php /* MARK_".basename($arg)." */  ?>";
	    $tail = "<?php /* END_".basename($arg)." */  ?>";
	    $file = str_replace([explode($tail, explode($head, $file)[1])[0], $head, $tail], '', $file);
	    file_put_contents($al_file, $file);
	}catch(Throwable $e){}
	echo "Done.\n";
	break;
}
