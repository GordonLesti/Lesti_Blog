<?php

class Lesti_Blog_Block_Adminhtml_Post_Edit_Tab_Tag
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        /* @var $model Lesti_Blog_Model_post */
        $model = Mage::registry('blog_post');

        /*
         * Checking if user have permissions to save tags
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }


        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('post_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('blog')->__('Tags')));

        $field = $fieldset->addField('tag_id', 'multiselect', array(
            'name'      => 'tags[]',
            'label'     => Mage::helper('blog')->__('Tags'),
            'title'     => Mage::helper('blog')->__('Tags'),
            'values'    => Mage::getModel('blog/tag')->getCollection()->toOptionArray(),
            'disabled'  => $isElementDisabled,
        ));

        Mage::dispatchEvent('adminhtml_blog_post_edit_tab_tag_prepare_form', array('form' => $form));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('blog')->__('Tags');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('blog')->__('Tags');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('blog/post/' . $action);
    }
}
