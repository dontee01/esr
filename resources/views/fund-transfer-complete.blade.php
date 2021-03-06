@extends('layouts.layout')
@section('title', 'Wallet')
@section('content')


	<div class="row page-titles">
        <div class="col-md-6 col-7 align-self-center">
            <h3 class="text-themecolor m-b-0 m-t-0">Wallet</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Fund Transfer</li>
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
                <h4 class="card-title">Confirm Transfer</h4>
                <h6 class="card-subtitle"> You are about to transfer a sum of ${{ $data->amount }} to : </h6>
                <form class="form-horizontal m-t-40" method="post" action="{{ url('transfer/complete')}}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Name</label>
                        <input class="form-control" type="text" name="receiver" placeholder="{{ $details->firstname.' '.$details->lastname }}" readonly="readonly" >
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <input class="form-control" type="text" name="description" placeholder="{{ $data->description }}" readonly="readonly" value="{{ old('description') }}">
                    </div>

                    <input class="form-control" type="hidden" name="receiver" value="{{ $data->username }}">
                    <input class="form-control" type="hidden" name="amount" value="{{ $data->amount }}">
                    <input class="form-control" type="hidden" name="description" value="{{ $data->description }}">

                    <div class="form-group m-b-0">
                        <div class="offset-sm-3 col-sm-9">
                            <button type="submit" class="btn btn-success waves-effect waves-light">Proceed</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div> 

@endsection