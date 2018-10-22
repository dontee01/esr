@extends('layouts.layout')
@section('title', 'My Account')
@section('content')


	<div class="row page-titles">
        <div class="col-md-6 col-7 align-self-center">
            <h3 class="text-themecolor m-b-0 m-t-0">My Account</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">My Profile</li>
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
        <div class="col-lg-4 col-xlg-3 col-md-5">
            <div class="card">
                <div class="card-body">
                    <center class="m-t-30"> <img src="{{ asset('assets/images/users/5.jpg') }}" class="img-circle" width="150" />
                        <h4 class="card-title m-t-10">{{ $profile->firstname.' '.$profile->lastname }}</h4>
                        <!-- <h6 class="card-subtitle">Accoubts Manager Amix corp</h6> -->
                    </center>
                </div>
                <div>
                    <hr> </div>
                <div class="card-body">
                    <small class="text-muted">Referral Code </small>
                    <h6>{{ $profile->esr_number }}</h6>

                    <small class="text-muted">Email address </small>
                    <h6>{{ $profile->email }}</h6>
                    <small class="text-muted p-t-30 db">Phone</small>
                    <h6>{{ $profile->mobile }}</h6> 
                    <small class="text-muted p-t-30 db">Address</small>
                    <h6>{{ $profile->state }}, {{ $profile->country }}</h6>
                    <!-- <small class="text-muted p-t-30 db">Social Profile</small> -->
                    <br/>
                    <!-- <button class="btn btn-circle btn-secondary"><i class="fa fa-facebook"></i></button>
                    <button class="btn btn-circle btn-secondary"><i class="fa fa-twitter"></i></button>
                    <button class="btn btn-circle btn-secondary"><i class="fa fa-youtube"></i></button> -->
                </div>
            </div>
        </div>
        <!-- Column -->
        <!-- Column -->
        <div class="col-lg-8 col-xlg-9 col-md-7">
            <div class="card">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs profile-tab" role="tablist">
                    <!-- <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#home" role="tab">Timeline</a> </li> -->
                    <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#profile" role="tab">Profile</a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Update Profile</a> </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <!--second tab-->
                    <div class="tab-pane active" id="profile" role="tabpanel">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 col-xs-6 b-r"> <strong>Full Name</strong>
                                    <br>
                                    <p class="text-muted">{{ $profile->firstname.' '.$profile->lastname }}</p>
                                </div>
                                <div class="col-md-3 col-xs-6 b-r"> <strong>Mobile</strong>
                                    <br>
                                    <p class="text-muted">{{ $profile->mobile }}</p>
                                </div>
                                <div class="col-md-3 col-xs-6 b-r"> <strong>Email</strong>
                                    <br>
                                    <p class="text-muted">{{ $profile->email }}</p>
                                </div>
                                <!-- <div class="col-md-3 col-xs-6 b-r"> <strong>Location</strong>
                                    <br>
                                    <p class="text-muted">Ibadan</p>
                                </div> -->
                                <div class="col-md-3 col-xs-6 b-r"> <strong>Gender</strong>
                                    <br>
                                    <p class="text-muted">{{ $profile->gender }}</p>
                                </div>
                                <div class="col-md-3 col-xs-6 b-r"> <strong>Bank Name</strong>
                                    <br>
                                    <p class="text-muted">{{ $profile->bank_name }}</p>
                                </div>
                                <div class="col-md-3 col-xs-6"> <strong>Account Number</strong>
                                    <br>
                                    <p class="text-muted">{{ $profile->account_number }}</p>
                                </div>
                            </div>
                            <hr>
                            
                        </div>
                    </div>
                    <div class="tab-pane" id="settings" role="tabpanel">
                        <div class="card-body">
                            <form class="form-horizontal form-material" method="post" action="{{ url('profile/update') }}">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label class="col-md-12">Full Name</label>
                                    <div class="col-md-12">
                                        <input type="text" name="fullname" placeholder="{{ $profile->firstname.' '.$profile->lastname }}" class="form-control form-control-line" required="required" value="{{ $profile->firstname.' '.$profile->lastname }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="example-email" class="col-md-12">Email</label>
                                    <div class="col-md-12">
                                        <input type="email" placeholder="{{ $profile->email }}" class="form-control form-control-line" name="email" id="example-email" required="required" value="{{ $profile->email }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">Phone Number</label>
                                    <div class="col-md-12">
                                        <input type="text" name="mobile" placeholder="{{ $profile->mobile }}" class="form-control form-control-line" required="required" value="{{ $profile->mobile }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button class="btn btn-success">Update Profile</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Column -->
    </div>  

@endsection