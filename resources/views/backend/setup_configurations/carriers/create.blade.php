@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('Add New Carrier') }}</h1>
            </div>
            <div class="col-md-6 text-md-right">
                <a href="{{ route('carriers.index') }}" class="btn btn-primary">
                    <span>{{ translate('Back') }}</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Carrier Information') }}</h5>
                </div>
                <div class="card-body">
                    <form id="carrier-form">

                        <div class="alert alert-danger print-error-msg" style="display:none">
                            <ul class="m-0"></ul>
                        </div>

                        @csrf
                        <div class="form-group row">
                            <label class="col-md-2 col-from-label">{{ translate('Carrier Name') }} <span
                                    class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="carrier_name"
                                    placeholder="{{ translate('Carrier Name') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 col-from-label">{{ translate('Transit Time') }} <span
                                    class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="transit_time"
                                    placeholder="{{ translate('Transit Time') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 col-from-label">{{ translate('Logo') }} </label>
                            <div class="col-md-9">
                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">
                                            {{ translate('Browse') }}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="logo" class="selected-files" value="">
                                </div>
                                <div class="file-preview box sm">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 col-from-label">{{ translate('Free Shipping') }} ? </label>
                            <div class="col-md-9">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="shipping_type" onchange="freeShipping(this)">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row" id="billing_type_section">
                            <label class="col-md-2 col-from-label">{{ translate('Billing Type') }} <span
                                    class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <select class="form-control aiz-selectpicker" name="billing_type"
                                    onchange="update_price_range_form()" id="billing_type" data-live-search="true">
                                    <option value="weight_based">{{ translate('According to Weight') }}</option>
                                    <option value="price_based">{{ translate('According to Price') }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- carrier Price Range form --}}
                        <div id="price_range_form">
                            <div class="mb-2 pl-0">
                                <h3 class="h6 carrier_range_form_header_text"></h3>
                            </div>
                            <hr>
                            <table id="price-range-table" class="table table-responsive mb-0">
                                <tbody>
                                    <tr style="background-color: #c9c9d4">
                                        <td class="price_range_text"></td>

                                        <td> >= </td>
                                        <td>
                                            <div class="input-group mb-2">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text bill_based_on"></div>
                                                </div>
                                                <input type="number" class="form-control delimiter1" name="delimiter1[]"
                                                    value="0.00" step="0.01">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr style="background-color: #c9c9d1">
                                        <td class="price_range_text"></td>
                                        <td>
                                            < </td>
                                        <td>
                                            <div class="input-group mb-2">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text bill_based_on"></div>
                                                </div>
                                                <input type="number" class="form-control delimiter2" name="delimiter2[]"
                                                    value="0.00" step="0.01">
                                            </div>
                                        </td>
                                    </tr>

                                    @foreach ($zones as $zone)
                                        <tr>
                                            <td>
                                                <span class="mt-2">{{ $zone->name }}</span>
                                            </td>
                                            <td>
                                                <input class="aiz-square-check zone_enable mt-2" type="checkbox"
                                                    name="zones[]" value="{{ $zone->id }}">
                                            </td>
                                            <td>
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">$</div>
                                                    </div>
                                                    <input type="number" class="form-control shipping_cost"
                                                        name="carrier_price[{{ $zone->id }}][]"
                                                        placeholder="{{ translate('cost') }}" disabled required>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    
                                </tbody>

                            </table>

                            <button type="button" class="btn btn-primary btn-sm" id="addNewRange">
                                {{ translate('Add new range') }}
                            </button>
                        </div>


                        <div class="form-group mb-0 text-right">
                            <button type="button" class="btn btn-primary" id="carrier-submit-btn">
                                {{ translate('Save') }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            update_price_range_form();
        });

        function freeShipping(el) {
            if (el.checked) {
                $("#billing_type_section").hide();
                $("#price_range_form").hide();
            } else {
                $("#billing_type_section").show();
                $("#price_range_form").show();
            }
        }

        // update price range form data based on billing type
        function update_price_range_form() {
            var billing_type = $('#billing_type').val();

            $(".carrier_range_form_header_text").html(billing_type === 'weight_based' ?
                "{{ translate('Weight based carrier price range') }}" :
                "{{ translate('Price based carrier price range') }}");
            $(".price_range_text").html(billing_type === 'weight_based' ?
                "{{ translate('Will be applied when the weight is') }}" :
                "{{ translate('Will be applied when the price is') }}");
            $(".bill_based_on").html(billing_type === 'weight_based' ? "{{ translate('kg') }}" : "$");

        }

        // disabled untill check
        $(document).on("change", ".zone_enable", function() {
            $(this).closest("tr").find('.shipping_cost').prop("disabled", !this.checked);
        });


        $(document).on("click", "#addNewRange", function() {
            //table body
            var tablebody = $("#price-range-table").find("tbody");
            var tdlenght = tablebody.find("tr td").length;
            // console.log(tdlenght);


            // last td input 
            var first_lasttd = $("#price-range-table").find("tr:nth-child(1)").find("td:last").find("input").val();
            var second_lasttd = $("#price-range-table").find("tr:nth-child(2)").find("td:last").find("input").val();

            if ((second_lasttd == 0) || (second_lasttd == first_lasttd) ||
                ((second_lasttd - first_lasttd) < 0)) {
                alert('Please validate the last range before creating a new one.')
            } else {
                // clonning last tds
                fnclone(tablebody, second_lasttd);
            }

        });

        // last td remove
        $(document).on("click", ".delete-range", function() {
            var iIndex = $(this).closest("td").prevAll("td").length;
            $(this).parents("#price-range-table").find("tr").each(function() {
                $(this).find("td:eq(" + iIndex + ")").remove();
            });
        });


        // last td clone function
        function fnclone(tablebody, second_lasttd) {
            tablebody.find("td:nth-last-child(1)").each(function() {
                $(this).clone()
                    .find("input").val("").end()
                    .insertAfter(this);
            });

            $('#price-range-table tr:last td:last').html(
                '<button type="button" id="disablebtn" class="btn btn-primary btn-sm delete-range">Delete</button>');

            var first_lasttd = $("#price-range-table").find("tr:nth-child(1)").find("td:last").find("input");
            first_lasttd.val(parseFloat(second_lasttd).toFixed(2));
        }

        $("#carrier-submit-btn").click(function() {
            var data = new FormData($('#carrier-form')[0]);
            var delimiter1 = $('.delimiter1');
            var delimiter2 = $('.delimiter2');

            for (let i = 0; i < delimiter1.length; i++) {
                if (delimiter1[i].value && delimiter2[i].value) {
                    if (parseFloat(delimiter1[i].value) >= parseFloat(delimiter2[i].value)) {
                        alert('Please put the last range greater than first range.');
                        delimiter2[i].focus();
                        return false
                    }
                    if (i>0 && (parseFloat(delimiter1[i].value) != parseFloat(delimiter2[(i-1)].value))) {
                        alert('Please put the first range equal to the previous last range.');
                        delimiter1[(i)].focus();
                        return false
                    }
                }
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: "{{ route('carriers.store') }}",
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data, textStatus, jqXHR) {

                }
            }).done(function(data) {
                window.location.replace("{{ route('carriers.index') }}");
            }).fail(function(jqXHR, textStatus, errorThrown) {
                $(".print-error-msg").find("ul").html('');
                $(".print-error-msg").css('display', 'block');
                $.each(jqXHR.responseJSON.errors, function(key, value) {
                    $(".print-error-msg").find("ul").append('<li>' + value[0] + '</li>');
                });
                
                $("html, body").animate({scrollTop: 0}, 800);
            });
        })
    </script>
@endsection
