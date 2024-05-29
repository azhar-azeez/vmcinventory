@extends('layouts.tabler')

@section('content')
    <div class="page-body">
        <div class="container-xl">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row row-cards">
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h3 class="card-title">
                                    {{ __('New Rental') }}
                                </h3>
                            </div>
                            <div class="card-actions btn-actions">
                                <x-action.close route="{{ route('orders.index') }}" />
                            </div>
                        </div>
                        <form action="{{ route('invoice.create_rent') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row gx-3 mb-3">
                                    @include('partials.session')
                                    <div class="col-md-4">
                                        <label for="rent_date" class="small my-1">
                                            {{ __('Renting Date') }}
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input name="rent_date" id="rent_date" type="date"
                                            class="form-control example-date-input @error('rent_date') is-invalid @enderror"
                                            value="{{ old('rent_date') ?? now()->format('Y-m-d') }}" required>

                                        @error('rent_date')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>


                                    <div class="col-md-4">
                                        <label for="return_date" class="small my-1">
                                            {{ __('Return Date') }}
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input name="return_date" id="return_date" type="date"
                                            class="form-control example-date-input @error('return_date') is-invalid @enderror"
                                            value="{{ old('return_date') ?? now()->format('Y-m-d') }}" required>

                                        @error('return_date')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="small mb-1" for="customer_id">
                                            {{ __('Customer') }}
                                            <span class="text-danger">*</span>
                                        </label>

                                        <select
                                            class="form-select form-control-solid @error('customer_id') is-invalid @enderror"
                                            id="customer_id" name="customer_id">
                                            <option value="" disabled {{ old('customer_id') ? '' : 'selected' }}>
                                                Select a customer:</option>
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}"
                                                    {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->name }}</option>
                                            @endforeach
                                        </select>

                                        @error('customer_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="small mb-1" for='rent_type'>
                                            {{ __('Rent Type') }}
                                        </label>

                                        <select
                                            class="form-select form-control-solid @error('rent_type') is-invalid @enderror"
                                            id="rent_type" name="rent_type">
                                            <option value="" disabled {{ old('rent_type') ? '' : 'selected' }}>Select
                                                a type:</option>
                                            <option value="Monthly">Monthly</option>
                                            <option value="Daily">Daily</option>
                                        </select>

                                        @error('rent_type')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>
                                    <div class="col-md-4">
                                        <label for="additional_cost" class="small mb-1">{{ __('Additional (LKR)') }}</label>
                                        <input type="number" class="form-control @error('additional_cost') is-invalid @enderror" value="0" id="additional_cost" name="additional_cost">
                                    </div>
                                    @error('additional_cost')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                    <div class="col-md-4">
                                        <label class="small mb-1" for="reference">
                                            {{ __('Reference') }}
                                        </label>

                                        <input type="text" class="form-control" id="reference" name="reference"
                                            value="ORD" readonly>

                                        @error('reference')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered align-middle">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col">{{ __('Product') }}</th>
                                                <th scope="col" class="text-center">{{ __('Quantity') }}</th>
                                                <th scope="col" class="text-center">{{ __('Price') }}</th>
                                                <th scope="col" class="text-center">{{ __('SubTotal') }}</th>
                                                <th scope="col" class="text-center">
                                                    {{ __('Action') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($carts as $item)
                                                <tr>
                                                    <td>
                                                        {{ $item->name }}
                                                    </td>
                                                    <td style="min-width: 170px;">
                                                        <form></form>
                                                        <form action="{{ route('pos.updateCartItem', $item->rowId) }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="input-group">
                                                                <input type="number" class="form-control" name="qty"
                                                                    required value="{{ old('qty', $item->qty) }}">
                                                                <input type="hidden" class="form-control" name="product_id"
                                                                    value="{{ $item->id }}">

                                                                <div class="input-group-append text-center">
                                                                    <button type="submit"
                                                                        class="btn btn-icon btn-success border-none"
                                                                        data-toggle="tooltip" data-placement="top"
                                                                        title="" data-original-title="Sumbit">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            class="icon icon-tabler icon-tabler-check"
                                                                            width="24" height="24"
                                                                            viewBox="0 0 24 24" stroke-width="2"
                                                                            stroke="currentColor" fill="none"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z"
                                                                                fill="none" />
                                                                            <path d="M5 12l5 5l10 -10" />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $item->price }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $item->subtotal }}
                                                    </td>
                                                    <td class="text-center">
                                                        <form action="{{ route('pos.deleteCartItem', $item->rowId) }}"
                                                            method="POST">
                                                            @method('delete')
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-icon btn-outline-danger "
                                                                onclick="return confirm('Are you sure you want to delete this record?')">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    class="icon icon-tabler icon-tabler-trash"
                                                                    width="24" height="24" viewBox="0 0 24 24"
                                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                        fill="none" />
                                                                    <path d="M4 7l16 0" />
                                                                    <path d="M10 11l0 6" />
                                                                    <path d="M14 11l0 6" />
                                                                    <path
                                                                        d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <td colspan="5" class="text-center">
                                                    {{ __('Add Products') }}
                                                </td>
                                            @endforelse

                                            <tr>
                                                <td colspan="4" class="text-end">
                                                    Total Product
                                                </td>
                                                <td class="text-center">
                                                    {{ Cart::count() }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-end">Subtotal</td>
                                                <td class="text-center">
                                                    {{ Cart::subtotal() }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-end">Tax</td>
                                                <td class="text-center">
                                                    {{ Cart::tax(0) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-end">Total</td>
                                                <td class="text-center">
                                                    {{ Cart::total(0) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-end">Additional</td>
                                                <td class="text-center" id="additional_cost_cell">
                                                    {{ 0 }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                            <div class="card-footer text-end">
                                <button type="submit"
                                    class="btn btn-success add-list mx-1 {{ Cart::count() > 0 ? '' : 'disabled' }}">
                                    {{ __('Create Invoice') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>


                <div class="col-lg-5">
                    <div class="card mb-4 mb-xl-0">
                        <div class="card-header">
                            List Product
                        </div>
                        <div class="card-body">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered align-middle">
                                        <thead class="thead-light">
                                            <tr>
                                                {{-- - <th scope="col">No.</th> - --}}
                                                <th scope="col">Name</th>
                                                <th scope="col">Quantity</th>
                                                <th scope="col">Unit</th>
                                                <th scope="col">Price</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($products as $product)
                                                <tr>
                                                    {{-- -
                                            <td>
                                                <div style="max-height: 80px; max-width: 80px;">
                                                    <img class="img-fluid"  src="{{ $product->product_image ? asset('storage/products/'.$product->product_image) : asset('assets/img/products/default.webp') }}">
                                                </div>
                                            </td>
                                            - --}}
                                                    <td class="text-center">
                                                        {{ $product->name }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $product->quantity }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $product->unit->name }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ number_format($product->selling_price, 2) }}
                                                    </td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <form action="{{ route('pos.addCartItem', $product) }}"
                                                                method="POST">
                                                                @csrf
                                                                <input type="hidden" name="id"
                                                                    value="{{ $product->id }}">
                                                                <input type="hidden" name="name"
                                                                    value="{{ $product->name }}">
                                                                <input type="hidden" name="selling_price"
                                                                    value="{{ $product->selling_price }}">

                                                                <button type="submit"
                                                                    class="btn btn-icon btn-outline-primary">
                                                                    <x-icon.cart />
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <th colspan="6" class="text-center">
                                                        Data not found!
                                                    </th>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@pushonce('page-scripts')
    <script src="{{ asset('assets/js/img-preview.js') }}"></script>
    <script>
        // Add an event listener to the input field
        document.getElementById('additional_cost').addEventListener('input', function() {
            // Get the value entered in the input field
            var additionalCost = parseFloat(this.value);

            // Update the content of the table cell
            document.getElementById('additional_cost_cell').innerText = additionalCost.toFixed(
            2); // Adjust the number of decimal places as needed
        });
    </script>
    <script>
        // Your JavaScript code goes here
        $(document).ready(function() {
            // Store the selected values when the dropdowns change
            $('#customer_id').on('change', function() {
                var selectedCustomerId = $(this).val();
                // Store the selected customer ID in a variable or use it as needed
            });

            $('#rent_type').on('change', function() {
                var selectedRentType = $(this).val();
                // Store the selected rent type in a variable or use it as needed
            });

            // Repopulate the dropdowns with the stored values after the page change
            var selectedCustomerId = /* retrieve the stored customer ID */ ;
            $('#customer_id').val(selectedCustomerId);

            var selectedRentType = /* retrieve the stored rent type */ ;
            $('#rent_type').val(selectedRentType);
        });
    </script>
@endpushonce
