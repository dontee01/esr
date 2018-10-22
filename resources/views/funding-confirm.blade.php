@extends('layouts.layout')
@section('title', 'Wallet')
@section('content')


	<div class="row page-titles">
        <div class="col-md-6 col-7 align-self-center">
            <h3 class="text-themecolor m-b-0 m-t-0">Fund Wallet</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Confirm Transaction</li>
            </ol>
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
                <h4 class="card-title">Fund Wallet</h4>
                <h6 class="card-subtitle"> Minimum deposit amount is $50 </h6>
                <form class="form-horizontal m-t-40" method="post" action="{{ url('pay')}}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <!-- <label>Teller Number</label> -->
                        <p>
                            You are about to pay: <span>Amount : &#8358;{{ number_format(($amount / 100), 2) }}</span>
                        </p>
                                    
                        <input type="hidden" name="email" value="{{ $email }}">
                        <input type="hidden" name="amount" value="{{ $amount }}">
                        <input type="hidden" name="original" value="{{ $original }}">
                        <input type="hidden" name="reference" value="{{ $reference }}">
                        <input type="hidden" name="key" value="{{ $key }}">

                    </div>

                    <div class="form-group m-b-0">
                        <div class="offset-sm-3 col-sm-9">
                            <button type="submit" class="btn btn-success waves-effect waves-light">Pay Now</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection