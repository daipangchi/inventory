<?php

namespace App\Channels\Amazon;

use SimpleXMLElement;

/**
 * Class Client
 *
 * Amazon API client.
 *
 * @link http://docs.developer.amazonservices.com/en_UK/reports/Reports_Overview.html
 * @package App\Channels\Amazon
 */
class Reports extends Client
{
    const INVENTORY_REPORT = '_GET_FLAT_FILE_OPEN_LISTINGS_DATA_';
    const ACTIVELISTINGSREPORT = '_GET_FLAT_FILE_OPEN_LISTINGS_DATA_';
    const OPEN_LISTINGS_REPORT = '_GET_MERCHANT_LISTINGS_DATA_BACK_COMPAT_';
    const OPEN_LISTINGS_REPORT_LITE = '_GET_MERCHANT_LISTINGS_DATA_LITE_';
    const OPEN_LISTINGS_REPORT_LITER = '_GET_MERCHANT_LISTINGS_DATA_LITER_';
    const CANCELED_LISTINGS_REPORT = '_GET_MERCHANT_CANCELLED_LISTINGS_DATA_';
    const SOLD_LISTINGS_REPORT = '_GET_CONVERGED_FLAT_FILE_SOLD_LISTINGS_DATA_';
    const LISTING_QUALITY_AND_SUPPRESSED_LISTING_REPORT = '_GET_MERCHANT_LISTINGS_DEFECT_DATA_';
    const BROWSE_TREE_REPORT = '_GET_XML_BROWSE_TREE_DATA_';

    /**
     * Initiate a request for a report.
     *
     * @link http://docs.developer.amazonservices.com/en_US/reports/Reports_RequestReport.html
     * @param string $reportType http://docs.developer.amazonservices.com/en_US/reports/Reports_ReportType.html
     * @param array $options
     * @return SimpleXMLElement
     */
    public function requestReport(string $reportType, array $options = []) : SimpleXMLElement
    {
        $url = $this->generateUrl('/Reports/2009-01-01', array_merge([
            'Action'     => 'RequestReport',
            'Version'    => '2009-01-01',
            'ReportType' => $reportType,
        ], $options));

        return simplexml_load_string($this->sendRequest($url)->getBody());
    }

    /**
     * Send request to check status of report(s).
     *
     * @link http://docs.developer.amazonservices.com/en_US/reports/Reports_GetReportRequestList.html
     * @param array $requestIds
     * @return SimpleXMLElement
     * @throws \Exception
     */
    public function getReportRequestList(array $requestIds = []) : SimpleXMLElement
    {
        $options = array_merge([
            'Action'  => 'GetReportRequestList',
            'Version' => '2009-01-01',
        ], $requestIds);
        $url = $this->generateUrl('/Reports/2009-01-01', $options);
        $count = 0;

        do {
            if ($count > 9) {
                abort(522);
            }

            // Set interval at 45 seconds so we don't get throttled by Amazon.
            sleep(45);

            $reportRequestList = simplexml_load_string($this->sendRequest($url)->getBody());

            $status = $reportRequestList->GetReportRequestListResult->ReportRequestInfo->ReportProcessingStatus;
            $count++;
        } while ($status != '_DONE_');

        return $reportRequestList;
    }

    /**
     * Send request with NextToken to check status of report(s).
     *
     * @link http://docs.developer.amazonservices.com/en_US/reports/Reports_GetReportRequestListByNextToken.html
     * @param string $nextToken
     * @return SimpleXMLElement
     */
    public function getReportListByNextToken(string $nextToken) : SimpleXMLElement
    {
        $url = $this->generateUrl('/Reports/2009-01-01', [
            'Action'    => 'GetReportRequestListByNextToken',
            'Version'   => '2009-01-01',
            'NextToken' => $nextToken,
        ]);

        return simplexml_load_string($this->sendRequest($url)->getBody());
    }

    /**
     * Send request to get a completed and generated report.
     *
     * @link http://docs.developer.amazonservices.com/en_US/reports/Reports_GetReport.html
     * @param string $generatedReportId
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getReport(string $generatedReportId)
    {
        $url = $this->generateUrl('/Reports/2009-01-01', [
            'Action'   => 'GetReport',
            'Version'  => '2009-01-01',
            'ReportId' => $generatedReportId,
        ]);

        return $this->sendRequest($url)->getBody();
    }

    /**
     * If the response is a completed and generated report, we need to
     * parse it into an array of objects from a tab delimited file.
     *
     * @param string $response
     * @return array
     */
    public function parseGeneratedReport(string $response) : array
    {
        $response = rtrim($response, "\n"); // Removes trailing new line so it doesn't create an empty row when parsing
        $response = explode("\n", $response);
        $columns = explode("\t", array_splice($response, 0, 1)[0]);

        foreach ($response as $key => $row) {
            $row = explode("\t", $row);
            $new = [];

            foreach ($row as $index => $cell) {
                $new[$columns[$index]] = $cell;
            }

            $response[$key] = $new;
        }

        return $response;
    }
}
