@extends('layouts.layout')
@section('title', 'Wallet')
@section('content')


	<div class="row page-titles">
        <div class="col-md-6 col-7 align-self-center">
            <h3 class="text-themecolor m-b-0 m-t-0">Wallet</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Transaction History</li>
            </ol>
        </div>

        <div class="col-md-6 col-5" align-self-center">
            <div class="card card-success card-inverse" style="float:right;">
                <div class="box text-center">
                <p class="text-white">Balance</p>
                    <h3 class="font-light text-white">${{ $balance->balance }}</h3>
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
        
        <!-- Column -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex no-block">
                        <h4 class="card-title">Transactions</h4>
                    </div>
                    <!-- <h6 class="card-subtitle">Check the monthly sales </h6> -->
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Amount</th>
                                <th>Previous Balance</th>
                                <th>Description</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($transactions) < 1)
                            <tr>
                                <td colspan="4">
                                    <p class="text-center">Nothing to display</p>
                                </td>
                            </tr>
                            @else
                            @foreach($transactions as $key => $transaction)
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td><span class="text-success">${{ $transaction->amount }}</span></td>
                                <td><span class="label label-primary label-rouded">${{ $transaction->previous_balance }}</span> </td>
                                <td class="txt-oflo">{{ $transaction->description }}</td>
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