/**
 * Asset Indexer plugin for Craft CMS
 *
 * Asset Indexer JS
 *
 * @author    Matt Shearing
 * @copyright Copyright (c) 2016 Matt Shearing
 * @link      http://adigital.agency
 * @package   CraftGiving
 * @since     1.0.0
 */
 
$(document).ready(function(){
	if ($("#source").length) {
		var cpTrigger = $("#cpTigger").text();
		if ($("#stepcount").length) {
			$(".body").load("/"+cpTrigger+"/assetindexer/step/"+$("#source").text()+"/"+$("#stepcount").text());
		} else {
			$(".body").load("/"+cpTrigger+"/assetindexer/step/"+$("#source").text());
		}
	}
});