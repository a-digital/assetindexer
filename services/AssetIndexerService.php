<?php
/**
 * Asset Indexer plugin for Craft CMS
 *
 * AssetIndexer Service
 *
 * --snip--
 * All of your plugin’s business logic should go in services, including saving data, retrieving data, etc. They
 * provide APIs that your controllers, template variables, and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 * --snip--
 *
 * @author    Matt Shearing
 * @copyright Copyright (c) 2016 Matt Shearing
 * @link      http://adigital.agency
 * @package   AssetIndexer
 * @since     1.0.0
 */

namespace Craft;

class AssetIndexerService extends BaseApplicationComponent
{
    /**
     * This function can literally be anything you want, and you can have as many service functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     craft()->assetIndexer->exampleService()
     */
    public function getAssetSources()
    {
	    $query = craft()->db->createCommand();
		$entryRecord = $query->select('name, handle')->from('assetsources')->where('type="S3"')->queryAll();
		return $entryRecord;
    }
    
    public function getAssets($folder, $start)
    {
		$sourceRecord = craft()->db->createCommand()->select('id, settings')->from('assetsources')->where('handle="'.$folder.'"')->queryRow();
		$settings = json_decode($sourceRecord["settings"]);
		
		$credentials = new \Aws\Credentials\Credentials($settings->keyId, $settings->secret);
		$options = [
			'version'     => 'latest',
			'region'      => $settings->location,
			'credentials' => $credentials
		];
		
		$s3Client = new \Aws\S3\S3Client($options);
		$s3Client->registerStreamWrapper();
		
		$dir = "s3://".$settings->bucket."/";
		if (isset($settings->subfolder) && $settings->subfolder <> "") {
			$dir .= $settings->subfolder."/";
		}
		
		$totalfiles = array_diff(scandir($dir), array(".", "..", "_thumbs", "_optimized"));
		$num_files = count($totalfiles);
		
		$count = 0;
		$end = $start + 100;
		
		$sessionId = "2dd57e0b-992a-4251-a19e-7d0fcbd600e7";
		$sourceId = $sourceRecord["id"];
		$cpTrigger = craft()->config->get('cpTrigger');
		
		$files = array();
		if (is_dir($dir) && ($dh = opendir($dir))) {
			while (($file = readdir($dh)) !== false) {
				if (filetype($dir . $file) == "file") {
					if ($count >= $start) {
						if ($count < $end) {
							craft()->db->createCommand()->insert('assetindexdata', array(
								"sessionId" => $sessionId,
								"sourceId" => $sourceId,
								"offset" => $count,
								"uri" => $file,
								"size" => filesize($dir . $file)
							));
							
							$files[$count] = array(
								"filename" => $file,
								"filetype" => filetype($dir . $file),
								"filesize" => filesize($dir . $file)
							);
						} else {
							closedir($dh);
							$data = array(
								"files" => $files,
								"count" => $count,
								"total" => $num_files,
								"cpTrigger" => $cpTrigger
							);
							return $data;
						}
					}
					$count++;
				}
			}
			closedir($dh);
		}
		$data = array(
			"files" => $files,
			"count" => $count,
			"total" => $num_files,
			"cpTrigger" => $cpTrigger
		);
		return $data;
    }
    
    public function getFiles($folder, $start)
    {
	    $sourceRecord = craft()->db->createCommand()->select('id, settings')->from('assetsources')->where('handle="'.$folder.'"')->queryRow();
	    $settings = json_decode($sourceRecord["settings"]);
		
		$credentials = new \Aws\Credentials\Credentials($settings->keyId, $settings->secret);
		$options = [
			'version'     => 'latest',
			'region'      => $settings->location,
			'credentials' => $credentials
		];
		
		$s3Client = new \Aws\S3\S3Client($options);
		$s3Client->registerStreamWrapper();
		
		$dir = "s3://".$settings->bucket."/";
		if (isset($settings->subfolder) && $settings->subfolder <> "") {
			$dir .= $settings->subfolder."/";
		}
		
		$totalfiles = array_diff(scandir($dir), array(".", "..", "_thumbs", "_optimized"));
		$num_files = count($totalfiles);
		
		$end = $start + 1;
		
		$sessionId = "2dd57e0b-992a-4251-a19e-7d0fcbd600e7";
		$sourceId = $sourceRecord["id"];
		
		craft()->assetIndexing->processIndexForSource($sessionId, $start, $sourceId);
		craft()->db->createCommand()->delete('assetindexdata', array('AND', 'sessionId=:sessionId', 'offset=:offset'), array(':offset'=>$start, ':sessionId'=>$sessionId));
		
		$data = array(
			"count" => $end,
			"total" => $num_files,
			"cpTrigger" => craft()->config->get('cpTrigger')
		);
		return $data;
    }

}