<?php

namespace App\Api\Shoezoo;

$dropShippingInventory = new CatalogInventoryApi();

/*
 * Submit request for inventory feed.
 * To get a inventory feed you should use function getCatalogInventoryFullList() without parameters.
 * This function starts generation of current inventory feed.
 * Example:
 * $response = $dropShippinginventory->getCatalogInventoryFullList();

 *
 * Use getCatalogInventoryFullList(true) to check periodically the status of the feed.
 * This function return associative array with status and the link to file(csv)
 * Example:
 * $response = $dropShippinginventory->getCatalogInventoryFullList(true);
 */

/*
 * Next example illustrates how get full catalog inventory
 */


// First step - Submit request for inventory feed.
$response = $dropShippingInventory->getCatalogInventoryFullList();

// Display success result or error message
if (isset($response->success) && $response->success) {
    echo "<pre>".print_r($response, 1)."</pre>";
}
else {
    echo "<pre>".print_r($response->getMessage(), 1)."</pre>";
}

// Second step -  to check periodically the status of the feed. If file is still in process of generation then
// function returns warning. If generation is finished then function returns array with the link to file.
$response = $dropShippingInventory->getCatalogInventoryFullList(true);
if (isset($response->success) && $response->success) {
    echo "<pre>".print_r($response, 1)."</pre>";
}
else {
    echo "<pre>".print_r($response->getMessage(), 1)."</pre>";
}
