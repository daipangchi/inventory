<?php

namespace App\Console\Commands\Magmi;

use App\CustomerPortal\Magmi\CsvGenerator;
use App\Models\Merchants\Merchant;
use Illuminate\Console\Command;

class MagmiCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magmi:create {merchant_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates products CSV file to post it to the Merchant Portal backend for Magmi import';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $start = microtime(true);

        $merchant = Merchant::find($this->argument('merchant_id'));

        $products = new CsvGenerator($merchant, 'create');
        $products->generate();

        echo (microtime(true) - $start).PHP_EOL;
    }
}
