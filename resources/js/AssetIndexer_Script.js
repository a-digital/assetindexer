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

function isError(status) {
  "use strict";
  return (status == 'error');
}

function appendFatal(response) {
  "use strict";
  $("#main").append('<div class="padded"><div class="pane">'+response+'</div></div>');
}

function standardResponseCallback(response, status, xhr) {
  "use strict";
  if (isError(status)) {
    appendFatal(response);
  }
}