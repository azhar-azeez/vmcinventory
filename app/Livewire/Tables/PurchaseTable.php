<?php

namespace App\Livewire\Tables;

use Livewire\Component;
use App\Models\Purchase;
use App\Models\Supplier;
use Livewire\WithPagination;

class PurchaseTable extends Component
{
    use WithPagination;

    public $perPage = 5;

    public $search = '';

    public $sortField = 'purchase_no';

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

            $supplierIds = Supplier::where('name', 'like', "%{$this->search}%")->pluck('id')->toArray();

            $purchases = Purchase::where("user_id", auth()->id())
                ->with('supplier')
                ->whereIn('supplier_id', $supplierIds)
                ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                ->paginate($this->perPage);

            return view('livewire.tables.purchase-table', compact('purchases'));

        } else {
            return view('livewire.tables.purchase-table', [
                'purchases' => Purchase::where("user_id", auth()->id())
                    ->with('supplier')
                    ->search($this->search)
                    ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                    ->paginate($this->perPage)
            ]);
        }

    }
}
