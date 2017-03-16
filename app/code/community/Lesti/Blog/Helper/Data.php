<?php

class Lesti_Blog_Helper_Data extends Mage_Core_Helper_Abstract
{

    const MYSQL_DATE_FORMAT = 'yyyy-MM-dd HH:mm:ss';
    /**
     * Retrieve blog url
     *
     * @return string
     */
    public function getBlogUrl()
    {
        return $this->_getUrl(Mage::getStoreConfig(Lesti_Blog_Model_Post::XML_PATH_BLOG_GENERAL_ROUTER));
    }

    public function formatDate($date, $format)
    {
        $date = new Zend_Date($date, self::MYSQL_DATE_FORMAT);
        return $date->toString($format);
    }
}
