<?php

namespace App\Api\Shoezoo;

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

$dropShippingOrder = new OrderApi();

// Create Order
/* PLEASE NOTE that prices and qty in following examples are used as placeholders.
 * USE prices/qty that are provided to you with price or catalog feeds.
 *
 * To create order use function createOrder(). This function takes one array with order number and product list
 * Product list is an array of order's items. Order Item is associative array with 'sku', 'qty' and 'price'.
 * See below example of the param for createOrder() function.
 *
 * $orderData = array(
 *     'orderNumber' => uniqid(), //generate unique order number
 *     'productList' => array(
 *         array('sku' => '0844229064104', 'qty' => 1, 'price' => 20)
 *     )
 * );
 * $response = $dropShippingOrder->createOrder($orderData);
 */

/*
 * Function checks if order exists and have status “pending checkout”.
 * Orders that hasn’t been completed within 30min, will be recycled.
 *
 * Example:
 * $response = $dropShippingOrder->heartbeatOrder('1200000019-5523ed8b5daa3');
 */

/*
 * To finalize the order you can use function finalizeOrder()
 * This function takes only one parameter - order increment id. It changes order status and creates invoice.
 *
 * Example:
 * $response = $dropShippingOrder->finalizeOrder('1200000019-5523ed8b5daa3');
 */

/*
 * To cancel the order you can use function cancelOrder()
 * This function takes one argument - order increment id and cancel order (if order has status "Pending Checkout")
 *
 * Example:
 * $response = $dropShippingOrder->cancelOrder('1200000014');
 */


/*
 * To get the order list you must use function getOrderList()
 * if function are called without params function returns all orders.
 * Function returns flag the "end of the list" and parameters ""timestamp" and "orderId" for next call.
 * Example:
 * $response = $dropShippingOrder->getOrderList();
 */

/*
 * You can set part to order list. Function takes since associative array which must contain last "timestamp"
 * and last returned "orderId".
 * Example:
 * $response = $dropShippingOrder->getOrderList(array('timestamp' => 1423233173, 'orderId' => 661761));
*/


/*=================================================== Normal Flow ===================================================*/
/*
 * Next example illustrates how create order with one simple product and finalize order
 */


// First step - create order with simple configuration
$orderData = [
    'orderNumber' => uniqid(), //generate unique order number
    'productList' => [
        ['sku' => '0844229064104', 'qty' => 1, 'price' => 32.93],
    ],
];
$response = $dropShippingOrder->createOrder($orderData);
// Second step - if order was created successfully than finalize the order
if (isset($response->success) && $response->success) {
    $response = $dropShippingOrder->finalizeOrder($response->orderIncrementId);
}
echo "<pre>".print_r($response, 1)."</pre>";


/*--------------------------------------------------------------------------------------------------------------------*/

/*
 * Next example illustrates how create order with one simple product (quantity 3) and finalize order
 */


// First step - create order with simple configuration
$orderData = [
    'orderNumber' => uniqid(), //generate unique order number
    'productList' => [
        ['sku' => '0844229064104', 'qty' => 3, 'price' => 32.93],
    ],
];
$response = $dropShippingOrder->createOrder($orderData);
// Second step - if order was created successfully than finalize the order
if (isset($response->success) && $response->success) {
    $response = $dropShippingOrder->finalizeOrder($response->orderIncrementId);
}
echo "<pre>".print_r($response, 1)."</pre>";


/*--------------------------------------------------------------------------------------------------------------------*/


/*
 * Next example illustrates how create order with two simple products with different quantities and finalize order
 */


// First step - create order with simple configuration
$orderData = [
    'orderNumber' => uniqid(), //generate unique order number
    'productList' => [
        ['sku' => '0844229064104', 'qty' => 1, 'price' => 32.93], // first product with quantity 1
        ['sku' => '0887229663915', 'qty' => 2, 'price' => 37.43] // second product with quantity 2
    ],
];
$response = $dropShippingOrder->createOrder($orderData);
// Second step - if order was created successfully than finalize order
if (isset($response->success) && $response->success) {
    $response = $dropShippingOrder->finalizeOrder($response->orderIncrementId);
}
echo "<pre>".print_r($response, 1)."</pre>";


/*--------------------------------------------------------------------------------------------------------------------*/


/*
 * Next example illustrates how create order with ten simple products with different quantities and finalize order
 */


