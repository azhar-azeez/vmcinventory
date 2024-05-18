@extends('layouts.tabler')

@section('content')
<header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
    <div class="container-xl px-4">
        <div class="page-header-content pt-4">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto mt-4">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i class="fa-solid fa-box-full"></i></div>
                        Stock Report
                    </h1>
                </div>
            </div>

            @include('partials._breadcrumbs')
        </div>
    </div>
</header>

<div class="container-xl px-2 mt-n10">
    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    Stock Report Details
                </div>
                <div class="card-body">
                    <p>Click the button below to generate the stock report.</p>
                    <form action="{{ route('products.generateStockReport') }}" method="POST">
                        @csrf
                        <button class="btn btn-primary" type="submit">Generate Report</button>
                        <a class="btn btn-danger" href="{{ URL::previous() }}">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection