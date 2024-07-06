@extends('layout')


@section('head')
    <title>FreeNews - Вподобані новини</title>
@endsection

@section('content')
    <div class="container">
        <h1>Вподобані новини</h1>
        @include('web.parts.row-cards-posts', ['posts' => $posts])
    </div>
@endsection