// First step - create order with simple configuration
$orderData = [
    'orderNumber' => uniqid(), //generate unique order number
    'productList' => [
        ['sku' => '0844229064104', 'qty' => 1, 'price' => 32.93], // first product with quantity 1
        ['sku' => '0887229663915', 'qty' => 2, 'price' => 37.43], // second product with quantity 2
        ['sku' => '0887229663908', 'qty' => 1, 'price' => 37.43], // ...
        ['sku' => '0091206423960', 'qty' => 1, 'price' => 74.93], // ...
        ['sku' => '0810056018962', 'qty' => 2, 'price' => 32.93], // ...
        ['sku' => '0887229531078', 'qty' => 3, 'price' => 67.43], // ...
        ['sku' => '0091201446995', 'qty' => 1, 'price' => 62.18], // ...
        ['sku' => '0659658761568', 'qty' => 1, 'price' => 41.18], // ...
        ['sku' => '0022859737388', 'qty' => 1, 'price' => 33.68], // ...
        ['sku' => '0885166908298', 'qty' => 2, 'price' => 24.68] // tenth product with quantity 2
    ],
];
$response = $dropShippingOrder->createOrder($orderData);
// Second step - if order was created successfully than finalize order
if (isset($response->success) && $response->success) {
    $response = $dropShippingOrder->finalizeOrder($response->orderIncrementId);
}
echo "<pre>".print_r($response, 1)."</pre>";


/*=========================================== Pending Checkout and Cancel ===========================================*/
/*
 * Next example illustrates how to create order with one simple product and cancel order
 */


// First step - create order with simple configuration
$orderData = [
    'orderNumber' => uniqid(), //generate unique order number
    'productList' => [
        ['sku' => '0844229064104', 'qty' => 1, 'price' => 32.93],
    ],
];
$response = $dropShippingOrder->createOrder($orderData);
//// Second step - if order was created successfully than cancel order
if (isset($response->success) && $response->success) {
    $response = $dropShippingOrder->cancelOrder($response->orderIncrementId);
}
echo "<pre>".print_r($response, 1)."</pre>";


/*--------------------------------------------------------------------------------------------------------------------*/


/*
 * Next example illustrates how create order with one simple product (quantity 3) and cancel order
 */


// First step - create order with simple configuration
$orderData = [
    'orderNumber' => uniqid(), //generate unique order number
    'productList' => [
        ['sku' => '0844229064104', 'qty' => 3, 'price' => 32.93],
    ],
];
$response = $dropShippingOrder->createOrder($orderData);
//// Second step - if order was created successfully than cancel order
if (isset($response->success) && $response->success) {
    $response = $dropShippingOrder->cancelOrder($response->orderIncrementId);
}
echo "<pre>".print_r($response, 1)."</pre>";


/*--------------------------------------------------------------------------------------------------------------------*/


/*
 * Next example illustrates how create order with two simple products with different quantities and cancel order
 */


// First step - create order with simple configuration
$orderData = [
    'orderNumber' => uniqid(), //generate unique order number
    'productList' => [
        ['sku' => '0844229064104', 'qty' => 1, 'price' => 32.93], // first product with quantity 1
        ['sku' => '0887229663915', 'qty' => 2, 'price' => 37.43] // second product with quantity 2
    ],
];
$response = $dropShippingOrder->createOrder($orderData);
// Second step - if order was created successfully than cancel order
if (isset($response->success) && $response->success) {
    $response = $dropShippingOrder->cancelOrder($response->orderIncrementId);
}
echo "<pre>".print_r($response, 1)."</pre>";


/*--------------------------------------------------------------------------------------------------------------------*/


/*
 * Next example illustrates how create order with ten simple products with different quantities and cancel order
 */


// First step - create order with simple configuration
$orderData = [
    'orderNumber' => uniqid(), //generate unique order number
    'productList' => [
        ['sku' => '0844229064104', 'qty' => 1, 'price' => 32.93], // first product with quantity 1
        ['sku' => '0887229663915', 'qty' => 2, 'price' => 37.43], // second product with quantity 2
        ['sku' => '0887229663908', 'qty' => 1, 'price' => 37.43], // ...
        ['sku' => '0091206423960', 'qty' => 1, 'price' => 74.93], // ...
        ['sku' => '0810056018962', 'qty' => 2, 'price' => 32.93], // ...
        ['sku' => '0887229531078', 'qty' => 3, 'price' => 67.43], // ...
        ['sku' => '0091201446995', 'qty' => 1, 'price' => 62.18], // ...
        ['sku' => '0659658761568', 'qty' => 1, 'price' => 41.18], // ...
        ['sku' => '0022859737388', 'qty' => 1, 'price' => 33.68], // ...
        ['sku' => '0885166908298', 'qty' => 2, 'price' => 24.68] // tenth product with quantity 2
    ],
];
$response = $dropShippingOrder->createOrder($orderData);
// Second step - if order was created successfully than cancel order
if (isset($response->success) && $response->success) {
    $response = $dropShippingOrder->cancelOrder($response->orderIncrementId);
}
echo "<pre>".print_r($response, 1)."</pre>";


