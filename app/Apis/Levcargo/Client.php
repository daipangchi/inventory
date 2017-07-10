<?php

namespace App\Apis\Levcargo;

use Cache;
use Config;
use GuzzleHttp\Exception\ClientException;

class Client
{
    /**
     * Api token to LevCargo
     *
     * @var string
     */
    private $token;

    /**
     * Client constructor.
     */
    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.levcargogoods.com',
        ]);

        //$this->token = Cache::get('levcargo.token');
    }

    /**
     * @return $this
     */
    public function authenticate()
    {
        if ($this->isAuthenticated()) return $this;

        $response = $this->client->request('get', '/api/token', [
            'form_params' => [
                'userName'   => config('services.levcargo.username'),
                'password'   => config('services.levcargo.password'),
                'grant_type' => 'password',
            ],
        ]);

        if ($response->getStatusCode() == 200 || $response->getStatusCode() == 201) {
            $response = \GuzzleHttp\json_decode($response->getBody(), true);

            Cache::put('levcargo.token', $response['access_token'], (int)$response['expires_in'] / 60);

            $this->token = $response['access_token'];
        }

        return $this;
    }

    /**
     * @param $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function placeOrder($params)
    {
        return $this->client->request('post', '/api/Store/Orders', [
            'headers' => ['Authorization' => 'Bearer '.$this->token],
            'json'    => $params,
        ]);
    }

    /**
     * @return bool
     */
    private function isAuthenticated()
    {
        return (bool)$this->token;
    }
}
