<?php
/**
 * Asset Indexer plugin for Craft CMS
 *
 * Asset Indexer Variable
 *
 * --snip--
 * Craft allows plugins to provide their own template variables, accessible from the {{ craft }} global variable
 * (e.g. {{ craft.pluginName }}).
 *
 * https://craftcms.com/docs/plugins/variables
 * --snip--
 *
 * @author    Matt Shearing
 * @copyright Copyright (c) 2016 Matt Shearing
 * @link      http://adigital.agency
 * @package   CraftGiving
 * @since     1.0.0
 */

namespace Craft;

class AssetIndexerVariable
{
    /**
     * Whatever you want to output to a Twig tempate can go into a Variable method. You can have as many variable
     * functions as you want.  From any Twig template, call it like this:
     *
     *     {{ craft.assetIndexer.exampleVariable }}
     *
     * Or, if your variable requires input from Twig:
     *
     *     {{ craft.assetIndexer.exampleVariable(twigValue) }}
     */
    public function AssetSources()
    {
	    $response = craft()->assetIndexer->getAssetSources();
		return $response;
    }
    
    public function IndexAssets($folder = null, $start = null)
    {
		$response = craft()->assetIndexer->getAssets($folder, $start);
		return $response;
    }
    
    public function IndexFiles($folder = null, $start = null)
    {
		$response = craft()->assetIndexer->getFiles($folder, $start);
		return $response;
    }
}