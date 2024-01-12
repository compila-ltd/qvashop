@extends('seller.layouts.app')

@section('panel_content')

    <div class="aiz-titlebar mt-2 mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('Add Your Product') }}</h1>
            </div>
        </div>
    </div>
    <form class="" action="{{route('seller.digitalproducts.store')}}" method="POST" enctype="multipart/form-data" id="choice_form">
        @csrf
		<input type="hidden" name="added_by" value="seller">

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('General')}}</h5>
            </div>

            <div class="card-body">
                <div class="form-group row">
                    <label class="col-lg-3 col-from-label">{{ translate('Product Name')}} <span class="text-danger">*</span></label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control" name="name" placeholder="{{ translate('Product Name')}}" required>
                    </div>
                </div>
                <div class="form-group row" id="category">
                    <label class="col-lg-3 col-from-label">{{ translate('Category')}} <span class="text-danger">*</span></label>
                    <div class="col-lg-9">
                        <select class="form-control aiz-selectpicker" name="category_id" id="category_id" required>
                            @foreach(\App\Models\Category::where('parent_id', 0)->where('digital', 1)->with('childrenCategories')->get(); as $category)
                                <option value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                                @foreach ($category->childrenCategories as $childCategory)
                                    @include('categories.child_category', ['child_category' => $childCategory])
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                </div>
                <!--
                <div class="form-group row">
                    <label class="col-lg-3 col-from-label">{{ translate('Product File')}} <span class="text-danger">*</span></label>
                    <div class="col-lg-9">
                        <div class="input-group" data-toggle="aizuploader" data-multiple="false">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="file" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>
                -->
                <div class="form-group row">
                    <label class="col-lg-3 col-from-label">{{ translate('Tags')}} <span class="text-danger">*</span></label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control aiz-tag-input" name="tags[]" placeholder="{{ translate('Type and hit enter') }}">
                        <small class="text-muted">{{ translate('This is used for search. Input those words by which cutomer can find this product.')}}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Images')}}</h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label" for="signinSrEmail">{{ translate('Gallery Images')}} <small>(600x600)</small></label>
                    <div class="col-lg-9">
                        <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="photos" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                        <small class="text-muted">{{ translate('These images are visible in product details page gallery. Use 600x600 sizes images.')}}</small>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 col-form-label" for="signinSrEmail">{{ translate('Thumbnail Image')}} <small>(300x300)</small></label>
                    <div class="col-lg-9">
                        <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="false">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="thumbnail_img" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                        <small class="text-muted">{{ translate('This image is visible in all product box. Use 300x300 sizes image. Keep some blank space around main object of your image as we had to crop some edge in different devices to make it responsive.')}}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Meta Tags')}}</h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-lg-3 col-from-label">{{ translate('Meta Title')}}</label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control" name="meta_title" placeholder="{{ translate('Meta Title')}}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-from-label">{{ translate('Description')}}</label>
                    <div class="col-lg-9">
                        <textarea name="meta_description" rows="5" class="form-control"></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label" for="signinSrEmail">{{ translate('Meta Image') }}</label>
                    <div class="col-lg-9">
                        <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="false">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="meta_img" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Price')}}</h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-lg-3 col-from-label">{{ translate('Unit price')}} <span class="text-danger">*</span></label>
                    <div class="col-lg-9">
                        <input type="number" lang="en" min="0" value="0" step="0.01" placeholder="{{ translate('Unit price')}}" name="unit_price" class="form-control" required>
                    </div>
                </div>
                <!--
                <div class="form-group row">
                    <label class="col-lg-3 col-from-label">{{ translate('Purchase price')}} <span class="text-danger">*</span></label>
                    <div class="col-lg-9">
                        <input type="number" lang="en" min="0" value="0" step="0.01" placeholder="{{ translate('Purchase price')}}" name="purchase_price" class="form-control" required>
                    </div>
                </div>
                -->
                @foreach (\App\Models\Tax::where('tax_status', 1)->get() as $tax)
                <div class="form-group row">
                    <label class="col-lg-3 col-from-label">
                        {{ $tax->name }}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-6">
                        <input type="hidden" value="{{$tax->id}}" name="tax_id[]">
                        <input type="number" lang="en" min="0" value="0" step="0.01" placeholder="{{ translate('Tax')}}" name="tax[]" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control aiz-selectpicker" name="tax_type[]">
                            <option value="amount">{{ translate('Flat')}}</option>
                            <option value="percent">{{ translate('Percent')}}</option>
                        </select>
                    </div>
                </div>
                @endforeach
                <div class="form-group row">
                    <label class="col-lg-3 col-from-label">{{ translate('Discount')}} <span class="text-danger">*</span></label>
                    <div class="col-lg-6">
                        <input type="number" lang="en" min="0" value="0" step="0.01" placeholder="{{ translate('Discount')}}" name="discount" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control aiz-selectpicker" name="discount_type">
                            <option value="amount">{{ translate('Flat')}}</option>
                            <option value="percent">{{ translate('Percent')}}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Product Variation')}}</h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-3">
                        <input type="text" class="form-control" value="{{ translate('Colors')}}" disabled>
                    </div>
                    <div class="col-md-8">
                        <select class="form-control aiz-selectpicker" data-live-search="true" name="colors[]"
                            data-selected-text-format="count" id="colors" multiple disabled>
                            @foreach (\App\Models\Color::orderBy('name', 'asc')->get() as $key => $color)
                            <option value="{{ $color->code }}"
                                data-content="<span><span class='size-15px d-inline-block mr-2 rounded border' style='background:{{ $color->code }}'></span><span>{{ $color->name }}</span></span>">
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="aiz-switch aiz-switch-success mb-0">
                            <input value="1" type="checkbox" name="colors_active">
                            <span></span>
                        </label>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <input type="text" class="form-control" value="{{ translate('Attributes')}}" disabled>
                    </div>
                    <div class="col-md-8">
                        <select name="choice_attributes[]" id="choice_attributes"
                            class="form-control aiz-selectpicker" data-live-search="true"
                            data-selected-text-format="count" multiple
                            data-placeholder="{{ translate('Choose Attributes') }}">
                            @foreach (\App\Models\Attribute::all() as $key => $attribute)
                            <option value="{{ $attribute->id }}">{{ $attribute->getTranslation('name') }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <p>{{ translate('Choose the attributes of this product and then input values of each attribute') }}
                    </p>
                    <br>
                </div>

                <div class="customer_choice_options" id="customer_choice_options">

                </div>
            </div>

            <div class="sku_combination" id="sku_combination">

            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Product Information')}}</h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-lg-3 col-from-label">{{ translate('Description')}}</label>
                    <div class="col-lg-9">
                        <textarea class="aiz-text-editor" name="description"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group mb-0 text-right mb-2">
            <button type="submit" class="btn btn-primary">{{ translate('Save Product')}}</button>
        </div>
    </form>

@endsection

@section('script')
<script type="text/javascript">

function add_more_customer_choice_option(i, name){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:"POST",
                url:'{{ route('seller.products.add-more-choice-option') }}',
                data:{
                attribute_id: i
                },
                success: function(data) {
                    var obj = JSON.parse(data);
                    $('#customer_choice_options').append('\
                    <div class="form-group row">\
                        <div class="col-md-3">\
                            <input type="hidden" name="choice_no[]" value="'+i+'">\
                            <input type="text" class="form-control" name="choice[]" value="'+name+'" placeholder="{{ translate('Choice Title') }}" readonly>\
                        </div>\
                        <div class="col-md-8">\
                            <select class="form-control aiz-selectpicker attribute_choice" data-live-search="true" name="choice_options_'+ i +'[]" multiple>\
                                '+obj+'\
                            </select>\
                        </div>\
                    </div>');
                    AIZ.plugins.bootstrapSelect('refresh');
            }
        });


        }

        $('input[name="colors_active"]').on('change', function() {
            if(!$('input[name="colors_active"]').is(':checked')){
                $('#colors').prop('disabled', true);
                AIZ.plugins.bootstrapSelect('refresh');
            }
            else{
                $('#colors').prop('disabled', false);
                AIZ.plugins.bootstrapSelect('refresh');
            }
            update_sku();
        });

        $(document).on("change", ".attribute_choice",function() {
            update_sku();
        });

        $('#colors').on('change', function() {
            update_sku();
        });

        $('input[name="unit_price"]').on('keyup', function() {
            update_sku();
        });

        $('input[name="name"]').on('keyup', function() {
            update_sku();
        });

        function delete_row(em){
            $(em).closest('.form-group row').remove();
            update_sku();
        }

        function delete_variant(em){
            $(em).closest('.variant').remove();
        }

        function update_sku(){
            $.ajax({
               type:"POST",
               url:'{{ route('seller.products.sku_combination') }}',
               data:$('#choice_form').serialize(),
               success: function(data){
                   $('#sku_combination').html(data);
                    AIZ.plugins.fooTable();
                   if (data.length > 1) {
                       $('#show-hide-div').hide();
                   }
                   else {
                        $('#show-hide-div').show();
                   }
               }
           });
        }

        $('#choice_attributes').on('change', function() {
            $('#customer_choice_options').html(null);
            $.each($("#choice_attributes option:selected"), function(){
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
            update_sku();
        });

</script>
    
@endsection