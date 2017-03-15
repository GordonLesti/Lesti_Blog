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
 * Create table 'blog/author'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('blog/author'))
    ->addColumn('author_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Author ID')
    ->addColumn('admin_user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ), 'Admin User ID')
    ->addColumn('author_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'primary'   => true,
    ), 'Blog User Name')
    ->addForeignKey($installer->getFkName('blog/author', 'admin_user_id', 'admin/user', 'user_id'),
        'admin_user_id', $installer->getTable('admin/user'), 'user_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Blog Author Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'blog/post'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('blog/post'))
    ->addColumn('post_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Post ID')
    ->addColumn('author_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Post Author ID')
    ->addColumn('creation_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Post Creation Time')
    ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Post Modification Time')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '1',
    ), 'Is Post Active')
    ->addColumn('allow_comments', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '1',
    ), 'Allow Comments')
    ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
    ), 'Post Content')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true
    ), 'Post Title')
    ->addColumn('identifier', Varien_Db_Ddl_Table::TYPE_TEXT, 200, array(
        'nullable'  => true,
        'default'   => null,
    ), 'Post String Identifier')
    ->addColumn('comment_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0'
    ), 'Post Comment Count')
    ->addIndex($installer->getIdxName('blog/post', array('identifier')),
        array('identifier'))
    ->addForeignKey($installer->getFkName('blog/post', 'author_id', 'blog/author', 'author_id'),
        'author_id', $installer->getTable('blog/author'), 'author_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Blog Post Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'blog/post_store'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('blog/post_store'))
    ->addColumn('post_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'primary'   => true,
    ), 'Post ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Store ID')
    ->addIndex($installer->getIdxName('blog/post_store', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('blog/post_store', 'post_id', 'blog/post', 'post_id'),
        'post_id', $installer->getTable('blog/post'), 'post_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('blog/post_store', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Blog Post To Store Linkage Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'blog/category'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('blog/category'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Category ID')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true
    ), 'Category Title')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Parent Category ID')
    ->addColumn('identifier', Varien_Db_Ddl_Table::TYPE_TEXT, 200, array(
        'nullable'  => true,
        'default'   => null,
    ), 'Category String Identifier')
    ->addIndex($installer->getIdxName('blog/category', array('identifier')),
    array('identifier'))
    ->setComment('Blog Category Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'blog/category_store'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('blog/category_store'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'primary'   => true,
    ), 'Category ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Store ID')
    ->addIndex($installer->getIdxName('blog/category_store', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('blog/category_store', 'category_id', 'blog/category', 'category_id'),
        'category_id', $installer->getTable('blog/category'), 'category_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('blog/category_store', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Blog Category To Store Linkage Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'blog/category_post'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('blog/category_post'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ), 'Category ID')
    ->addColumn('post_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ), 'Post ID')
    ->addIndex($installer->getIdxName('blog/category_post', array('post_id')),
        array('post_id'))
    ->addForeignKey($installer->getFkName('blog/category_post', 'category_id', 'blog/category', 'category_id'),
        'category_id', $installer->getTable('blog/category'), 'category_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('blog/category_post', 'post_id', 'blog/post', 'post_id'),
        'post_id', $installer->getTable('blog/post'), 'post_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Blog Category To Post Linkage Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'blog/tag'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('blog/tag'))
    ->addColumn('tag_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Tag ID')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true
    ), 'Tag Title')
    ->addColumn('identifier', Varien_Db_Ddl_Table::TYPE_TEXT, 200, array(
        'nullable'  => true,
        'default'   => null,
    ), 'Tag String Identifier')
    ->addIndex($installer->getIdxName('blog/tag', array('identifier')),
        array('identifier'))
    ->setComment('Blog Tag Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'blog/tag_store'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('blog/tag_store'))
    ->addColumn('tag_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'primary'   => true,
    ), 'Tag ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Store ID')
    ->addIndex($installer->getIdxName('blog/tag_store', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('blog/tag_store', 'tag_id', 'blog/tag', 'tag_id'),
        'tag_id', $installer->getTable('blog/tag'), 'tag_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('blog/tag_store', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Blog Tag To Store Linkage Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'blog/tag_post'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('blog/tag_post'))
    ->addColumn('tag_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ), 'Tag ID')
    ->addColumn('post_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ), 'Post ID')
    ->addIndex($installer->getIdxName('blog/tag_post', array('post_id')),
        array('post_id'))
    ->addForeignKey($installer->getFkName('blog/tag_post', 'tag_id', 'blog/tag', 'tag_id'),
        'tag_id', $installer->getTable('blog/tag'), 'tag_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('blog/tag_post', 'post_id', 'blog/post', 'post_id'),
        'post_id', $installer->getTable('blog/post'), 'post_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Blog Tag To Post Linkage Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'blog/post_comment'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('blog/post_comment'))
    ->addColumn('comment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Comment ID')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Parent Comment ID')
    ->addColumn('post_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Post ID')
    ->addColumn('creation_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Post Creation Time')
    ->addColumn('author_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => true,
    ), 'Comment Customer Author ID')
    ->addColumn('author_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true
    ), 'Comment Author Name')
    ->addColumn('author_email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true
    ), 'Comment Author Email')
    ->addColumn('author_url', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true
    ), 'Comment Author Url')
    ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
    ), 'Comment Content')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
    ), 'Comment Status')
    ->addIndex($installer->getIdxName('blog/post_comment', array('post_id')),
        array('post_id'))
    ->addForeignKey($installer->getFkName('blog/post_comment', 'post_id', 'blog/post', 'post_id'),
        'post_id', $installer->getTable('blog/post'), 'post_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Blog Post Comment Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
