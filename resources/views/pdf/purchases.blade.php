<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
        html {
            /*font-family: sans-serif;*/
            line-height: 1.15;
            margin: 0;
        }

        body {
            font-family: "nikosh", Nikosh, nikos, sans-serif;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: left;
            background-color: #fff;
            font-size: 10px;
            margin: 36pt;
        }

        h4 {
            margin-top: 0;
            margin-bottom: 0.5rem;
        }

        p {
            margin-top: 0;
            margin-bottom: 1rem;
        }

        strong {
            font-weight: bolder;
        }

        img {
            vertical-align: middle;
            border-style: none;
        }

        table {
            border-collapse: collapse;
        }

        th {
            text-align: inherit;
        }

        h4, .h4 {
            margin-bottom: 0.5rem;
            font-weight: 500;
            line-height: 1.2;
        }

        h4, .h4 {
            font-size: 1.5rem;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody + tbody {
            border-top: 2px solid #dee2e6;
        }

        .mt-5 {
            margin-top: 3rem !important;
        }

        .pr-0,
        .px-0 {
            padding-right: 0 !important;
        }

        .pl-0,
        .px-0 {
            padding-left: 0 !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-uppercase {
            text-transform: uppercase !important;
        }

        * {
            font-family: "DejaVu Sans";
        }

        body, h1, h2, h3, h4, h5, h6, table, th, tr, td, p, div {
            line-height: 1.1;
        }

        .party-header {
            font-size: 1.5rem;
            font-weight: 400;
        }

        .total-amount {
            font-size: 12px;
            font-weight: 700;
        }

        .border-0 {
            border: none !important;
        }
    </style>
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header" style="text-align: center">
                        <center>
                            <img src="{{$setup->logo}}" style="width: 77px; max-height: 77px; border-radius: 50%">
                            <h6><span>{{$setup->site_name}}</span></h6>
                            <div>Owner: <span>{{$setup->admin}}</span></div>
                            <div>Email: <a href="mailto:{{$setup->email}}">{{$setup->email}}</a></div>
                            <div>Phone: <a href="tel:{{$setup->phone}}">{{$setup->phone}}</a></div>
                            <h5 style="color: deepskyblue; padding-top: 5px;" class="card-title">All purchases</h5>
                        </center>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">

                        <div class="row table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th wire:click.prevent="FilterSerialize('product_id')">Product</th>
                                    <th wire:click.prevent="FilterSerialize('user_id')">Seller</th>
                                    <th wire:click.prevent="FilterSerialize('category_id')">Size</th>
                                    <th wire:click.prevent="FilterSerialize('quantity')">Quantity</th>
                                    <th wire:click.prevent="FilterSerialize('kg')">KG</th>
                                    <th wire:click.prevent="FilterSerialize('unit_price')">price</th>
                                    <th wire:click.prevent="FilterSerialize('total_price')">Total</th>
                                    <th wire:click.prevent="FilterSerialize('created_at')">Date</th>
                                    <th wire:click.prevent="FilterSerialize('status')">Status</th>
                                </tr>
                                </thead>
                                <div class="progress"><div class="progress-bar" style="width:{{@$data['progressBar']}}%"></div></div>
                                <tbody>
                                @forelse($purchases->reverse() as $key=>$purchase)
                                    <tr  wire:key="row-{{ $purchase->id }}">
                                        <td class="text-capitalize"><a href="{{route('dashboard.products')}}">{{ $purchase->product->name }}</a></td>
                                        <td class="text-capitalize"><a href="{{route('dashboard.sellers')}}">{{ $purchase->seller->name }}</a></td>
                                        <td class="text-capitalize"><a href="{{route('dashboard.categories')}}">{{ $purchase->category->name }}</a></td>
                                        <td class="text-capitalize">{{ $purchase->quantity }}</td>
                                        <td class="text-capitalize">{{ $purchase->kg }}</td>
                                        <td class="text-capitalize">{{ $purchase->unit_price }}</td>
                                        <td class="text-capitalize">{{ $purchase->total_price }}</td>
                                        <td class="text-capitalize">{{ \Carbon\Carbon::parse($purchase->created_at)->format('Y-m-d') }}</td>
                                        <td><span class="text-capitalize" style="{{ $purchase->status=='active'?'color:green':'color:red'}}">{{ $purchase ->status }}</span></td>
                                    </tr>
                                @empty
                                    <th class="text-center" colspan="9">No purchases found</th>
                                @endforelse
                                </tbody>
                            </table>
                            <h5>Signature:</h5>
                            <div>Date: <span>{{ date('d.m.Y H:i:s')}}</span></div>
                            <div>Location: <span>{{$setup->location}}</span></div>
                            <div>Website: <a href="{{$setup->site_url}}">{{$setup->site_url}}</a></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</body>
</html>