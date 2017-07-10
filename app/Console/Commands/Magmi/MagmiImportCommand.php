<?php

namespace App\Console\Commands\Magmi;

use App\Models\MagmiLog;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class MagmiImportCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'magmi:import {merchant_id} {action}';

    /**
     * @var string
     */
    protected $description = 'Give a command to Magmi to perform products import';

    /**
     * @return string
     */
    public function handle()
    {
        $magmiSecureUrl = $this->getMagmiUrl();
        $this->info('Importing from Magmi...');

        $client = new Client([
            'base_uri' => $magmiSecureUrl,
            'timeout'  => 600,
        ]);

        $response = $client->get($magmiSecureUrl);
        $result = $response->getBody()->getContents();

        MagmiLog::create(['data' => strip_tags($result)]);

        $this->info('Magmi response: '.$result);
    }

    /**
     * @return string
     */
    private function getMagmiUrl()
    {
        $login = config('magmi.login');
        $password = config('magmi.password');
        $host = config('magmi.host');
        $mode = config('magmi.profile_name');
        $id = $this->argument('merchant_id');
        $action = $this->argument('action');

        $url = "http://$login:$password@$host/magmi/web/magmi_run.php";
        $url .= "?mode=$mode";
        $url .= "&profile=cadabra_$action";
        $url .= "&engine=magmi_productimportengine:Magmi_ProductImportEngine";
        $url .= "&CSV:filename=/var/www/public_html/var/portal_storage/$id/$action/products.csv";

        if ($action == 'create') {
            $url .= "&5B5ATI:filename=/var/www/public_html/var/portal_storage/$id/$action/attribute.csv";
            $url .= "&5B5ASI:filename=/var/www/public_html/var/portal_storage/$id/$action/attribute_set.csv";
            $url .= "&5B5AAI:filename=/var/www/public_html/var/portal_storage/$id/$action/attribute_set_association.csv";
        }

        return $url;
    }
}
