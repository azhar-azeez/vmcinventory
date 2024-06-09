<?php

namespace App\Livewire\Tables;

use App\Models\Order;
use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class OrderTable extends Component
{
    use WithPagination;

    public $perPage = 5;

    public $search = '';

    public $sortField = 'invoice_no';

    public $sortAsc = false;

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;

        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }
    public static function containsNumbers($input)
    {
        $numberPattern = '/\d/';
        return preg_match($numberPattern, $input);
    }
    public function render()
    {
        if (!$this->containsNumbers($this->search)) {

            $customerIds = Customer::where('name', 'like', "%{$this->search}%")->pluck('id')->toArray();

            $orders = Order::where("user_id", auth()->id())
                ->with(['customer', 'details'])
                ->whereIn('customer_id', $customerIds)
                ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                ->paginate($this->perPage);

            return view('livewire.tables.order-table', compact('orders'));
        } else {
            return view('livewire.tables.order-table', [
                'orders' => Order::where("user_id", auth()->id())
                    ->with(['customer', 'details'])
                    ->search($this->search)
                    ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                    ->paginate($this->perPage)
            ]);
        }

    }
}
