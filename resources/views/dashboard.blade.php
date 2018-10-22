@extends('layouts.layout')
@section('title', 'Dashboard')
@section('content')

@if(empty($profile))
 An error occurred 
@else

	<div class="row page-titles">
        <div class="col-md-6 col-7 align-self-center">
            <h3 class="text-themecolor m-b-0 m-t-0">Dashboard</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>

        <div class="col-md-6 col-5 align-self-center">
            <div class="card card-success" style="float:right;">
                <div class="box text-center">
                <p class="text-black">Wallet</p>
                    <h3 class="font-light text-white">&#8358; {{ $profile->balance }}</h3>
                </div>
            </div>
            <!-- <div style="float:right;">
                <h3>Wallet :  &#8358; {{-- $profile->balance --}}</h3>
                
            </div> -->
            
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
        <div class="col-lg-6 col-md-6">
            <div class="card btn btn-info">
                <a class="card-body" href="{{ url('history/requests') }}">
                    <!-- <div id="myCarousel" class="carousel slide" data-ride="carousel"> -->
                        <!-- Carousel items -->
                        <div class="">
                            <div class="carousel-item flex-column active">
                                <!-- <i class="fa fa-twitter fa-2x text-white"></i> -->
                                <p class="text-white">&nbsp;</p>
                                <h3 class="text-white font-light">Request payment</h3>
                                <div class="text-white m-t-20">
                                    <i>&nbsp;</i>
                                </div>
                            </div>
                        </div>
                    <!-- </div> -->
                </a>
            </div>
        </div>


        <div class="col-lg-6 col-md-6">
            <div class="card btn btn-primary">
                <a class="card-body" href="{{ url('history/transfer') }}">
                    <!-- <div id="myCarousel" class="carousel slide" data-ride="carousel"> -->
                        <!-- Carousel items -->
                        <div class="">
                            <div class="carousel-item flex-column active">
                                <p class="text-white">&nbsp;</p>
                                <h3 class="text-white font-light">Transfer</h3>
                                <div class="text-white m-t-20">
                                    <i>&nbsp;</i>
                                </div>
                            </div>
                        </div>
                    <!-- </div> -->
                </a>
            </div>
        </div>
    </div>
                 
@endif         

@endsection