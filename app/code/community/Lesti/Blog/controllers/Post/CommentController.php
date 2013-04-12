<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 10.04.13
 * Time: 16:36
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Post_CommentController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        // check if is allowed for user to comment;
    }

    public function postAction()
    {
        // save comment
    }
}