@php

@endphp
@extends('layouts.master')
@section('content')
    <div class="container">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><i class="fas fa-home" aria-hidden="true"></i> Marketing</li>
            <li class="breadcrumb-item">Shortlink</li>
        </ul>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Danh sách các shortlink</h5>

                        <div id="fb-root"></div>
                        <script async defer crossorigin="anonymous"
                            src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v14.0&appId=615532730010827&autoLogAppEvents=1"
                            nonce="UbbJ5EFS"></script>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover" border="0">
                            <thead>
                                <tr style="background-color: #dee2e6">
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Tittle</th>
                                    <th>Link</th>
                                    <th>Click Post</th>
                                    <th>Share Post</th>
                                    <th>Link Post</th>
                                    <th>Click Story</th>
                                    <th>Share Story</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{--@foreach ($fanpage as $item)
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
                                @endforeach--}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
@endsection
