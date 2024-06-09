<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Order;
use Illuminate\Support\Collection;

class SearchProduct extends Component
{
    public $query;
    public $search_results;
    public $how_many;
    public $searchType;

    public function mount()
    {
        $this->query = '';
        $this->how_many = 5;
        $this->search_results = collect();
        $this->searchType = 'products';
    }

    public function render()
    {
        return view('livewire.search-entities');
    }

    public function updatedQuery()
    {
        switch ($this->searchType) {
            case 'products':
                $this->search_results = Product::where('user_id', auth()->id())
                    ->where(function ($query) {
                        $query->where('name', 'like', '%' . $this->query . '%')
                              ->orWhere('code', 'like', '%' . $this->query . '%');
                    })
                    ->take($this->how_many)
                    ->get();
                break;
                
            case 'purchases':
                $this->search_results = Purchase::whereHas('supplier', function ($query) {
                        $query->where('name', 'like', '%' . $this->query . '%');
                    })
                    ->take($this->how_many)
                    ->get();
                break;

            case 'orders':
                $this->search_results = Order::whereHas('customer', function ($query) {
                        $query->where('name', 'like', '%' . $this->query . '%');
                    })
                    ->take($this->how_many)
                    ->get();
                break;

            default:
                $this->search_results = collect();
                break;
        }
    }

    public function loadMore()
    {
        $this->how_many += 5;
        $this->updatedQuery();
    }

    public function resetQuery()
    {
        $this->query = '';
        $this->how_many = 5;
        $this->search_results = Collection::empty();
    }

    public function selectProduct($product)
    {
        $this->dispatch('productSelected', $product);
    }
}
