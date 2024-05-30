@extends('layouts.tabler')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">
                            {{ __('Rent Details') }}
                        </h3>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row row-cards mb-3">
                        <div class="col">
                            <label for="invoice_no" class="form-label required">
                                {{ __('Invoice No.') }}
                            </label>
                            <input type="text" id="invoice_no" class="form-control" value="{{ $rent->invoice_no }}"
                                disabled>
                        </div>

                        <div class="col">
                            <label for="rent_date" class="form-label required">
                                {{ __('Rent Date') }}
                            </label>
                            <input type="text" id="rent_date" class="form-control"
                                value="{{ $rent->rent_date->format('d-m-Y') }}" disabled>
                        </div>

                        <div class="col">
                            <label for="return_date" class="form-label required">
                                {{ __('Return Date') }}
                            </label>
                            <input type="text" id="rent_date" class="form-control"
                                value="{{ $rent->return_date->format('d-m-Y') }}" disabled>
                        </div>

                        <div class="col">
                            <label for="invoice_no" class="form-label required">
                                {{ __('Invoice No.') }}
                            </label>
                            <input type="text" id="invoice_no" class="form-control" value="{{ $rent->invoice_no }}"
                                disabled>
                        </div>

                        <div class="col">
                            <label for="customer" class="form-label required">
                                {{ __('Customer') }}
                            </label>
                            <input type="text" id="customer" class="form-control" value="{{ $rent->customer->name }}"
                                disabled>
                        </div>

                        <div class="col">
                            <label for="payment_type" class="form-label required">
                                {{ __('Payment Type') }}
                            </label>

                            <input type="text" id="payment_type" class="form-control" value="{{ $rent->payment_type }}"
                                disabled>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" class="align-middle text-center">No.</th>
                                    <th scope="col" class="align-middle text-center">Photo</th>
                                    <th scope="col" class="align-middle text-center">Product Name</th>
                                    <th scope="col" class="align-middle text-center">Days</th>
                                    <th scope="col" class="align-middle text-center">Quantity</th>
                                    <th scope="col" class="align-middle text-center">Price</th>
                                    <th scope="col" class="align-middle text-center">Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rent->details as $item)
                                    <tr>
                                        <td class="align-middle text-center">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="align-middle text-center">
                                            <div style="max-height: 80px; max-width: 80px;">
                                                <img class="img-fluid"
                                                    src="{{ $item->product->product_image ? asset('storage/' . $item->product->product_image) : asset('assets/img/products/default.webp') }}">
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            {{ $item->product->name }}
                                        </td>
                                        <td class="align-middle text-center">
                                            {{ $rent->days }}
                                        </td>
                                        <td class="align-middle text-center">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="align-middle text-center">
                                            {{ number_format($item->per_day_price, 2) }}
                                        </td>
                                        <td class="align-middle text-center">
                                            {{ number_format($item->total, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="6" class="text-end">
                                        Additional
                                    </td>
                                    <td class="text-center">{{ number_format($rent->additional_cost, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end">VAT</td>
                                    <td class="text-center">{{ number_format($rent->vat, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end">Total</td>
                                    <td class="text-center">LKR : {{ number_format($rent->total, 2) }}</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer text-end">

                </div>
            </div>

        </div>
    </div>
@endsection
