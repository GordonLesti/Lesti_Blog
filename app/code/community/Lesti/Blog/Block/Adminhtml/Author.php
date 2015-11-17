<?php

class Lesti_Blog_Block_Adminhtml_Author extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize blog author
     *
     * @return void
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'blog';
        $this->_mode = 'author';

        parent::__construct();

        $this->setData('form_action_url', $this->getUrl('*/blog_author/save'));

        if ($this->_isAllowedAction('save')) {
            $this->_updateButton('save', 'label', Mage::helper('blog')->__('Save Data'));
            $this->_addButton('saveandcontinue', array(
                'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit(\''.$this->_getSaveAndContinueUrl().'\')',
                'class'     => 'save',
            ), -100);
        } else {
            $this->_removeButton('save');
        }
        $this->_removeButton('delete');
    }

    /**
     * Retrieve text for header element depending on loaded author
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('blog_author')->getId()) {
            return Mage::helper('blog')->__("Edit Author '%s'", $this->htmlEscape(Mage::registry('blog_author')->getAuthorName()));
        }
        else {
            return Mage::helper('blog')->__('Edit Author');
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
        return Mage::getSingleton('admin/session')->isAllowed('blog/author/' . $action);
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
            'back'       => 'index',
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
        $tabsBlock = $this->getLayout()->getBlock('blog_author_tabs');
        if ($tabsBlock) {
            $tabsBlockJsObject = $tabsBlock->getJsObjectName();
            $tabsBlockPrefix   = $tabsBlock->getId() . '_';
        } else {
            $tabsBlockJsObject = 'author_tabsJsTabs';
            $tabsBlockPrefix   = 'author_tabs_';
        }

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('author_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'author_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'author_content');
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
