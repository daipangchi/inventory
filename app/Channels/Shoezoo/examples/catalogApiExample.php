<?php

namespace App\Api\Shoezoo;

$dropShipping = new CatalogApi();

/*
 * Submit request for full catalog feed.
 * To get a full catalog you should use function getCatalogList() without parameters.
 * This function starts generation of the catalog.
 * $response = $dropShipping->getCatalogList();
 *
 *
 * Use getCatalogList(true) to check periodically the status of the feed.
 * This function return associative array with status and file link (csv)
 * $response = $dropShipping->getCatalogList(true);
 *
 *
 */

/*
 * Next example illustrate how you will get full catalog
 */

// First step - Submit request for full catalog feed.
$response = $dropShipping->getCatalogList();

// Display success result or error message
if (isset($response->success) && $response->success) {
    echo "<pre>".print_r($response, 1)."</pre>";
}
else {
    echo "<pre>".print_r($response->getMessage(), 1)."</pre>";
}

// Second step -  to check periodically the status of the feed. If file is still in process of generation then
// function return warning. If generation is finished then function returns array with link to file.
$response = $dropShipping->getCatalogList(true);
if (isset($response->success) && $response->success) {
    echo "<pre>".print_r($response, 1)."</pre>";
}
else {
    echo "<pre>".print_r($response->getMessage(), 1)."</pre>";
}


/*
 * Next example illustrate how to get incremental changes of catalog after 14 Apr 2014 20:16:30 GMT
 */

// First step - Submit request for full catalog feed with timestamp
$response = $dropShipping->getCatalogList(false, 1397506590);

// Display success result or error message
if (isset($response->success) && $response->success) {
    echo "<pre>".print_r($response, 1)."</pre>";
}
else {
    echo "<pre>".print_r($response->getMessage(), 1)."</pre>";
}

// Second step -  to check periodically the status of the feed. If file is still in process of generation then
// function return warning. If generation is finished then function returns array with link to file.
$response = $dropShipping->getCatalogList(true);
if (isset($response->success) && $response->success) {
    echo "<pre>".print_r($response, 1)."</pre>";
}
else {
    echo "<pre>".print_r($response->getMessage(), 1)."</pre>";
}


//$response = $dropShipping->getCatalogList(false, 1432547755);
//$response = $dropShipping->getCatalogList(true);
//echo "<pre>" . print_r($response, 1) . "</pre>";


/*=================================================== Normal Flow ===================================================*/
/*
 * Next example illustrates how retrieve full Catalog on the first step and retrieve updated Catalog on the next steps
 */

/*
    // First step - get current timestamp and submit request for feed
    $currentTimestamp = $dropShipping->getStoreCurrentTimestamp();
    $response = $dropShipping->getCatalogList();

    // Display success result or error message
    if (isset($response->success) && $response->success) {
        echo "<pre>" . print_r($response, 1) . "</pre>";
        // also you must save $currentTimestamp in your system
    } else {
        echo "<pre>" . print_r($response->getMessage(), 1) . "</pre>";
    }

    // Second step -  to check periodically the status of the feed. If file is still in process of generation then
    // function return warning. If generation is finished then function returns array with link to file.
    // Generate new timestamp. Get saved timestamp and use it for getCatalogList() function.
    $lastTimestamp = 1397506590; // Please overwrite this row and get timestamp from your system
    $currentTimestamp = $dropShipping->getStoreCurrentTimestamp();
    $response = $dropShipping->getCatalogList(false, $lastTimestamp);

    // Display success result or error message
    if (isset($response->success) && $response->success) {
        echo "<pre>" . print_r($response, 1) . "</pre>";
        // also you must save $currentTimestamp in your system in order to use it on the next run
    } else {
        echo "<pre>" . print_r($response->getMessage(), 1) . "</pre>";
    }


    // Third step -  repeat (scheduler/cron) Second step for get updated Catalog

*/