/*=================================================== No Inventory ===================================================*/

/*
 * Next example illustrates inventory error during create order with one simple product out of stock
 */


// First step - create order with simple configuration
$orderData = [
    'orderNumber' => uniqid(), //generate unique order number
    'productList' => [
        ['sku' => '0091206424011', 'qty' => 1, 'price' => 74.93],
    ],
];
$response = $dropShippingOrder->createOrder($orderData);
// Second step - if order was created successfully than finalize order
if (isset($response->success) && $response->success) {
    $response = $dropShippingOrder->finalizeOrder($response->orderIncrementId);
}
else {
    $exceptionArray = (array)json_decode($response->getMessage());
    if ($exceptionArray['code'] == 7005 && isset($exceptionArray['quantity']) && isset($exceptionArray['sku'])) {
        $quantity = $exceptionArray['quantity'];
        $sku = $exceptionArray['sku'];
        echo "SKU: $sku. Available quantity: $quantity";
    }
    // return "SKU: 0091206424011. Available quantity: 0"
}
echo "<pre>".print_r($response, 1)."</pre>";


/*--------------------------------------------------------------------------------------------------------------------*/


/*
 * Next example illustrates inventory error during create order with one simple product no sufficient inventory
 */


// First step - create order with simple configuration
$orderData = [
    'orderNumber' => uniqid(), //generate unique order number
    'productList' => [
        ['sku' => '0659658761544', 'qty' => 3, 'price' => 41.18],
    ],
];
$response = $dropShippingOrder->createOrder($orderData);
// Second step - if order was created successfully than finalize order
if (isset($response->success) && $response->success) {
    $response = $dropShippingOrder->finalizeOrder($response->orderIncrementId);
}
else {
    $exceptionArray = (array)json_decode($response->getMessage());
    if ($exceptionArray['code'] == 7005 && isset($exceptionArray['quantity']) && isset($exceptionArray['sku'])) {
        $quantity = $exceptionArray['quantity'];
        $sku = $exceptionArray['sku'];
        echo "SKU: $sku. Available quantity: $quantity";
    }
    // return "SKU: 0659658761544. Available quantity: 1"
}
echo "<pre>".print_r($response, 1)."</pre>";


/*--------------------------------------------------------------------------------------------------------------------*/


/*
 * Next example illustrates inventory error during create order with two simple product one of them no sufficient inventory
 */


// First step - create order with simple configuration
$orderData = [
    'orderNumber' => uniqid(), //generate unique order number
    'productList' => [
        ['sku' => '0844229064104', 'qty' => 1, 'price' => 74.93],
        ['sku' => '0659658761544', 'qty' => 3, 'price' => 41.18],
    ],
];
$response = $dropShippingOrder->createOrder($orderData);
// Second step - if order was created successfully than finalize order
if (isset($response->success) && $response->success) {
    $response = $dropShippingOrder->finalizeOrder($response->orderIncrementId);
}
else {
    echo "<pre>".print_r($response->getMessage(), 1)."</pre>";
    // return "The requested quantity for "Nike Men's Son Of Force White/White/Black/Green Glow Basketball Shoe 10 Men US" is not available"
}
echo "<pre>".print_r($response, 1)."</pre>";


/*--------------------------------------------------------------------------------------------------------------------*/


/*
 * Next example illustrates inventory error during create order with ten simple product one of them no sufficient inventory
 */


