<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 27.03.13
 * Time: 00:16
 * To change this template use File | Settings | File Templates.
 */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'blog/post'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('blog/post'))
    ->addColumn('post_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Post ID')
    ->addColumn('author_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'User ID')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
    ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
    ), 'Updated At')
    ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
    ), 'Page Content')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true
    ), 'Page Title')
    ->addColumn('excerpt', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true
    ), 'Excerpt')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
    ), 'Status')
    ->addColumn('identifier', Varien_Db_Ddl_Table::TYPE_TEXT, 200, array(
        'nullable'  => true,
        'default'   => null,
    ), 'Post String Identifier')
    ->addIndex($installer->getIdxName('blog/post', array('identifier')),
        array('identifier'))
    ->addForeignKey($installer->getFkName('blog/post', 'author_id', 'admin/user', 'user_id'),
        'author_id', $installer->getTable('admin/user'), 'user_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Blog Post Table');
$installer->getConnection()->createTable($table);

// hier gehts weiter

$installer->endSetup();