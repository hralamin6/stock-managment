<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{__('Sells') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <div class="card-header">
                            <button  wire:click.prevent="addNew" class="btn btn-primary float-right"><i class="fa fa-plus-circle mr-1"></i>{{__('Add')}}
                                <span wire:loading wire:target="addNew" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </button>
                            <p class="card-title">{{ __('All').' '. __('Sell').' '. __('managment') }}</p>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-2 col-3">
                                    <input wire:model="paginate" type="number" class="form-control">

                                </div>
                                <div class="form-group col-md-2 col-8">
                                    <input wire:model="startDate" type="date" class="form-control">
                                </div> {{__('to')}}
                                <div class="form-group col-md-2 col-8">
                                    <input wire:model="endDate" type="date" class="form-control">
                                </div>
                                <div class="form-group col-md-2 col-3">
                                    <input wire:click.prevent="generate_pdf" type="button" class="btn btn-info" value=" {{__('PDF')}}">
                                    <span wire:loading wire:target="generate_pdf" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                </div>
                                <div class="form-group col-md-2 col-12 float-right">
                                    @if($selections)
                                        <div class="btn-group ml-2">
                                            <button type="button" class="btn btn-default">Bulk Actions</button>
                                            <button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <div class="dropdown-menu" role="menu">
                                                <a wire:click.prevent="confirmRemoval" class="dropdown-item" href="#">Delete Selected</a>
{{--                                                <a wire:click.prevent="activeStatus" class="dropdown-item" href="#">Mark as Active</a>--}}
{{--                                                <a wire:click.prevent="inactiveStatus" class="dropdown-item" href="#">Mark as Inactive</a>--}}
                                            </div>
                                        </div>
                                        <span wire:loading wire:target="confirmRemoval, inactiveStatus, activeStatus" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    @endif
                                    @error('selections')<div class="invalid-feedback">{{ $message }}</div>@enderror

                                </div>
                            </div>
                            <div class="row table-responsive" wire:loading.delay.class="opacity-50" wire:target="paginate, search, FilterSerialize, selectall, selections">
                                <table class="table table-bordered table-striped text-sm">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" wire:model="selectall"></th>
                                        <th wire:click.prevent="FilterSerialize('product_id')">{{ __('Product')}}</th>
                                        <th wire:click.prevent="FilterSerialize('user_id')">{{ __('Customer')}}</th>
                                        <th wire:click.prevent="FilterSerialize('category_id')">{{ __('Size')}}</th>
                                        <th wire:click.prevent="FilterSerialize('quantity')">{{ __('Quantity')}}</th>
                                        <th wire:click.prevent="FilterSerialize('kg')">{{ __('KG')}}</th>
                                        <th wire:click.prevent="FilterSerialize('unit_price')">{{ __('Price')}}</th>
                                        <th wire:click.prevent="FilterSerialize('total_price')">{{ __('Total')}}</th>
                                        <th wire:click.prevent="FilterSerialize('paid_price')">{{ __('Paid')}}</th>
                                        <th wire:click.prevent="FilterSerialize('due_price')">{{ __('Due')}}</th>
                                        <th wire:click.prevent="FilterSerialize('created_at')">{{ __('Date')}}</th>
                                        <th wire:click.prevent="FilterSerialize('status')">{{ __('Status')}}</th>
                                        <th wire:click.prevent="FilterSerialize('price_status')">{{ __('Paid')}} {{ __('Status')}}</th>
                                        <th>{{__('Action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($sells as $key=>$sell)
                                        <tr @if (is_array($selections)) @if(in_array($sell->id, $selections)) class="bg-secondary" @endif @endif wire:key="row-{{ $sell->id }}">
                                            <td><input type="checkbox" value="{{ $sell->id }}" wire:model="selections"></td>
                                            <td class="text-capitalize"><a href="{{route('dashboard.products')}}">{{ $sell->product->name }}</a></td>
                                            <td class="text-capitalize"><a href="{{route('dashboard.customers')}}">{{ $sell->customer->name }}</a></td>
                                            <td class="text-capitalize"><a href="{{route('dashboard.categories')}}">{{ $sell->category->name }}</a></td>
                                            <td class="text-capitalize">{{ $sell->quantity }}</td>
                                            <td class="text-capitalize">{{ $sell->kg }}</td>
                                            <td class="text-capitalize">{{ $sell->unit_price }}</td>
                                            <td class="text-capitalize">{{ $sell->total_price }}</td>
                                            <td class="text-capitalize">{{ $sell->paid_price }}</td>
                                            <td class="text-capitalize">{{ $sell->due_price }}</td>
                                            <td class="text-capitalize">{{ \Carbon\Carbon::parse($sell->created_at)->format('Y-m-d') }}</td>
                                            <td>
                                                <a class="text-capitalize badge {{ $sell->status==='active'?'badge-success':'badge-danger' }}" wire:click.prevent="confirm_sell({{ $sell->id }})">{{ $sell->status }}</a>
                                            </td>
                                            <td>
                                                <span class="text-capitalize badge @if($sell->status==='fullpaid') badge-success @elseif($sell->status==='subpaid') badge-warning @else badge-danger @endif ">{{ $sell->price_status }}</span>
                                            </td>
                                            <td>
                                                @if($sell->status==='inactive')
                                                <a wire:click.prevent="Edit({{ $sell->id }})"><i class="fa fa-edit text-pink"></i></a>
                                                    <span wire:loading wire:target="Edit({{ $sell->id }})" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <th class="text-center" colspan="14">{{__('No sell found')}}</th>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="justify-content-center items-center row">
                                <div class="col-12"></div>
                                {{ $sells->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <form autocomplete="off" wire:submit.prevent="{{ $editmode ? 'update_sell' : 'create_sell' }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            @if($editmode)
                                <span>Edit sell</span>
                            @else
                                <span>Add New sell</span>
                            @endif
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Customer</label>
                            <select type="text" wire:model.defer="state.user_id" class="form-control @error('user_id') is-invalid @enderror">
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="name">Size</label>
                            <select type="text" wire:model.defer="state.category_id" class="form-control @error('category_id') is-invalid @enderror">
                                <option value="">Select Size</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="name">Product</label>
                            <select type="text" wire:model.defer="state.product_id" class="form-control @error('product_id') is-invalid @enderror">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="text" wire:model.defer="state.quantity" name="quantity" class="form-control @error('quantity') is-invalid @enderror" placeholder="Enter quantity">
                            @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="kg">Kg</label>
                            <input type="text" wire:model.defer="state.kg" name="kg" class="form-control @error('kg') is-invalid @enderror" placeholder="Enter kg">
                            @error('kg')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="unit_price">Unit Price</label>
                            <input type="text" wire:model.defer="state.unit_price" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror" placeholder="Enter unit_price">
                            @error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times mr-1"></i> Cancel</button>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save mr-1"></i>
                                @if($editmode)<span>Save Changes</span>@else<span>Save</span>@endif
                                <span wire:loading wire:target="update_sell,create_sell" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>

                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

@push('js')
    <script>
        window.addEventListener('show-sell-confirmation', event => {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, do  it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.emit('sell_confirmed')
                }
            })
        })

        window.addEventListener('selled', event => {
            Swal.fire(
                'updated!',
                event.detail.message,
                'success'
            )
        })
    </script>
@endpush
