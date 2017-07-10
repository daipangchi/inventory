<?php

namespace App\Channels\Amazon;

use GuzzleHttp\Exception\ServerException;

/**
 * Base Amazon API client.
 *
 * @package App\Channels\Amazon
 */
abstract class Client
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $token;

    /**
     * Amazon API Client constructor.
     *
     * @param string $sellerId
     * @param string $mwsAuthToken
     */
    public function __construct(string $sellerId, string $mwsAuthToken)
    {
        $this->client = new \GuzzleHttp\Client();

        $this->config = [
            'AWSAccessKeyId'   => config('channels.amazon.aws_access_key'),
            'SellerId'         => $sellerId,
            'SignatureMethod'  => 'HmacSHA256',
            'SignatureVersion' => '2',
            'MWSAuthToken'     => $mwsAuthToken,
        ];

        $this->token = config('channels.amazon.mws_secret');
    }

    /**
     * Generate URL for request.
     *
     * @param string $path
     * @param array $options
     * @return string
     */
    protected function generateUrl(string $path, array $options = []) : string
    {
        if (! starts_with($path, '/')) {
            $path = '/'.$path;
        }

        $options = array_merge($this->config, $options, [
            'Timestamp' => gmdate("Y-m-d\\TH:i:s.\\0\\0\\0\\Z", time()),
        ]);
        $params = [];

        foreach ($options as $key => $val) {
            $key = str_replace("%7E", "~", rawurlencode($key));
            $val = str_replace("%7E", "~", rawurlencode($val));
            if ($key == 'RootNodesOnly') $val = true;
            $params[] = "$key=$val";
        }
        sort($params);

        $params = implode('&', $params);

        $signature = $this->generateSignature($params, $path);

        return "https://mws.amazonservices.com/$path?$params&Signature=$signature";
    }

    /**
     * Send the actual request.
     *
     * @param string $link
     * @return \Psr\Http\Message\ResponseInterface|\SimpleXMLElement
     */
    protected function sendRequest(string $link)
    {
        try {
            return $this->client->get($link, ['headers' => [
                'Content-Type'        => 'application/xml',
                'x-amazon-user-agent' => 'AmazonJavascriptScratchpad/1.0 (Language=Javascript)',
            ]]);
        } catch (ServerException $e) {
            \Log::error((string)$e->getResponse()->getBody());

            throw $e;
        }
    }

    /**
     * Generate signature.
     *
     * @param string $params
     * @param string $path
     * @return string
     */
    protected function generateSignature(string $params, string $path) : string
    {
        $sign = "GET\n";
        $sign .= "mws.amazonservices.com\n";
        $sign .= "$path\n";
        $sign .= $params;

        $signature = hash_hmac("sha256", $sign, $this->token, true);

        return urlencode(base64_encode($signature));
    }
}