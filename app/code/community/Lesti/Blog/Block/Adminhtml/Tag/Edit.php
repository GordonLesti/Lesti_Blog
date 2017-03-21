<?php

class Lesti_Blog_Block_Adminhtml_Tag_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    /**
     * Initialize blog tag edit block
     *
     * @return void
     */
    public function __construct()
    {
        $this->_objectId   = 'tag_id';
        $this->_controller = 'adminhtml_tag';
        $this->_blockGroup = 'blog';

        parent::__construct();

        $this->setData('form_action_url', $this->getUrl('*/blog_tag/save'));

        if ($this->_isAllowedAction('save')) {
            $this->_updateButton('save', 'label', Mage::helper('blog')->__('Save Tag'));
            $this->_addButton('saveandcontinue', array(
                'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit(\''.$this->_getSaveAndContinueUrl().'\')',
                'class'     => 'save',
            ), -100);
        } else {
            $this->_removeButton('save');
        }

        if ($this->_isAllowedAction('delete')) {
            $this->_updateButton('delete', 'label', Mage::helper('blog')->__('Delete Tag'));
        } else {
            $this->_removeButton('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded tag
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('blog_tag')->getId()) {
            return Mage::helper('blog')->__("Edit Tag '%s'", $this->htmlEscape(Mage::registry('blog_tag')->getTitle()));
        }
        else {
            return Mage::helper('blog')->__('New Tag');
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
        return Mage::getSingleton('admin/session')->isAllowed('blog/tag/' . $action);
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
     * Allows to use the saveAndContinueEdit Button
     * @return mixed
     */
    protected function _prepareLayout()
    {
        $tabsBlock = $this->getLayout()->getBlock('blog_tag_edit_tabs');
        if ($tabsBlock) {
            $tabsBlockJsObject = $tabsBlock->getJsObjectName();
            $tabsBlockPrefix   = $tabsBlock->getId() . '_';
        } else {
            $tabsBlockJsObject = 'tag_tabsJsTabs';
            $tabsBlockPrefix   = 'tag_tabs_';
        }

        $this->_formScripts[] = "
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
