<?php

namespace App\Channels\Shoezoo;

class CatalogInventoryApi extends Api
{
    /**
     * @param array $since
     * @return \Exception|\SoapFault|\stdClass
     */
    public function getCatalogInventoryList($since = array())
    {
        $session = $this->getSession();
        $client = $this->_getClient();

        try {
            if (empty($since)) {
                $result = $client->dropshippingCatalogInventoryList($session);
            }
            else {
                $result = $client->dropshippingCatalogInventoryList($session, $since);
            }
        } catch (\SoapFault $exception) {
            return $exception;
        }

        return $result;
    }

    /**
     * @param bool $returnResult
     * @return \Exception|\SoapFault|\stdClass
     */
    public function getCatalogInventoryFullList($returnResult = false)
    {
        $soapClient = $this->_getClient();
        $session = $this->getSession();

        try {
            $result = $soapClient->dropshippingCatalogInventoryFullList($session, $returnResult);
        } catch (\SoapFault $exception) {
            return $exception;
        }

        return $result;
    }
}
