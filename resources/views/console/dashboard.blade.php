@extends('console.layouts.layout')
@section('title', 'Dashboard')
@section('content')


    <div class="row page-titles">
        <div class="col-md-6 col-7 align-self-center">
            <h3 class="text-themecolor m-b-0 m-t-0">Dashboard</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
        <div class="col-md-6 col-5 align-self-center">
            <div style="float:right;">
                <h3>Wallet Balance :  &#8358;$balance</h3>
              <span style="color:red;" class="text-right" ><small >Hold balance: &#8358;0</small></span>
                
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
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Funding Requests</h4>
                    <h6 class="card-subtitle">Pending </h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Teller Name</th>
                                    <th>Teller Number</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($funding_pending) < 1)
                                <p>No request yet</p>
                                @else
                                @foreach ($funding_pending as $key => $fpend)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $fpend->firstname }}</td>
                                    <td>{{ $fpend->lastname }}</td>
                                    <td>{{ $fpend->teller_name }}</td>
                                    <td>{{ $fpend->teller_number }}</td>
                                    <td>{{ $fpend->amount }}</td>
                                    <td>
                                        <form method="post" action="{{ url('console/funding/approve') }}">
                                            <input type="hidden" name="ssh" value="{{ $fpend->id }}">
                                            <button type="submit" class="btn btn-xs btn-success waves-effect waves-light m-t-10" name="approve">Approve</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                                {{ $funding_pending->links() }}
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
                 
         

@endsection