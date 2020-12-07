/**
 * ET Web Solutions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @category   ET
 * @package    ET_Reviewnotify
 * @copyright  Copyright (c) 2011 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
Event.observe(window, 'load', function()
{
	if($("review-form")!= null){
		$("review-form").insert('<input class="validate-code" name="sequence" id="review_sequence" value="" style="width:0px;height:0px;overflow:hidden;border:0;margin:0;padding:0;" /><iframe id="sequenceframe" name="review_sequence_place" id="review_sequence_place_id" style="width:0px;height:0px;overflow:hidden;border:0;margin:0;padding:0;"></iframe>',{"position":"top"});
		var url=$("review-form").readAttribute("action");
		var myString = new String(url);//review/product/post
		var rExp = /\/review\/product\/post\//gi;
		var tmpHref = myString.replace(rExp, "/etreviewnotify/product/prepost/");
		$("review-form").writeAttribute("target","review_sequence_place");
		$("review-form").writeAttribute("oldurl",url);
		$("review-form").writeAttribute("action",tmpHref);
	}
});

function postReviewRestoreData(sequence)
{
	$("review_sequence").value=sequence;
	$("review-form").writeAttribute("target","_self");
	$("review-form").writeAttribute("action",$("review-form").readAttribute("oldurl"));
	$("review-form").submit();
}