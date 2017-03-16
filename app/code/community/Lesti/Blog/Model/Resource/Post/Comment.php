<?php

class Lesti_Blog_Model_Resource_Post_Comment extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('blog/post_comment', 'comment_id');
    }

    /**
     * Process comment data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Lesti_Blog_Model_Resource_Post_Comment
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {

        // modify create / update dates
        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }

        $content = $object->getContent();
        $content = Mage::helper('blog/post_comment')->purifyHtml($content);
        $object->setContent($content);

        return parent::_beforeSave($object);
    }

    protected function _refreshPostCommentCount(Mage_Core_Model_Abstract $object)
    {
        $post = Mage::getModel('blog/post')->load($object->getPostId());
        if($post->getId()) {
            $commentCollection = Mage::getModel('blog/post_comment')->getCollection()
                ->addFieldToFilter('status', Lesti_Blog_Model_Post_Comment::STATUS_ENABLED)
                ->addFieldToFilter('post_id', $post->getId());
            $post->setCommentCount($commentCollection->count())->save();
        }
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $this->_refreshPostCommentCount($object);
        return parent::_afterSave($object);
    }

    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        $condition = array(
            'parent_id = ?'     => (int) $object->getId(),
        );
        $this->_getWriteAdapter()->delete($this->getTable('blog/post_comment'), $condition);

        $this->_refreshPostCommentCount($object);
        return parent::_afterDelete($object);
    }

}
