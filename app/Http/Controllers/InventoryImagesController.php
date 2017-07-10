<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Products\Image;
use App\Models\Products\Product;
use Illuminate\Http\Request;
use Storage;

class InventoryImagesController extends Controller
{
    /**
     * Create product for images.
     *
     * @param Request $request
     * @param $productId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request, $productId)
    {
        $this->validate($request, [
            'files'   => 'required|max:255',
            'files.*' => 'image',
        ]);

        $product = Product::findOrFail($productId);
        $storage = Storage::disk();

        foreach ($request->file('files') as $file) {
            $image = \Image::make($file->getRealPath());

            $index = 0;
            $id = $product->id;
            $sku = $product->sku;

            do {
                $index++;
                $path = "products/$id/$sku-$index.jpg";
            } while ($storage->exists($path));

            $storage->put($path, $image->encode('jpg'));

            Image::create([
                'product_id' => $productId,
                'url'        => $storage->url($path),
            ]);
        }

        $request->session()->flash('success', 'Successfully uploaded images.');

        return response(['message' => 'Good']);
    }

    public function destroy($productId, $imageId)
    {
        Image::findOrFail($imageId)->delete();

        return response(['message' => 'Good']);
    }
}
