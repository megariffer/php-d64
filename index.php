<?php

use PhpD64\Disk;

require_once 'vendor/autoload.php';
require_once 'functions.php';

$disk = new Disk();
$disk->loadFromFile('test.d64');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>PHP-D64 test page</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<div class="d64-directory">
    <div class="header">
        <div>0</div>
        <div class="inverse">"<?php echo petscii_to_html($disk->getName()) ?>"</div>
        <div class="inverse"><?php echo utf8_encode($disk->getHeader()) ?></div>
    </div>
    <div class="files">
        <?php foreach ($disk->getDirectory() as $file) { ?>
            <div class="file">
                <div class="size"><?php echo $file->getSize() ?></div>
                <div class="name">
                    <?php
                        $file_name = petscii_to_html($file->getName());
                        echo str_replace(' ', '&nbsp;', $file_name);
                    ?>
                </div>
                <div class="type"><?php echo $file->getFileType() ?></div>
            </div>
        <?php } ?>
    </div>
</div>
<?php echo $disk->getFreeBlocks() ?> BLOCKS FREE.
</body>
</html>
