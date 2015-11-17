<?php

class Lesti_Blog_Block_Adminhtml_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    /**
     * Initialize blog category edit block
     *
     * @return void
     */
    public function __construct()
    {
        $this->_objectId   = 'category_id';
        $this->_controller = 'adminhtml_category';
        $this->_blockGroup = 'blog';

        parent::__construct();

        $this->setData('form_action_url', $this->getUrl('*/blog_category/save'));

        if ($this->_isAllowedAction('save')) {
            $this->_updateButton('save', 'label', Mage::helper('blog')->__('Save Category'));
            $this->_addButton('saveandcontinue', array(
                'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit(\''.$this->_getSaveAndContinueUrl().'\')',
                'class'     => 'save',
            ), -100);
        } else {
            $this->_removeButton('save');
        }

        if ($this->_isAllowedAction('delete')) {
            $this->_updateButton('delete', 'label', Mage::helper('blog')->__('Delete Category'));
        } else {
            $this->_removeButton('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded category
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('blog_category')->getId()) {
            return Mage::helper('blog')->__("Edit Category '%s'", $this->htmlEscape(Mage::registry('blog_category')->getTitle()));
        }
        else {
            return Mage::helper('blog')->__('New Category');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('blog/category/' . $action);
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
