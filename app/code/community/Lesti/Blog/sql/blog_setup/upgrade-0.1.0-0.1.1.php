<?php
/**
 * Add data fields for thumbnail and main image
 */
$installer = $this;
$installer->startSetup();

$installer->run("
    
    ALTER TABLE `{$installer->getTable('blog/post')}`
        ADD `main_image` varchar(255) NULL COMMENT 'Main Image' AFTER `comment_count`;
    
");


$installer->endSetup();
