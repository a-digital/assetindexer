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
    private $sourceId;
    private $sessionId = "2dd57e0b-992a-4251-a19e-7d0fcbd600e7";
    private $cnt_files;
    private $all_files;
    private $dir;
    private $s3Client;
    private $credentials;
    private $settings;
    private $sourceRecord;

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
        $this->prep($folder);
		
		$count = 0;
		$end = $start + 100;

		$cpTrigger = craft()->config->get('cpTrigger');
		
		$files = array();
		if (is_dir($this->dir) && ($dh = opendir($this->dir))) {
			while (($file = readdir($dh)) !== false) {
				if (filetype($this->dir . $file) == "file") {
					if ($count >= $start) {
						if ($count < $end) {
							craft()->db->createCommand()->insert('assetindexdata', array(
								"sessionId" => $this->sessionId,
								"sourceId" => $this->sourceId,
								"offset" => $count,
								"uri" => $file,
								"size" => filesize($this->dir . $file)
							));
							
							$files[$count] = array(
								"filename" => $file,
								"filetype" => filetype($this->dir . $file),
								"filesize" => filesize($this->dir . $file)
							);
						} else {
							closedir($dh);
							$data = array(
								"files" => $files,
								"count" => $count,
								"total" => $this->cnt_files,
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
			"total" => $this->cnt_files,
			"cpTrigger" => $cpTrigger
		);
		return $data;
    }
    
    public function getFiles($folder, $start)
    {
        $this->prep($folder);
		
		$end = $start + 1;

		$sourceId = $this->sourceRecord["id"];
		
		craft()->assetIndexing->processIndexForSource($this->sessionId, $start, $sourceId);
		craft()->db->createCommand()->delete('assetindexdata', array('AND', 'sessionId=:sessionId', 'offset=:offset'), array(':offset'=>$start, ':sessionId'=>$this->sessionId));
		
		$data = array(
			"count" => $end,
			"total" => $this->cnt_files,
			"cpTrigger" => craft()->config->get('cpTrigger')
		);
		return $data;
    }

    private function prep($folder)
    {
        $this->sourceRecord = craft()->db->createCommand()->select('id, settings')->from('assetsources')->where('handle="' . $folder . '"')->queryRow();
        $this->settings = json_decode($this->sourceRecord["settings"]);

        $this->credentials = new \Aws\Credentials\Credentials($this->settings->keyId, $this->settings->secret);
        $options = [
            'version'     => 'latest',
            'region'      => $this->processLocation($this->settings->location),
            'credentials' => $this->credentials
        ];

        $this->s3Client = new \Aws\S3\S3Client($options);
        $this->s3Client->registerStreamWrapper();

        $this->dir = "s3://" . $this->settings->bucket . "/";
        if (isset($this->settings->subfolder) && $this->settings->subfolder <> "") {
            $this->dir .= $this->settings->subfolder."/";
        }

        $this->all_files = array_diff(scandir($this->dir), array(".", "..", "_thumbs", "_optimized"));
        $this->cnt_files = count($this->all_files);

        $this->sourceId = $this->sourceRecord["id"];
    }

    private function processLocation($location)
    {
        // Craft uses short hand for their predefined locations... AWS barfs on it.
        // The list can be found in \Craft\S3AssetSourceType::$_predefinedEndpoints
        $loc = strtolower(trim($location));

        if ($loc == 'us') return 'us-east-1';
        if ($loc == 'eu') return 'eu-west-1';

        return $location;
    }

}