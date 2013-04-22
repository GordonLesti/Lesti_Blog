/**
 * Created with JetBrains PhpStorm.
 * User: gordon
 * Date: 09.04.13
 * Time: 14:42
 * To change this template use File | Settings | File Templates.
 */

function moveReplyForm(comment)
{
    // move reply-form
    $('comment-' + comment).insert({'bottom': $('post-response')});
    // add "cancel reply"
    if($('parent_id').getValue() == "0") {
        $('comment-form').insert({'top': '<a id="cancel-reply" onclick="cancelReply();">' + Translator.translate('Cancel Reply') + '</a>' });
    }
    // set parent_id
    $('parent_id').setValue(comment);
}

function cancelReply()
{
    // move reply-form
    $('post-comments').insert({'bottom': $('post-response')});
    // remove "cancel reply"
    $('cancel-reply').remove();
    // set parent_id
    $('parent_id').setValue("0");
}