// First step - create order with simple configuration
$orderData = [
    'orderNumber' => uniqid(), //generate unique order number
    'productList' => [
        ['sku' => '0844229064104', 'qty' => 1, 'price' => 32.93], // first product with quantity 1
        ['sku' => '0887229663915', 'qty' => 2, 'price' => 37.43], // second product with quantity 2
        ['sku' => '0887229663908', 'qty' => 1, 'price' => 37.43], // ...
        ['sku' => '0091206423960', 'qty' => 1, 'price' => 74.93], // ...
        ['sku' => '0810056018962', 'qty' => 2, 'price' => 32.93], // ...
        ['sku' => '0659658761544', 'qty' => 3, 'price' => 41.18], // ...
        ['sku' => '0091201446995', 'qty' => 1, 'price' => 62.18], // ...
        ['sku' => '0659658761568', 'qty' => 1, 'price' => 41.18], // ...
        ['sku' => '0022859737388', 'qty' => 1, 'price' => 33.67], // ...
        ['sku' => '0885166908298', 'qty' => 2, 'price' => 27.67] // tenth product with quantity 2
    ],
];
$response = $dropShippingOrder->createOrder($orderData);
// Second step - if order was created successfully than finalize order
if (isset($response->success) && $response->success) {
    $response = $dropShippingOrder->finalizeOrder($response->orderIncrementId);
}
else {
    echo "<pre>".print_r($response->getMessage(), 1)."</pre>";
    // return "The requested quantity for "Nike Men's Son Of Force White/White/Black/Green Glow Basketball Shoe 10 Men US" is not available"
}
echo "<pre>".print_r($response, 1)."</pre>";


/*=============================================== Wrong/Illegal input ===============================================*/

/*
 * Next example illustrates inventory error during create order with a simple product not in catalog
 */

// First step - create order with simple configuration
$orderData = [
    'orderNumber' => uniqid(), //generate unique order number
    'productList' => [
        ['sku' => '00912064240119999', 'qty' => 1, 'price' => 74.93],
    ],
];
$response = $dropShippingOrder->createOrder($orderData);
// Second step - if order was created successfully than finalize order
if (isset($response->success) && $response->success) {
    $response = $dropShippingOrder->finalizeOrder($response->orderIncrementId);
}
else {
    echo "<pre>".print_r($response->getMessage(), 1)."</pre>";
    // return "Product with SKU 00912064240119999 does not exist"
}
echo "<pre>".print_r($response, 1)."</pre>";


/*--------------------------------------------------------------------------------------------------------------------*/

/*
 * Next example illustrates price error during create order with a simple product - negative price
 */


// First step - create order with simple configuration
$orderData = [
    'orderNumber' => uniqid(), //generate unique order number
    'productList' => [
        ['sku' => '0091206424011', 'qty' => 1, 'price' => -74.93],
    ],
];
$response = $dropShippingOrder->createOrder($orderData);
// Second step - if order was created successfully than finalize order
if (isset($response->success) && $response->success) {
    $response = $dropShippingOrder->finalizeOrder($response->orderIncrementId);
}
else {
    echo "<pre>".print_r($response->getMessage(), 1)."</pre>";
    // return "Product price does not exist"
}
echo "<pre>".print_r($response, 1)."</pre>";


/*--------------------------------------------------------------------------------------------------------------------*/

/*
 * Next example illustrates price error during create order with two simple product with wrong price
 */


// First step - create order with simple configuration
$orderData = [
    'orderNumber' => uniqid(), //generate unique order number
    'productList' => [
        ['sku' => '0844229064104', 'qty' => 1, 'price' => 15], // first product with quantity 1
        ['sku' => '0887229663915', 'qty' => 2, 'price' => 14] // second product with quantity 2
    ],
];
$response = $dropShippingOrder->createOrder($orderData);
// Second step - if order was created successfully than finalize order
if (isset($response->success) && $response->success) {
    $response = $dropShippingOrder->finalizeOrder($response->orderIncrementId);
}
else {
    echo "<pre>".print_r($response->getMessage(), 1)."</pre>";
    // return "{0844229064104,40.83}{0887229663915,46.41}"
}
echo "<pre>".print_r($response, 1)."</pre>";


/*--------------------------------------------------------------------------------------------------------------------*/

/*=============================================== Retrieve order list ================================================*/
/*
 * Next example illustrates how get all orders
 */

$endList = false;
$orders = [];
$since = [];
while (! $endList) {
    $response = $dropShippingOrder->getOrderList($since);
    if (isset($response->success) && $response->success) {
        $orders = array_merge($orders, $response->orders);
        $endList = $response->endList;
        if (isset($response->timestamp) && $response->timestamp) {
            $since['timestamp'] = $response->timestamp;
        }
        else {
            unset($since['timestamp']);
        }
        if (isset($response->orderId) && $response->orderId) {
            $since['orderId'] = $response->orderId;
        }
        else {
            unset($since['orderId']);
        }
    }
    else {
        $endList = true;
        echo "<pre>".print_r($response->getMessage(), 1)."</pre>";
    }
}
echo "<pre>".print_r($orders, 1)."</pre>";
