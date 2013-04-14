<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 14.04.13
 * Time: 18:38
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Block_Adminhtml_Post_Comment_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        /* @var $model Lesti_Blog_Model_post */
        $model = Mage::registry('blog_post_comment');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }


        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('post_comment_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('blog')->__('Comment Information')));

        $fieldset->addField('comment_id', 'hidden', array(
            'name' => 'comment_id',
        ));

        $fieldset->addField('author_name', 'text', array(
            'name'      => 'author_name',
            'label'     => Mage::helper('blog')->__('Author Name'),
            'title'     => Mage::helper('blog')->__('Author Name'),
            'required'  => true,
            'disabled'  => true
        ));

        $fieldset->addField('author_email', 'text', array(
            'name'      => 'author_email',
            'label'     => Mage::helper('blog')->__('Author Email'),
            'title'     => Mage::helper('blog')->__('Author Email'),
            'required'  => true,
            'disabled'  => true
        ));

        $fieldset->addField('author_url', 'text', array(
            'name'      => 'author_name',
            'label'     => Mage::helper('blog')->__('Author Website'),
            'title'     => Mage::helper('blog')->__('Author Website'),
            'required'  => false,
            'disabled'  => true
        ));

        $fieldset->addField('content', 'textarea', array(
            'name'      => 'content',
            'label'     => Mage::helper('blog')->__('Comment'),
            'title'     => Mage::helper('blog')->__('Comment'),
            'required'  => true,
            'disabled'  => true
        ));

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('blog')->__('Status'),
            'title'     => Mage::helper('blog')->__('Comment Status'),
            'name'      => 'status',
            'required'  => true,
            'options'   => $model->getAvailableStatuses(),
            'disabled'  => $isElementDisabled,
        ));

        Mage::dispatchEvent('adminhtml_blog_post_comment_edit_tab_main_prepare_form', array('form' => $form));

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
        return Mage::helper('blog')->__('Comment Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('blog')->__('Comment Information');
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
        return Mage::getSingleton('admin/session')->isAllowed('blog/post_comment/' . $action);
    }
}