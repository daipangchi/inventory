{
    // const $page = $('.page-inventory-create');
    const $productVariationsInputContainer = $('#product-variations-input-container');
    const $productVariationsContainer = $('#product-variations-container');
    let index = 1;

    disableSubmitOnEnter('.attribute-key');

    // Show variations form if checked
    $('#variations').click(function () {
        if ($(this).prop('checked')) {
            $productVariationsInputContainer.show();
        }
        else {
            $productVariationsInputContainer.hide();
            $productVariationsContainer.hide();
        }
    });

    // Create new attribute inputs for product variations
    $('#add-new-attribute').click(function (e) {
        e.preventDefault();
        $('.variations-container').append(compileHbs('#attribute-value-inputs-template', {index}));
        $('[data-role="tagsinput"]').tagsinput();
        disableSubmitOnEnter('.attribute-key');

        index++;
    });

    // Generate variations
    $('#generate-variations').click(function () {
        let html = $('#product-variations-template').html();
        let parentSku = $('input[name=sku]').val();
        let attributes = {};
        let keys = [];

        $('[data-role="tagsinput"]').each(function () {
            let $this = $(this);
            let key = $this.closest('.attribute').find('.attribute-key').val();
            let items = $(this).tagsinput('items');

            if (key === '' || !items.length) return;

            keys.push(key);
            attributes[key] = items;
        });

        if (!keys.length) return;

        $productVariationsInputContainer.hide();
        $productVariationsContainer.show();

        keys.unshift('sku');
        keys.push('quantity');

        let variations = getAllCombinations(attributes);

        variations.forEach(function (variation, index) {
            variation.sku = parentSku + '-' + (index + 1);
            variation.quantity = 50;
        });

        $productVariationsContainer.children().remove('table');
        $productVariationsContainer.append(Handlebars.compile(html)({variations, keys}));
    });

    $('input[name="parent_sku"]').on('keyup change', function (e) {
        if ($(this).val().length > 0) {
            $('#variations').closest('.form-group').addClass('hidden');
            $('#product-variations-container').addClass('hidden');
        }
        else {
            $('#variations').closest('.form-group').removeClass('hidden');
            $('#product-variations-container').removeClass('hidden');
        }
    });

    $('#category1').change(function () {
        if (this.value) {
            $.ajax({
                url: '/categories',
                data: {parent_id: this.value}
            }).done(function (response) {
                var html = '<option value="">Select a Subcategory</option>';
                var categories = response.data;

                if (response.data.length) {
                    for (var i = 0; i < categories.length; i++) {
                        html += `<option value="${categories[i].id}">${categories[i].name}</option>`
                    }

                    html = `<select id="category2" class="form-control" name="categories[]">${html}</select>`;
                    html = `<div class="col-sm-2"><p class="form-control-static">${html}</p></div>`;
                }

                $('#category2').closest('.col-sm-2').remove();
                $('#category3').closest('.col-sm-2').remove();

                // To prevent stuttering, we will move this after.
                if (response.data.length) {
                    $('.categories-row').append(html);
                }
            });
        }
        else {
            $('#category2').closest('.col-sm-2').remove();
            $('#category3').closest('.col-sm-2').remove();
        }
    });

    $('.categories-row').on('change', '#category2', function () {
        if (this.value) {
            $.ajax({
                url: '/categories',
                data: {parent_id: this.value}
            }).done(function (response) {
                var html = '<option value="">Select a Subcategory</option>';
                var categories = response.data;

                if (response.data.length) {
                    for (var i = 0; i < categories.length; i++) {
                        html += `<option value="${categories[i].id}">${categories[i].name}</option>`
                    }
                }

                html = `<select id="category3" class="form-control" name="categories[]">${html}</select>`;
                html = `<div class="col-sm-2"><p class="form-control-static">${html}</p></div>`;

                $('#category3').closest('.col-sm-2').remove();

                // To prevent stuttering, we will move this after.
                if (response.data.length) {
                    $('.categories-row').append(html);
                }
            });
        }
        else {
            $('#category3').closest('.col-sm-2').remove();
        }
    });

    function disableSubmitOnEnter(selector) {
        $(selector).keypress(function (e) {
            if (e.keyCode === 13) {
                e.preventDefault();

                $(this).closest('.attribute').find('.bootstrap-tagsinput input').focus();
            }
        });
    }

    function getProducts(arrays) {
        if (arrays.length === 0) {
            return [[]];
        }

        let results = [];

        getProducts(arrays.slice(1)).forEach((product) => {
            arrays[0].forEach((value) => {
                results.push([value].concat(product));
            });
        });

        return results;
    }

    function getAllCombinations(attributes) {
        let attributeNames = Object.keys(attributes);
        let attributeValues = attributeNames.map((name) => attributes[name]);

        return getProducts(attributeValues).map((product) => {
            let obj = {};

            attributeNames.forEach((name, i) => {
                obj[name] = product[i];
            });

            return obj;
        });
    }
}
