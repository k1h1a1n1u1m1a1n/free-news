@extends('layout')


@section('head')
    <title>FreeNews - Пошук</title>
@endsection


@section('content')
    @vite('resources/scss/search.scss')
    <div class="container">
        <h1>Пошук</h1>
        <div class="input-wrapper">
            <svg width="25" height="25" viewBox="0 0 24 24" fill="none">
                <path
                    d="M11.5 2.75C6.66751 2.75 2.75 6.66751 2.75 11.5C2.75 16.3325 6.66751 20.25 11.5 20.25C16.3325 20.25 20.25 16.3325 20.25 11.5C20.25 6.66751 16.3325 2.75 11.5 2.75ZM1.25 11.5C1.25 5.83908 5.83908 1.25 11.5 1.25C17.1609 1.25 21.75 5.83908 21.75 11.5C21.75 14.0605 20.8111 16.4017 19.2589 18.1982L22.5303 21.4697C22.8232 21.7626 22.8232 22.2374 22.5303 22.5303C22.2374 22.8232 21.7626 22.8232 21.4697 22.5303L18.1982 19.2589C16.4017 20.8111 14.0605 21.75 11.5 21.75C5.83908 21.75 1.25 17.1609 1.25 11.5Z"
                    fill="#1C274C"/>
            </svg>
            <input type="text" name="s" placeholder="Почніть вводити...">
        </div>

        <div id="search-content">
            @include('web.parts.row-cards-posts', ['posts' => $latestPosts])
        </div>



    </div>
    @vite('resources/js/search.js')
@endsection