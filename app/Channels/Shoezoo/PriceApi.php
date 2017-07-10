<?php

namespace App\Channels\Shoezoo;

class PriceApi extends Api
{

    /**
     * @param bool $returnResult
     * @return \Exception|\SoapFault|\stdClass
     */
    public function getPriceFullList($returnResult = false)
    {
        try {
            $result = $this->_getClient()->dropshippingPriceFullList($this->getSession(), $returnResult);
        } catch (\SoapFault $exception) {
            return $exception;
        }
        return $result;
    }
}
