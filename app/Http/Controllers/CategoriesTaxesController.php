<?php

namespace App\Http\Controllers;

use App\Models\CategoryTax;
use Illuminate\Http\Request;

use App\Http\Requests;

class CategoriesTaxesController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function destroy($categoryId, $categoryTaxId)
    {
        $tax = CategoryTax::find($categoryTaxId);

        if ($tax) {
            $tax->delete();
        }

        return response(['data' => 'good']);
    }
}
