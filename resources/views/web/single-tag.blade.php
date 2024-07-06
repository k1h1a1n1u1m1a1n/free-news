@extends('layout')


@section('head')
    <title>FreeNews - {{$tagName}}</title>
@endsection

@section('content')
    <div class="container">
        <h1>Тег: {{$tagName}}</h1>
        @include('web.parts.row-cards-posts', ['posts' => $posts])
    </div>

@endsection
