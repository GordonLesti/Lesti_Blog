<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 28.03.13
 * Time: 13:48
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Block_Post_view extends Mage_Core_Block_Template
{

    protected function _construct()
    {
        $this->addData(array(
            'cache_lifetime'    => 3600
        ));
        return parent::_construct();
    }

    protected $_post;

    public function getPost()
    {
        if(is_null($this->_post))
        {
            $this->_post = $this->_getPost();
        }
        return $this->_post;
    }

    protected function _getPost()
    {
        return Mage::registry('blog_post');
    }

    /**
     * Get cache key informative items
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKeyInfo = parent::getCacheKeyInfo();
        $cacheKeyInfo[] = 'blog_post_view';
        $cacheKeyInfo[] = $this->getPost()->getId();
        $cacheKeyInfo[] = Mage::getSingleton('customer/session')->isLoggedIn();
        return $cacheKeyInfo;
    }

}