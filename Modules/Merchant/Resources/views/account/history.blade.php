@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Lịch sử hoạt động của tài khoản ') }} {{$merchant->name ?? $merchant->email}}</h1>

    @include('admin::layouts.partials.header-message')

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable-vti" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Tài khoản</th>
                        <th>Chức năng</th>
                        <th style="min-width: 200px">Hành động</th>
                        <th class="text-center">Nội dung</th>
                        <th>Thời gian thực hiện</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@php
    $url = ($request->route()->getName() === 'merchant.account.historyMerchant') ? route($request->route()->getName(), ['id' => $request->id]) : route($request->route()->getName())
@endphp

@section('extra-js')
    <script>
        $(function () {
            var table = $('#dataTable-vti').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ $url }}",
                order: [4, 'DESC'],
                columns: [
                    {data: 'account_name', name: 'account_name'},
                    {data: 'function', name: 'function', searchable: false},
                    {data: 'action', name: 'action', searchable: false},
                    {data: 'content_request', name: 'content_request', searchable: false},
                    {data: 'created_at', name: 'created_at'}
                ]
            });
        });
    </script>
@endsection
