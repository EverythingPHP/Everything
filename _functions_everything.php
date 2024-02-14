<?php

function unzip($from, $to){
    $zip = new ZipArchive;
    if ($zip->open($from) === TRUE) {
        $zip->extractTo($to);
        $zip->close();
        return true;
    }
    throw Exception("Unzip failed.");
}

function recurseRmdir($dir) {
  $files = array_diff(scandir($dir), array('.','..'));
  foreach ($files as $file) {
    (is_dir("$dir/$file") && !is_link("$dir/$file")) ? recurseRmdir("$dir/$file") : unlink("$dir/$file");
  }
  return rmdir($dir);
}

function deps_update(){
    echo "Downloading latest INDEX.db...\n";
    file_put_contents('deps/INDEX.db', file_get_contents('https://raw.githubusercontent.com/EverythingPHP/Storage/main/INDEX.db'));
    echo "Done updating.\n";
}

function input(string $prompt = null): string
{
    echo $prompt;
    $handle = fopen("php://stdin", "rb");
    $output = fgets($handle);
    return trim($output);
}
