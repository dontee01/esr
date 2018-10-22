@extends('layouts.layout')
@section('title', 'Account')
@section('content')


	<div class="row page-titles">
        <div class="col-md-6 col-7 align-self-center">
            <h3 class="text-themecolor m-b-0 m-t-0">My Account</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Change Password/Pin</li>
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
        <div class="col-sm-6">
            <div class="card card-body">
                <h4 class="card-title">Update Password</h4>
                <!-- <h6 class="card-subtitle"> Please ensure you specify the right recipient and amount you want to transfer </h6> -->
                <form class="form-horizontal m-t-40" method="post" action="{{ url('account/change-password')}}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <input class="form-control" type="password" name="old_password" id="old_password" placeholder="Old Password" required="required" />
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" name="password" id="password" placeholder="New Password" required="required" />
                    </div>

                    <div class="form-group">
                        <input class="form-control" type="password" name="repeat" id="repeat" placeholder="Confirm Password" required="required" />
                    </div>

                    <div class="form-group m-b-0">
                        <div class="offset-sm-3 col-sm-9">
                            <button type="submit" class="btn btn-success waves-effect waves-light">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <div class="col-sm-6">
            <div class="card card-body">
                <h4 class="card-title">Update Transaction Pin</h4>
                <h6 class="card-subtitle text-primary"> Default Transaction pin for new members is <span class="text-danger">1234</span> </h6>
                <form class="form-horizontal m-t-40" method="post" action="{{ url('account/change-pin')}}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <input class="form-control" type="password" name="old_pin" id="old_pin" placeholder="Old Pin" required="required" />
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" name="pin" id="pin" placeholder="Enter New 4 digit Pin" required="required" />
                    </div>

                    <div class="form-group">
                        <input class="form-control" type="password" name="repeat" id="repeat" placeholder="Confirm Pin" required="required" />
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

@endsection