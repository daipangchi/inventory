<?php

namespace App\Console\Commands\Magmi;

use App\CustomerPortal\Magmi\CsvGenerator;
use App\Models\Merchants\Merchant;
use Illuminate\Console\Command;

class MagmiUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magmi:update {merchant_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $merchant = Merchant::find($this->argument('merchant_id'));

        $products = new CsvGenerator($merchant, 'update');
        $products->generate();
    }
}
