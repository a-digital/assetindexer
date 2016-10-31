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
		if ($("#filecount").length) {
			$(".body").load("/cms/assetindexer/file/"+$("#source").text()+"/"+$("#filecount").text());
		} else {
			$(".body").load("/cms/assetindexer/file/"+$("#source").text());
		}
	}
});