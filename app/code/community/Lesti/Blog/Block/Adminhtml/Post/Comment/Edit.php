<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 14.04.13
 * Time: 18:31
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Block_Adminhtml_Post_Comment_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize blog comment edit block
     *
     * @return void
     */
    public function __construct()
    {
        $this->_objectId   = 'comment_id';
        $this->_controller = 'adminhtml_post_comment';
        $this->_blockGroup = 'blog';

        parent::__construct();

        $this->setData('form_action_url', $this->getUrl('*/blog_post_comment/save'));

        if ($this->_isAllowedAction('save')) {
            $this->_updateButton('save', 'label', Mage::helper('blog')->__('Save Comment'));
            $this->_addButton('saveandcontinue', array(
                'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit(\''.$this->_getSaveAndContinueUrl().'\')',
                'class'     => 'save',
            ), -100);
        } else {
            $this->_removeButton('save');
        }

        if ($this->_isAllowedAction('delete')) {
            $this->_updateButton('delete', 'label', Mage::helper('blog')->__('Delete Comment'));
        } else {
            $this->_removeButton('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded comment
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('blog')->__('Comment');
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('blog/post_comment/' . $action);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit',
            'active_tab' => '{{tab_id}}'
        ));
    }
}