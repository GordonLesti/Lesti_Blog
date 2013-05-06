<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 24.04.13
 * Time: 13:10
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Model_Archive extends Varien_Object
{
    protected $_postCollection;

    public function exists($year, $month = null)
    {
        $year = (int) $year;
        $month = (int) $month;
        $date = new Zend_Date();
        $date->setDay(1);
        $date->setTime(0);
        $date->setYear($year);
        if($month) {
            $date->setMonth($month);
        } else {
            $date->setMonth(1);
        }
        $postCollection = Mage::getModel('blog/post')->getCollection()
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addFieldToFilter('creation_time',
                array('gteq' => $date->toString(Lesti_Blog_Helper_Data::MYSQL_DATE_FORMAT)));
        if(!$month) {
            $date->setMonth(12);
        }
        $date->addMonth(1);
        $postCollection->addFieldToFilter('creation_time',
                array('lt' => $date->toString(Lesti_Blog_Helper_Data::MYSQL_DATE_FORMAT)));
        $postCollection->getSelect()->reset(Zend_Db_Select::COLUMNS)
            ->columns('main_table.post_id')
            ->limit(1);
        return $postCollection->count();
    }

    public function getPostCollection()
    {
        return $this->_postCollection;
    }

    public function load($date) {
        $date = explode('-', $date);
        $year = (int) $date[0];
        $month = (int) $date[1];
        $date = new Zend_Date();
        $date->setDay(1);
        $date->setTime(0);
        $date->setYear($year);
        if($month) {
            $date->setMonth($month);
        } else {
            $date->setMonth(1);
        }
        $postCollection = Mage::getModel('blog/post')->getCollection()
            ->addFieldToFilter('creation_time',
                array('gteq' => $date->toString(Lesti_Blog_Helper_Data::MYSQL_DATE_FORMAT)));
        if(!$month) {
            $date->setMonth(12);
        }
        $date->addMonth(1);
        $postCollection->addFieldToFilter('creation_time',
            array('lt' => $date->toString(Lesti_Blog_Helper_Data::MYSQL_DATE_FORMAT)));
        $this->_postCollection = $postCollection;
        return $this;
    }
}