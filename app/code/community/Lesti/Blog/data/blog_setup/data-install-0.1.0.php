<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 27.03.13
 * Time: 14:10
 * To change this template use File | Settings | File Templates.
 */
$defaultAuthor = Mage::getModel('admin/user')->getCollection()->getFirstItem();

$data = array(
    'author_id'         => $defaultAuthor->getId(),
    'creation_time'     => Mage::getSingleton('core/date')->gmtDate(),
    'update_time'       => Mage::getSingleton('core/date')->gmtDate(),
    'is_active'         => Lesti_Blog_Model_Post::STATUS_ENABLED,
    'content'           => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.<!--more--> At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
    'title'             => 'Example Post',
    'identifier'        => 'example-post',
    'stores'            => array(Mage::app()->getStore()->getId()),
);

Mage::getModel('blog/post')->setData($data)->save();