<?php

namespace App\Channels\Shoezoo;

class CatalogApi extends Api
{
    /**
     * @param array $since
     * @return \Exception|\SoapFault|\stdClass
     */
    public function getProductList($since = array())
    {
        try {
            $result = empty($since) ? $this->_getClient()->dropshippingCatalogProductList($this->getSession())
                : $this->_getClient()->dropshippingCatalogProductList($this->getSession(), $since);
        } catch (\SoapFault $exception) {
            return $exception;
        }

        return $result;
    }

    /**
     * @param bool $returnResult
     * @param integer|null $updatedAtTimestamp
     * @return \Exception|\SoapFault|\stdClass
     */
    public function getCatalogList($returnResult = false, $updatedAtTimestamp = null)
    {
        try {
            $result = $this->_getClient()->dropshippingCatalogProductFullList($this->getSession(), $returnResult,
                $updatedAtTimestamp);
        } catch (\SoapFault $exception) {
            return $exception;
        }

        return $result;
    }
}
