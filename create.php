<?php

include '_functions_everything.php';
$title = input("Please enter your website's name: ");
$root = input("Is your website located in a subfolder (like, https://example.com/abc)? If yes, type the folder's name, else - hit enter. ");
$url = input("Please enter the 404 page path (this can be any URL, like https://example.org, or ./404): ");
echo "\n";
file_put_contents('config.php', "<?php\n\ndefine('EV_SUBFOLDER', '".$root."');\ndefine('EV_404', '".$url."');\ndefine('EV_TITLE', '".$title."');");
echo "Great, configuration has been written. You can modify it later in config.php.\nDownloading and installing the latest template..\n";
file_put_contents('template.zip', file_get_contents('https://github.com/EverythingPHP/Storage/raw/main/template.zip'));
unzip('template.zip','./');
unlink('template.zip');
echo "All good.\nTemplate Wiki: https://github.com/EverythingPHP/Everything/wiki\n\nPlease set up requests rewriting to index.php.\n";
