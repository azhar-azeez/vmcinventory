<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">
                {{ __('Rents') }}
            </h3>
        </div>

        <div class="card-actions">
            <x-action.create route="{{ route('rents.create') }}" />
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <div class="d-flex">
            <div class="text-secondary">
                Show
                <div class="mx-2 d-inline-block">
                    <select wire:model.live="perPage" class="form-select form-select-sm" aria-label="result per page">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                    </select>
                </div>
                entries
            </div>
            <div class="ms-auto text-secondary">
                Search:
                <div class="ms-2 d-inline-block">
                    <input type="text" wire:model.live="search" class="form-control form-control-sm"
                        aria-label="Search invoice">
                </div>
            </div>
        </div>
    </div>

    <x-spinner.loading-spinner />

    <div class="table-responsive">
        <table wire:loading.remove class="table table-bordered card-table table-vcenter text-nowrap datatable">
            <thead class="thead-light">
                <tr>
                    <th class="align-middle text-center w-1">
                        {{ __('No.') }}
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('invoice_no')" href="#" role="button">
                            {{ __('Invoice No.') }}
                            @include('inclues._sort-icon', ['field' => 'invoice_no'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('customer_id')" href="#" role="button">
                            {{ __('Customer') }}
                            @include('inclues._sort-icon', ['field' => 'customer_id'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('rent_date')" href="#" role="button">
                            {{ __('Rent Date') }}
                            @include('inclues._sort-icon', ['field' => 'rent_date'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('return_date')" href="#" role="button">
                            {{ __('Return Date') }}
                            @include('inclues._sort-icon', ['field' => 'return_date'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('total')" href="#" role="button">
                            {{ __('Total') }}
                            @include('inclues._sort-icon', ['field' => 'total'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('payment_type')" href="#" role="button">
                            {{ __('Pay Type') }}
                            @include('inclues._sort-icon', ['field' => 'payment_type'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        {{ __('Action') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rents as $rent)
                    <tr>
                        <td class="align-middle text-center">
                            {{ $loop->iteration }}
                        </td>
                        <td class="align-middle text-center">
                            {{ $rent->invoice_no }}
                        </td>
                        <td class="align-middle text-center">
                            {{ $rent->customer->name }}
                        </td>
                        <td class="align-middle text-center">
                            {{ $rent->rent_date->format('d-m-Y') }}
                        </td>
                        <td class="align-middle text-center">
                            {{ $rent->return_date->format('d-m-Y') }}
                        </td>
                        <td class="align-middle text-center">
                            {{ Number::currency($rent->total, 'LKR') }}
                        </td>
                        <td class="align-middle text-center">
                            {{ $rent->payment_type }}
                        </td>

                        <td class="align-middle text-center">
                            <x-button.show class="btn-icon" route="{{ route('rents.show', $rent->uuid) }}" />
                            <x-button.print class="btn-icon"
                                route="{{ route('rent.downloadInvoice', $rent->uuid) }}" />
                            <x-button.delete class="btn-icon" route="{{ route('rents.destroy', $rent->uuid) }}"
                                onclick="return confirm('Are you sure to cancel invoice no. {{ $rent->invoice_no }} ?')" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="align-middle text-center" colspan="8">
                            No results found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">
            Showing <span>{{ $rents->firstItem() }}</span> to <span>{{ $rents->lastItem() }}</span> of
            <span>{{ $rents->total() }}</span> entries
        </p>

        <ul class="pagination m-0 ms-auto">
            {{ $rents->links() }}
        </ul>
    </div>
</div>
