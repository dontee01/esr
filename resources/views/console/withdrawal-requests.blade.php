@extends('console.layouts.layout')
@section('title', 'Withdrawal Requests')
@section('content')


    <div class="row page-titles">
        <div class="col-md-6 col-7 align-self-center">
            <h3 class="text-themecolor m-b-0 m-t-0">Requests</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Withdrawal Requests</li>
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
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Withdrawal Requests</h4>
                    <h6 class="card-subtitle">Pending Requests</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fullname</th>
                                    <th>Bank Name</th>
                                    <th>Account Name</th>
                                    <th>Account Number</th>
                                    <th>Amount</th>
                                    <th>Current Balance</th>
                                    <th>Action</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($withdrawal_pending) < 1)
                                <p>No request yet</p>
                                @else
                                @foreach ($withdrawal_pending as $key => $wpend)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $wpend->firstname.' '.$wpend->lastname }}</td>
                                    <td>{{ $wpend->bank_name }}</td>
                                    <td>{{ $wpend->account_name }}</td>
                                    <td>{{ $wpend->account_number }}</td>
                                    <td><span class="label label-info label-rouded">${{ $wpend->amount }}</span></td>
                                    <td>${{ $wpend->balance }}</td>
                                    <td>
                                        <form method="post" action="{{ url('console/withdrawal/approve') }}">
                                            <input type="hidden" name="ssh" value="{{ $wpend->id }}">
                                            <button type="submit" class="btn btn-xs btn-success waves-effect waves-light m-t-10" name="approve">Approve</button>
                                        </form>
                                    </td>
                                    <td>{{ date_create($wpend->created_at)->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                                {{ $withdrawal_pending->links() }}
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Withdrawal Requests</h4>
                    <h6 class="card-subtitle">Approved Requests</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fullname</th>
                                    <th>Bank Name</th>
                                    <th>Account Name</th>
                                    <th>Account Number</th>
                                    <th>Amount</th>
                                    <th>Current Balance</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($withdrawal_approved) < 1)
                                <p>No request yet</p>
                                @else
                                @foreach ($withdrawal_approved as $key => $wpend)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $wpend->firstname.' '.$wpend->lastname }}</td>
                                    <td>{{ $wpend->bank_name }}</td>
                                    <td>{{ $wpend->account_name }}</td>
                                    <td>{{ $wpend->account_number }}</td>
                                    <td>${{ $wpend->amount }}</td>
                                    <td>${{ $wpend->balance }}</td>
                                    <td>{{ date_create($wpend->created_at)->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                                {{ $withdrawal_approved->links() }}
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
                 
         

@endsection