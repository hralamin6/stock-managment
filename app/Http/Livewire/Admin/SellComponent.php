<?php

namespace App\Http\Livewire\Admin;

use App\Models\Category;
use App\Models\PaidAmount;
use App\Models\Product;
use App\Models\sell;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class SellComponent extends Component
{
    use WithPagination;
    public $state = [], $sell, $editmode, $name, $orderBy='id', $serialize='desc', $paginate=10, $search='', $sellId, $selectall = false, $selections = [];
    protected $listeners = ['deleteConfirmed' => 'delete', 'sell_confirmed' => 'sell_confirmed'];

    protected $paginationTheme = 'bootstrap';
    public function addNew()
    {
        $this->reset();
        $this->showEditModal = false;
        $this->dispatchBrowserEvent('show-form', ['action'=>'show']);
    }
    public function create_sell()
    {
        $validatedData = Validator::make($this->state, [
            'product_id' => ['required'],
            'category_id' => ['required'],
            'user_id' => ['required'],
            'quantity' => ['required', 'numeric'],
            'kg' => ['required', 'numeric'],
            'unit_price' => ['required', 'numeric'],
        ])->validate();
        $validatedData['total_price'] = $validatedData['kg']*$validatedData['unit_price'];
        $validatedData['due_price'] = $validatedData['total_price'];
        $validatedData['paid_price'] = 0;
            sell::create($validatedData);
            $this->dispatchBrowserEvent('show-form', ['action'=>'hide']);
            $this->alert('success', 'Successfully inserted');
    }
    public function Edit(sell $sell)
    {
        $this->reset();
        $this->editmode = true;
        $this->sell = $sell;
        $this->state = $sell->toArray();
        $this->dispatchBrowserEvent('show-form', ['action'=>'show']);
    }

    public function update_sell()
    {
        $validatedData = Validator::make($this->state, [
            'product_id' => ['required'],
            'category_id' => ['required'],
            'user_id' => ['required'],
            'quantity' => ['required', 'numeric'],
            'kg' => ['required', 'numeric'],
            'unit_price' => ['required', 'numeric'],
        ])->validate();
        $validatedData['total_price'] = $validatedData['kg']*$validatedData['unit_price'];
        $validatedData['due_price'] = $validatedData['total_price'];
        $validatedData['paid_price'] = 0;
        $this->sell->update($validatedData);
            $this->dispatchBrowserEvent('show-form', ['action' => 'hide']);
            $this->alert('success', 'Successfully updated');
    }
    public function confirm_sell($id)
    {
        $this->sellId = $id;
        $this->dispatchBrowserEvent('show-sell-confirmation');
    }


    public function sell_confirmed()
    {
        $sell = sell::find($this->sellId);
        $product = $sell->product;
        $customer = $sell->customer;
        if ($product->stock_amount<$sell->kg) {
            $this->alert('error', 'You does not have enough stock');
        }else{
            if ($sell->status==='active'){
                $product->stock_amount += $sell->kg;
                $product->sell_amount -= $sell->kg;
                $customer->due_amount -= $sell->total_price;
                $sell->status = 'inactive';
            }else{
                $product->sell_amount += $sell->kg;
                $product->stock_amount -= $sell->kg;
                $customer->due_amount += $sell->total_price;
                $sell->status = 'active';
            }
            $product->save();
            $customer->save();
            $sell->save();
            $this->alert('success', 'Successfully sell completed');
            $this->dispatchBrowserEvent('selled', ['message' => 'Successfully sell completed!']);
        }

    }
    public function confirmRemoval()
    {
        $this->dispatchBrowserEvent('show-delete-confirmation');
    }
    public function delete()
    {
        $sell = sell::whereIn('id', $this->selections);
        $sell->delete();
        $this->dispatchBrowserEvent('deleted', ['message' => 'Appointment deleted successfully!']);
    }
    public function updatedSelectall($value)
    {
        if ($value){
            $this->selections = sell::where('created_at', 'like', '%'.$this->search.'%')->orderBy($this->orderBy, $this->serialize)->paginate($this->paginate)->pluck('id')->map(fn($id) =>(string) $id);
        }else{
            $this->selections = [];
        }
    }
    public function activeStatus()
    {
        foreach ($this->selections as $key => $selection) {
            $sell = sell::find($selection);
            $sell->status = 'active';
            $sell->save();
        }
        $this->alert('success', 'Successfully activated');
    }
    public function inactiveStatus()
    {
        foreach ($this->selections as $key => $selection) {
            $sell = sell::find($selection);
            $sell->status = 'inactive';
            $sell->save();
        }
        $this->alert('success', 'Successfully inactivated');
    }
    public function FilterSerialize($filtername)
    {
        $this->orderBy = $filtername;
        if ($this->serialize==='desc'){
            $this->serialize = 'asc';
        }else{
            $this->serialize = 'desc';
        }
    }
    public function render()
    {
        $categories = Category::whereStatus('active')->get();
        $customers = User::whereStatus('active')->whereType('customer')->get();
        $products = Product::whereStatus('active')->get();
        $sells = sell::when($this->search, function($query) {
            return $query->whereDate('created_at', '=', Carbon::parse($this->search)->format('Y-m-d'));
        })->orderBy($this->orderBy, $this->serialize)->paginate($this->paginate);
        return view('livewire.admin.sell-component', compact('sells', 'categories', 'customers', 'products'));
    }
}