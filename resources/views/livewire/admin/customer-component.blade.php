<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{__('Customers') }}</li>
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
                            <p class="card-title">{{ __('All').' '. __('Customers').' '. __('managment') }}</p>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-2 col-3">
                                    <input wire:model="paginate" type="number" class="form-control">

                                </div>
                                <div class="form-group col-md-2 col-6">
                                    <input wire:model="search" type="text" class="form-control" placeholder="Search by name">
                                </div>
                                <div class="form-group col-md-2 col-3">
                                    <input wire:click.prevent="generate_pdf" type="button" class="btn btn-info" value="{{__('PDF')}}">
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
                                                <a wire:click.prevent="activeStatus" class="dropdown-item" href="#">Mark as Active</a>
                                                <a wire:click.prevent="inactiveStatus" class="dropdown-item" href="#">Mark as Inactive</a>
                                            </div>
                                        </div>
                                        <span wire:loading wire:target="confirmRemoval, inactiveStatus, activeStatus" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    @endif
                                </div>
                            </div>
                            <div class="row table-responsive" wire:loading.delay.class="opacity-50" wire:target="paginate, search, FilterSerialize, selectall, selections">
                                <table class="table table-bordered table-striped text-sm">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" wire:model="selectall"></th>
                                        <th wire:click.prevent="FilterSerialize('name')">{{__('Name')}}</th>
                                        <th wire:click.prevent="FilterSerialize('due_amount')">{{__('Due')}}</th>
                                        <th wire:click.prevent="FilterSerialize('phone')">{{__('Phone')}}</th>
                                        <th>{{__('Purchases')}}</th>
                                        <th>{{__('Total')}} {{__('Price')}}</th>
                                        <th>{{__('Paid')}} {{__('Price')}}</th>
                                        <th>{{__('Total')}} {{__('Quantity')}}</th>
                                        <th>{{__('Total')}} {{__('KG')}}</th>
                                        <th wire:click.prevent="FilterSerialize('address')">{{__('Address')}}</th>
                                        <th wire:click.prevent="FilterSerialize('status')">{{__('Status')}}</th>
                                        <th>{{__('Action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($customers as $key=>$customer)
                                        <tr @if (is_array($selections)) @if(in_array($customer->id, $selections)) class="bg-secondary" @endif @endif wire:key="row-{{ $customer->id }}">
                                            <td><input type="checkbox" value="{{ $customer->id }}" wire:model="selections"></td>
                                            <td class="text-capitalize"><a href="{{route('dashboard.customer.payment', $customer->id)}}">{{ $customer->name }}</a></td>
                                            <td>{{ $customer->due_amount }}</td>
                                            <td><a href="tel:{{ $customer->phone }}">{{ $customer->phone }}</a></td>
                                            <td>{{ $customer->sells->count() }}</td>
                                            <td>{{ $customer->sells->sum('total_price') }}</td>
                                            <td>{{ $customer->sells->sum('paid_price') }}</td>
                                            <td>{{ $customer->sells->sum('quantity') }}</td>
                                            <td>{{ $customer->sells->sum('kg') }}</td>
                                            <td>{{ $customer->address }}</td>
                                            <td><span class="text-capitalize badge {{ $customer->status==='active'?'badge-success':'badge-danger' }}">{{ $customer->status }}</span></td>
                                            <td>
                                                <a wire:click.prevent="Edit({{ $customer->id }})"><i class="fa fa-edit text-pink"></i></a>
                                                <span wire:loading wire:target="Edit({{ $customer->id }})" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            </td>
                                        </tr>
                                    @empty
                                        <th class="text-center" colspan="12">{{__('No customer found')}}</th>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="justify-content-center items-center row">
                                <div class="col-12"></div>
                                {{ $customers->links() }}
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
            <form autocomplete="off" wire:submit.prevent="{{ $editmode ? 'update_customer' : 'create_customer' }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            @if($editmode)
                                <span>Edit customer</span>
                            @else
                                <span>Add New customer</span>
                            @endif
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                        <input type="text" wire:model.defer="state.name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter name">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Phone</label>
                            <input type="tel" wire:model.defer="state.phone" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Enter phone">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" wire:model.defer="state.email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter email">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" wire:model.defer="state.address" name="address" class="form-control @error('address') is-invalid @enderror"  placeholder="Enter address">
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times mr-1"></i> Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save mr-1"></i>
                            @if($editmode)<span>Save Changes</span>@else<span>Save</span>@endif
                            <span wire:loading wire:target="update_customer,create_customer" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

