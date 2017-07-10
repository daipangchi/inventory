<?php

namespace App\Api\Shoezoo;

$dropShippingPrice = new PriceApi();

/*
 * Submit request for the price feed.
 * To get a price feed you should use function getPriceFullList() without parameters.
 * This function starts generation of current price feed.
 * Example:
 * $response = $dropShippingPrice->getPriceFullList();
 *
 * Use getPriceFullList(true) to check periodically the status of the feed.
 * This function return associative array with status and file link (csv)
 * Example:
 * $response = $dropShippingPrice->getPriceFullList(true);
 */

/*
 * Next example illustrate how get the price feed.
 */


// First step - Submit request for the price feed.
$response = $dropShippingPrice->getPriceFullList();

// Display success result or error message
if (isset($response->success) && $response->success) {
    echo "<pre>".print_r($response, 1)."</pre>";
}
else {
    echo "<pre>".print_r($response->getMessage(), 1)."</pre>";
}

// Second step -  to check periodically the status of the feed. If file is still in process of generation then
// function return warning. If generation is finished then function return array with file link
$response = $dropShippingPrice->getPriceFullList(true);
if (isset($response->success) && $response->success) {
    echo "<pre>".print_r($response, 1)."</pre>";
}
else {
    echo "<pre>".print_r($response->getMessage(), 1)."</pre>";
}
