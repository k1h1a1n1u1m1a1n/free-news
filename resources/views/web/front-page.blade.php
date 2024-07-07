@extends('layout')


@section('head')
    <title>FreeNews - Головна</title>
    <meta name="description" content="FreeNews - Головна">
    <link rel="canonical" href="{{ url('/') }}">
    <meta property="og:title" content="FreeNews - Головна">
    <meta property="og:description" content="FreeNews - Головна">
    <link rel="preload" href="{{Storage::disk('s3')->url($mainPost['images']['webp']['s'])}}" as="image">

@endsection

@section('content')
    <div class="container">
        <article class="big-card">
            <a href="{{url($mainPost['slug'])}}">
                <div class="big-card__img">
                    <picture>
                        <source type="image/webp"  srcset="{{Storage::disk('s3')->url($mainPost['images']['webp']['s'])}}">
                        <img width="340" height="214" src="{{Storage::disk('s3')->url($mainPost['images']['s'])}}"
                             alt="{{Str::limit($mainPost['title'], 20, '')}}"
                        />
                    </picture>

                </div>
                <div class="big-card__content">
                    <h1 class="big-card__title">{{$mainPost['title']}}</h1>
                </div>
            </a>
        </article>

        <div class="infinity-scroll" data-offset="{{count($posts) -1 }}">
            @include('web.parts.row-cards-posts', ['posts' => $posts])
        </div>
        <div class="infinity-scroll-end"></div>
    </div>
@endsection

