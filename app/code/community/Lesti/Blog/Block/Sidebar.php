<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 30.03.13
 * Time: 16:26
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Block_Sidebar extends Mage_Core_Block_Template
{
    const XML_PATH_LAST_POSTS_COUNT = 'blog/general/last_posts_count';

    public function getLastPosts()
    {
        $count = (int) Mage::getStoreConfig(self::XML_PATH_LAST_POSTS_COUNT);
        return Mage::getModel('blog/post')->getCollection()
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->setOrder('creation_time')
            ->setPageSize($count);
    }

    public function getCategories()
    {
        return Mage::getModel('blog/category')->getCollection()
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->setOrder('identifier');
    }
}