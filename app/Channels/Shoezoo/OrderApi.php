<?php

namespace App\Channels\Shoezoo;

class OrderApi extends Api
{

    /**
     * @param $orderData
     * @return \Exception|\SoapFault|\stdClass
     */
    public function createOrder($orderData)
    {
        try {
            $result = $this->_getClient()->dropshippingOrderCreate(
                $this->getSession(),
                $orderData
            );
        } catch (\SoapFault $exception) {
            return $exception;
        }
        return $result;
    }

    /**
     * @param $orderIncrementId
     * @return \Exception|\SoapFault|\stdClass
     */
    public function heartbeatOrder($orderIncrementId)
    {
        try {
            $result = $this->_getClient()->dropshippingOrderHeartbeat($this->getSession(),
                array('orderIncrementId' => $orderIncrementId));
        } catch (\SoapFault $exception) {
            return $exception;
        }
        return $result;
    }

    /**
     * @param $orderIncrementId
     * @return \Exception|\SoapFault|\stdClass
     */
    public function finalizeOrder($orderIncrementId)
    {
        try {
            $result = $this->_getClient()->dropshippingOrderFinalize($this->getSession(), $orderIncrementId);
        } catch (\SoapFault $exception) {
            return $exception;
        }
        return $result;
    }

    /**
     * @param $orderIncrementId
     * @return \Exception|\SoapFault|\stdClass
     */
    public function abortOrder($orderIncrementId)
    {
        try {
            $result = $this->_getClient()->dropshippingOrderAbort($this->getSession(), $orderIncrementId);
        } catch (\SoapFault $exception) {
            return $exception;
        }
        return $result;
    }

    /**
     * @param $orderIncrementId
     * @return \Exception|\SoapFault|\stdClass
     */
    public function cancelOrder($orderIncrementId)
    {
        try {
            $result = $this->_getClient()->dropshippingOrderCancel($this->getSession(), $orderIncrementId);
        } catch (\SoapFault $exception) {
            return $exception;
        }
        return $result;
    }

    /**
     * @param array $since
     * @return \Exception|\SoapFault|\stdClass
     */
    public function getOrderList($since = array())
    {
        try {
            $result = empty($since) ? $this->_getClient()->dropshippingOrderList($this->getSession())
                : $this->_getClient()->dropshippingOrderList($this->getSession(), $since);
        } catch (\SoapFault $exception) {
            return $exception;
        }
        return $result;
    }
}

