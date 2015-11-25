<?php

class Lesti_Blog_Model_Archive extends Varien_Object
{
    protected $_posts;
    protected $_isLoaded;
    
    /**
     * @return bool
     */
    public function exists()
    {
        if ($this->getPosts()) {
            return (bool) $this->getPosts()->count();
        }
        return false;
    }
    
    /**
     * @return Lesti_Blog_Model_Resource_Post_Collection
     */
    public function getPosts()
    {
        if (! $this->_posts) {
            if ($this->getYear() && ! $this->_isLoaded) {
                $this->_load($this->getYear(), $this->getMonth());
            }
        }
        return $this->_posts;
    }
    
    /**
     * @param $format for strftime()
     * @return string
     */
    public function getTitle($format = null) {
        if (! $this->getYear()) {
            return '';
        }
        if (! $this->getMonth()) {
            return $this->getYear();
        }
        
        $dateStr = "1-{$this->getMonth()}-{$this->getYear()}";
        if (! is_string($format)) {
            $format = "%B %Y";
        }
        return strftime($format, strtotime($dateStr));
    }
    
    /**
     * @param int $year
     * @param int|null $month
     * @return Lesti_Blog_Model_Archive
     */
    protected function _load($year, $month = null)
    {
        if (! $this->_isLoaded) {
            $date = new Zend_Date();
            $date->setDay(1);
            $date->setTime(0);
            $date->setYear($year);
            if ($month) {
                $date->setMonth($month);
            } else {
                $date->setMonth(1);
            }
            $this->_posts = Mage::getModel('blog/post')->getCollection();
            $this->_posts->addFieldToFilter('creation_time',
                array('gteq' => $date->toString(Lesti_Blog_Helper_Data::MYSQL_DATE_FORMAT)));
            
            if (! $month) {
                $date->setMonth(12);
            } else {
                $date->addMonth(1);
            }
            $this->_posts->addFieldToFilter('creation_time',
                array('lt' => $date->toString(Lesti_Blog_Helper_Data::MYSQL_DATE_FORMAT)));
            
            $this->_isLoaded = true;
        }
        return $this;
    }
}
