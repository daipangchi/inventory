@extends('master')

@section('content')
    <div class="page-orders-create">
        <div class="container">
            <h1>Inventory - New</h1>

            <form action="">
                {{ csrf_field() }}

                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Product Name</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="text" class="form-control" placeholder="Product Name">
                        </p>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="category" class="col-sm-2 form-control-label">Category</label>
                    <div class="col-sm-5">
                        <p class="form-control-static">
                            <select id="category" class="form-control">
                                <option selected>Select Category</option>
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                            </select>
                        </p>
                    </div>
                    <div class="col-sm-5">
                        <p class="form-control-static">
                            <select class="form-control">
                                <option selected>Select Subcategory</option>
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                            </select>
                        </p>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="condition" class="col-sm-2 form-control-label">Condition</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <select id="condition" class="form-control">
                                <option selected>Select Condition</option>
                                <option>New</option>
                                <option>Used</option>
                                <option>Reconditioned</option>
                            </select>
                        </p>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="manufacturer" class="col-sm-2 form-control-label">Manufacturer</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="text" id="manufacturer" class="form-control" placeholder="Manufacturer">
                        </p>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="model_number" class="col-sm-2 form-control-label">Model Number</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="text" id="model_number" class="form-control" placeholder="Model Number">
                        </p>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="identifier" class="col-sm-2 form-control-label">Product Identifier</label>
                    <div class="col-sm-8">
                        <p class="form-control-static">
                            <input type="text" id="identifier" class="form-control" placeholder="UPC/EAN/ISBN">
                        </p>
                    </div>
                    <div class="col-sm-2">
                        <p class="form-control-static">
                            <select class="form-control">
                                <option selected>Select Type</option>
                                <option>UPC</option>
                                <option>EAN</option>
                                <option>ISBN</option>
                            </select>
                        </p>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="msrp" class="col-sm-2 form-control-label">MSRP</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="text" id="msrp" class="form-control" placeholder="MSRP">
                        </p>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="price" class="col-sm-2 form-control-label">Price</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="text" id="price" class="form-control" placeholder="Price">
                        </p>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="weight" class="col-sm-2 form-control-label">Weight</label>
                    <div class="col-sm-8">
                        <p class="form-control-static">
                            <input type="text" id="weight" class="form-control" placeholder="Weight">
                        </p>
                    </div>
                    <div class="col-sm-2">
                        <p class="form-control-static">
                            <select class="form-control">
                                <option selected>Select Unit</option>
                                <option>Pounds</option>
                                <option>Kilograms</option>
                            </select>
                        </p>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Package Dimensions</label>
                    <div class="col-sm-2">
                        <p class="form-control-static">
                            <input type="text" class="form-control" placeholder="Dimensions">
                        </p>
                    </div>
                    <div class="col-sm-2">
                        <p class="form-control-static">
                            <input type="text" class="form-control" placeholder="Dimensions">
                        </p>
                    </div>
                    <div class="col-sm-2">
                        <p class="form-control-static">
                            <input type="text" class="form-control" placeholder="Dimensions">
                        </p>
                    </div>
                    <div class="col-sm-2">
                        <p class="form-control-static">
                            <select class="form-control">
                                <option selected>Select Unit</option>
                                <option>Inches</option>
                                <option>Centimeters</option>
                            </select>
                        </p>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="parent_sku" class="col-sm-2 form-control-label">Parent SKU</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="text" id="parent_sku" class="form-control" placeholder="Parent SKU">
                        </p>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-10 col-sm-offset-2">
                        <div class="checkbox">
                            <input type="checkbox" id="variations">
                            <label for="variations">Variations</label>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>
@endsection
