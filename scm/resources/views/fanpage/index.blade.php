@php

@endphp
@extends('layouts.master')
@section('content')
    <div class="container">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><i class="fas fa-home" aria-hidden="true"></i> Media</li>
            <li class="breadcrumb-item">Loại kênh</li>
        </ul>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            @if (!isset($edit_page))
                                {{ 'Thêm fanpage mới' }}
                            @else
                                {{ 'Sửa fanpage' }}
                            @endif
                        </h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <form method="POST"
                            action="@if (isset($edit_page)) {{ route('fanpage.update', ['fanpage' => $fanpage_get[0]->id]) }}@else{{ route('fanpage.store') }} @endif">
                            @csrf
                            @isset($edit_page)
                                @method('PUT')
                            @endisset
                            <label>Chủ đề</label>
                            <input id="theme" type="text" class="form-control " name="theme"
                                value="@isset($edit_page){{ $fanpage_get[0]->theme }}@endisset"
                                required="" autofocus="" placeholder="Chủ đề">
                            <label>Tên Page</label>
                            <input id="page_name" type="text" class="form-control " name="page_name"
                                value="@isset($edit_page){{ $fanpage_get[0]->page_name }}@endisset"
                                required="" autofocus="" placeholder="Tên Page">
                            <label>Link</label>
                            <input id="link" type="text" class="form-control " name="link"
                                value="@isset($edit_page){{ $fanpage_get[0]->link }}@endisset"
                                required="" autofocus="" placeholder="Đường link">
                            <div class=" margin-top justify-content-between"
                                style="display: flex; -ms-flex-wrap: wrap; flex-wrap: wrap; -ms-flex-align: center; align-items: center;">
                                <button type="submit" class="btn btn-primary">Lưu lại</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Danh sách các fanpage</h5>

                        <div id="fb-root"></div>
                        <script async defer crossorigin="anonymous"
                            src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v14.0&appId=615532730010827&autoLogAppEvents=1"
                            nonce="UbbJ5EFS"></script>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover" border="0">
                            <thead>
                                <tr style="background-color: #dee2e6">
                                    <th>Id</th>
                                    <th>Chủ đề</th>
                                    <th>Tên Page</th>
                                    <th>Link</th>
                                    <th>Số Followers</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($fanpage as $item)
                                    <tr>
                                        <td> {{ $item->id }} </td>
                                        <td> {{ $item->theme }} </td>
                                        <td> {{ $item->page_name }} </td>
                                        <td> {{ $item->link }} </td>
                                        <td>
                                            <div class="fb-like" data-href="{{ $item->link }}" data-width=""
                                                data-layout="box_count" data-action="like" data-size="small"
                                                data-share="false"></div>
                                        </td>
                                        <td>
                                            <form action="{{ route('fanpage.edit', ['fanpage' => $item->id]) }}"
                                                method="edit">
                                                <button style="background: transparent; border: 0;"><i
                                                        class="far fa-edit ic24"></i></button>
                                            </form>
                                        </td>
                                        <td>
                                            <form action="{{ route('fanpage.destroy', ['fanpage' => $item->id]) }}"
                                                method="post">
                                                <button style="background: transparent; border: 0;"><i
                                                        class="far fa-trash-alt ic24 cursor-hand"
                                                        style="color: #ff5648!important"></i></button>
                                                <input type="hidden" name="_method" value="delete" />
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
@endsection
