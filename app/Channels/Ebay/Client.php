<?php

namespace App\Channels\Ebay;

use DateTime;
use DTS\eBaySDK\Sdk;
use DTS\eBaySDK\Trading\Types\PaginationType;

/**
 * Class Client
 *
 * Wrapper for eBay API SDK.
 *
 * @package App\Channels\Ebay
 */
class Client
{
    public $sdk;

    /**
     * Client constructor.
     * @param string $authToken
     */
    public function __construct(string $authToken)
    {
        $config = config('channels.ebay');
        $config['authToken'] = $authToken;
        $this->sdk = new Sdk($config);
    }

    /**
     * @param \DateTime $startFrom
     * @param \DateTime $startTo
     * @return \DTS\eBaySDK\Trading\Types\GetSellerListRequestType
     */
    public function buildGetSellerListRequestObject(\DateTime $startFrom, \DateTime $startTo)
    {
        $request = new \DTS\eBaySDK\Trading\Types\GetSellerListRequestType();
        $request->StartTimeFrom = $startFrom;
        $request->StartTimeTo = $startTo;
        $request->IncludeVariations = true;
        $request->DetailLevel = [\DTS\eBaySDK\Trading\Enums\DetailLevelCodeType::C_ITEM_RETURN_DESCRIPTION];
        $request->Pagination = new PaginationType();
        $request->Pagination->EntriesPerPage = 200;

        return $request;
    }
}
