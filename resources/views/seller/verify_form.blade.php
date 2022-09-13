@extends('seller.layouts.app')

@section('panel_content')
    <div class="aiz-titlebar mt-2 mb-4">
      <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Shop Verification')}}
                <a href="{{ route('shop.visit', $shop->slug) }}" class="btn btn-link btn-sm" target="_blank">({{ translate('Visit Shop')}})<i class="la la-external-link"></i>)</a>
            </h1>
        </div>
      </div>
    </div>
    <form class="" action="{{ route('seller.shop.verify.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0 h6">{{ translate('Verification info')}}</h4>
            </div>
            @php
                $verification_form = get_setting('verification_form');
            @endphp
            <div class="card-body">
                @foreach (json_decode($verification_form) as $key => $element)
                    @if ($element->type == 'text')
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ $element->label }} <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-md-10">
                                <input type="{{ $element->type }}" class="form-control mb-3" placeholder="{{ $element->label }}" name="element_{{ $key }}" required>
                            </div>
                        </div>
                    @elseif($element->type == 'file')
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ $element->label }}</label>
                            </div>
                            <div class="col-md-10">
                                <div class="custom-file">
                                    <label class="custom-file-label">
                                        <input type="{{ $element->type }}" name="element_{{ $key }}" id="file-{{ $key }}" class="custom-file-input" required>
                                        <span class="custom-file-name">{{ translate('Choose file') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    @elseif ($element->type == 'select' && is_array(json_decode($element->options)))
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ $element->label }}</label>
                            </div>
                            <div class="col-md-10">
                                <div class="mb-3">
                                    <select class="form-control selectpicker" data-minimum-results-for-search="Infinity" name="element_{{ $key }}" required>
                                        @foreach (json_decode($element->options) as $value)
                                            <option value="{{ $value }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @elseif ($element->type == 'multi_select' && is_array(json_decode($element->options)))
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ $element->label }}</label>
                            </div>
                            <div class="col-md-10">
                                <div class="mb-3">
                                    <select class="form-control selectpicker" data-minimum-results-for-search="Infinity" name="element_{{ $key }}[]" multiple required>
                                        @foreach (json_decode($element->options) as $value)
                                            <option value="{{ $value }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @elseif ($element->type == 'radio')
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ $element->label }}</label>
                            </div>
                            <div class="col-md-10">
                                <div class="mb-3">
                                    @foreach (json_decode($element->options) as $value)
                                        <div class="radio radio-inline">
                                            <input type="radio" name="element_{{ $key }}" value="{{ $value }}" id="{{ $value }}" required>
                                            <label for="{{ $value }}">{{ $value }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
                <div class="text-right mt-4">
                    <button type="submit" class="btn btn-primary">{{ translate('Apply')}}</button>
                </div>
            </div>
        </div>
    </form>
@endsection
