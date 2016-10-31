# Asset Indexer plugin for Craft CMS

Index a very large assets folder in batches when Craft fails to do so

![Screenshot](resources/screenshots/plugin_logo.png)

## Installation

To install Asset Indexer, follow these steps:

1. Download & unzip the file and place the `assetindexer` directory into your `craft/plugins` directory
2.  -OR- do a `git clone ???` directly into your `craft/plugins` folder.  You can then update it with `git pull`
3.  -OR- install with Composer via `composer require /assetindexer`
4. Install plugin in the Craft Control Panel under Settings > Plugins
5. The plugin folder should be named `assetindexer` for Craft to see it.  GitHub recently started appending `-master` (the branch name) to the name of the folder for zip file downloads.

Asset Indexer works on Craft 2.4.x and Craft 2.5.x.

## Asset Indexer Overview

This plugin was created after we hit issues indexing assets from S3 on a fresh install of Craft where we wanted to pull all of the images into a new site build. To get around it we submitted a ticket to Pixel & Tonic and the friendly guys helped to guide us towards creating this plugin. Thank you to Andris in particular at Pixel & Tonic. We have released this for anyone else having issues like we did as we want to help the Craft community.

## Configuring Asset Indexer

There is no configuration necessary if your S3 asset sources are set up correctly as this plugin takes all of the settings it needs from there.

## Using Asset Indexer

Click into Asset Indexer in the sidebar, and then click on your asset source from the list, and off it goes. Currently you will need to keep this window open until the process is completed. This may take some time however if it is a very large directory.

## Asset Indexer Roadmap

Some things to do, and ideas for potential features:

* Convert indexing to a task so you can leave it running in the background or target with a cron
* Index subfolders of the directory

## Asset Indexer Changelog

### 1.0.0 -- 2016.10.21

* Initial release

Brought to you by [Matt Shearing](http://adigital.agency) from A Digital

Icon made by [Freepik](http://www.freepik.com) from [www.flaticon.com](http://www.flaticon.com) is licensed by [CC 3.0 BY](http://creativecommons.org/licenses/by/3.0/)