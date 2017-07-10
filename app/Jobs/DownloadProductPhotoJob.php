<?php

namespace App\Jobs;

use App\Models\Products\Image;
use App\Models\Products\Product;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Image as Intervention;
use Storage;

class DownloadProductPhotoJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var string
     */
    public $connection = 'photos';

    /**
     * @var string
     */
    public $queue = 'photos';

    /**
     * @var int
     */
    protected $merchantId;

    /**
     * @var int
     */
    protected $productId;

    /**
     * @var string
     */
    protected $productSku;

    /**
     * @var array
     */
    protected $urls;

    /**
     * Create a new job instance.
     *
     * @param int $merchantId
     * @param int $productId
     * @param string $productSku
     * @param array $urls
     */
    public function __construct(int $merchantId, int $productId, string $productSku, array $urls)
    {
        $this->merchantId = $merchantId;
        $this->productId = $productId;
        $this->productSku = $productSku;
        $this->urls = $urls;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $storage = Storage::disk();
        $date = (string)Carbon::now();

        // cache to prevent n + 1 queries
        $existing = Image::whereIn('original_url', $this->urls)->get();

        foreach ($this->urls as $index => $url) {
            $path = "products/$this->merchantId/$this->productSku-$index.jpg";
            $storagePath = $storage->url($path);
            $success = true;

            // If there is an image from the url that is already downloaded.
            // Don't download again and save the reference to existing
            // image to the new model. This means reusing a photo.
            if ($image = $existing->where('original_url', $url)->first()) {
                $storagePath = $image->url;
            } else if (! $storage->exists($storagePath)) {

                $headers_result = get_headers($url);
                $headers        = substr($headers_result[0], 9, 3);

                if($headers == "200"){
                    try {
                        $img = Intervention::make(file_get_contents($url));
                        $storage->put($path, $img->encode('jpg'));
                    }
                    catch (ClientException $e) {
                        $success = false;
                    }
                }else{
                    $success = false;
                }
            }

            if ($success) {
                Image::create([
                    'product_id'   => $this->productId,
                    'url'          => $storagePath,
                    'original_url' => $url,
                    'created_at'   => $date,
                    'updated_at'   => $date,
                ]);
            }
        }
    }
}