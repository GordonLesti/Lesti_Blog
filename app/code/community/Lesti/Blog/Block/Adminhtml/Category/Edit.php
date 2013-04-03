<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 03.04.13
 * Time: 15:25
 * To change this template use File | Settings | File Templates.
 */
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

    /**
     * Prepare layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $tabsBlock = $this->getLayout()->getBlock('blog_category_edit_tabs');
        if ($tabsBlock) {
            $tabsBlockJsObject = $tabsBlock->getJsObjectName();
            $tabsBlockPrefix   = $tabsBlock->getId() . '_';
        } else {
            $tabsBlockJsObject = 'category_tabsJsTabs';
            $tabsBlockPrefix   = 'category_tabs_';
        }

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('category_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'category_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'category_content');
                }
            }

            function saveAndContinueEdit(urlTemplate) {
                var tabsIdValue = " . $tabsBlockJsObject . ".activeTab.id;
                var tabsBlockPrefix = '" . $tabsBlockPrefix . "';
                if (tabsIdValue.startsWith(tabsBlockPrefix)) {
                    tabsIdValue = tabsIdValue.substr(tabsBlockPrefix.length)
                }
                var template = new Template(urlTemplate, /(^|.|\\r|\\n)({{(\w+)}})/);
                var url = template.evaluate({tab_id:tabsIdValue});
                editForm.submit(url);
            }
        ";
        return parent::_prepareLayout();
    }

}