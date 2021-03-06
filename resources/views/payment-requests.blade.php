@extends('layouts.layout')
@section('title', 'Wallet')
@section('content')


	<div class="row page-titles">
        <div class="col-md-6 col-7 align-self-center">
            <h3 class="text-themecolor m-b-0 m-t-0">Wallet</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Payment Request</li>
            </ol>
        </div>

        <div class="col-md-6 col-5" align-self-center">
            <div class="card card-success card-inverse" style="float:right;">
                <div class="box text-center">
                <p class="text-white">Balance</p>
                    <h3 class="font-light text-white">&#8358; {{ $balance->balance }}</h3>
                </div>
            </div>
        </div>

    </div>

    <section class="">
        <div class="panel-heading">
            @include('common.errors')

            @if (Session::has('flash_message'))
            <div align="center" class="alert alert-danger alert-dismissable mw800 center-block">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true" color="blue">x</button>
                <strong>{{Session::get('flash_message')}}</strong>
            </div>
            @endif

            @if (Session::has('flash_message_success'))
            <div align="center" class="alert alert-success alert-dismissable mw800 center-block">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true" color="blue">x</button>
                <strong>{{Session::get('flash_message_success')}}</strong>
            </div>
            @endif
        </div>
    </section>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-body">
                <h4 class="card-title">Request Payment</h4>
                <h6 class="card-subtitle text-primary"> Please ensure you specify the right ESR Number and amount you want to request </h6>
                <form class="form-horizontal m-t-40" method="post" action="{{ url('payment/request')}}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <input class="form-control" type="text" name="receiver" placeholder="Receiver ESR Number" required="required" value="{{ old('receiver') }}">
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="text" name="amount" placeholder="Amount you are requesting" required="required" value="{{ old('amount') }}">
                    </div>

                    <div class="form-group">
                        <input class="form-control" type="text" name="description" placeholder="Description" required="required" value="{{ old('description') }}">
                    </div>

                    <div class="form-group m-b-0">
                        <div class="offset-sm-3 col-sm-9">
                            <button type="submit" class="btn btn-success waves-effect waves-light">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        
        <!-- Column -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex no-block">
                        <h4 class="card-title">Request History</h4>
                    </div>
                    <!-- <h6 class="card-subtitle">Check the monthly sales </h6> -->
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Receiver</th>
                                <th>Sender</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($transactions) < 1)
                            <tr>
                                <td colspan="6">
                                    <p class="text-center">Nothing to display</p>
                                </td>
                            </tr>
                            @else
                            @foreach($transactions as $key => $transaction)
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td class="txt-oflo">{{ $transaction->receiver }}</td>
                                <td class="txt-oflo">{{ $transaction->sender }}</td>
                                <td><span class="text-success">${{ $transaction->amount }}</span></td>
                                <td class="txt-oflo">{{ $transaction->description }}</td>
                                <td>
                                    <?php //$status = (($transaction->status == 0) ? 'Pending' : (($transaction->status == 1) ? 'Sent' : 'Failed')) ?>
                                    @if ($transaction->status == 0)
                                        <span class="label label-primary label-rouded">Pending</span>
                                    @endif
                                    @if ($transaction->status == 1)
                                        <span class="label label-success label-rouded">Sent</span>
                                    @endif
                                    @if ($transaction->status == 2)
                                        <span class="label label-danger label-rouded">Failed</span>
                                    @endif

                                </td>
                                <td class="txt-oflo">{{ date_create($transaction->created_at)->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                            {{ $transactions->links() }}
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Column -->
    </div>  

@endsection