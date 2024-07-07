@extends('layout')

@section('head')
    <title>{{ $post['title'] }}</title>

    <!-- Meta Tags -->
    <meta name="description" content="{{ $post['short_content'] }}">

    <link rel="canonical" href="{{ url($post['slug']) }}">

    <!-- Open Graph Tags -->
    <meta property="og:title" content="{{ $post['title'] }}">
    <meta property="og:description" content="{{ $post['short_content'] }}">
    <meta property="og:image" content="{{Storage::disk('s3')->url($post['images']['s'])}}">
    <meta property="og:url" content="{{ url($post['slug']) }}">
    <meta property="og:type" content="article">

    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $post['title'] }}">
    <meta name="twitter:description" content="{{ $post['short_content'] }}">
    <meta name="twitter:image" content="{{Storage::disk('s3')->url($post['images']['s'])}}">

    <!-- JSON-LD Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BlogPosting",
      "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{{ url($post['slug']) }}"
      },
      "headline": "{{ $post['title'] }}",
      "image": [
        "{{Storage::disk('s3')->url($post['images']['s'])}}"
      ],
      "description": "{{ $post['short_content'] }}"
    }
    </script>
    <link rel="preload" href="{{Storage::disk('s3')->url($post['images']['webp']['s'])}}" as="image">

@endsection

@section('content')
    <article>
        <div class="post-image-content" style="background-color: #d5d5d5;">
            <picture>
                <source type="image/webp"  srcset="{{Storage::disk('s3')->url($post['images']['webp']['s'])}}">
                <img width="370" height="233" class="post-image"
                     src="{{Storage::disk('s3')->url($post['images']['s'])}}"
                     alt="{{Str::limit($post['title'], 20, '')}}"
                >
            </picture>
            <div class="like @if($isLiked) {{'liked'}} @endif">
                <svg width="25" height="25" viewBox="0 0 24 24" fill="#fff">
                    <path
                        d="M8.96173 18.9109L9.42605 18.3219L8.96173 18.9109ZM12 5.50063L11.4596 6.02073C11.601 6.16763 11.7961 6.25063 12 6.25063C12.2039 6.25063 12.399 6.16763 12.5404 6.02073L12 5.50063ZM15.0383 18.9109L15.5026 19.4999L15.0383 18.9109ZM9.42605 18.3219C7.91039 17.1271 6.25307 15.9603 4.93829 14.4798C3.64922 13.0282 2.75 11.3345 2.75 9.1371H1.25C1.25 11.8026 2.3605 13.8361 3.81672 15.4758C5.24723 17.0866 7.07077 18.3752 8.49742 19.4999L9.42605 18.3219ZM2.75 9.1371C2.75 6.98623 3.96537 5.18252 5.62436 4.42419C7.23607 3.68748 9.40166 3.88258 11.4596 6.02073L12.5404 4.98053C10.0985 2.44352 7.26409 2.02539 5.00076 3.05996C2.78471 4.07292 1.25 6.42503 1.25 9.1371H2.75ZM8.49742 19.4999C9.00965 19.9037 9.55954 20.3343 10.1168 20.6599C10.6739 20.9854 11.3096 21.25 12 21.25V19.75C11.6904 19.75 11.3261 19.6293 10.8736 19.3648C10.4213 19.1005 9.95208 18.7366 9.42605 18.3219L8.49742 19.4999ZM15.5026 19.4999C16.9292 18.3752 18.7528 17.0866 20.1833 15.4758C21.6395 13.8361 22.75 11.8026 22.75 9.1371H21.25C21.25 11.3345 20.3508 13.0282 19.0617 14.4798C17.7469 15.9603 16.0896 17.1271 14.574 18.3219L15.5026 19.4999ZM22.75 9.1371C22.75 6.42503 21.2153 4.07292 18.9992 3.05996C16.7359 2.02539 13.9015 2.44352 11.4596 4.98053L12.5404 6.02073C14.5983 3.88258 16.7639 3.68748 18.3756 4.42419C20.0346 5.18252 21.25 6.98623 21.25 9.1371H22.75ZM14.574 18.3219C14.0479 18.7366 13.5787 19.1005 13.1264 19.3648C12.6739 19.6293 12.3096 19.75 12 19.75V21.25C12.6904 21.25 13.3261 20.9854 13.8832 20.6599C14.4405 20.3343 14.9903 19.9037 15.5026 19.4999L14.574 18.3219Z"
                    />
                </svg>
            </div>
        </div>


        <div class="container content">
            <h1 class="post-title">{{$post['title']}}</h1>
            <div class="post-time">
                {{$post['meta']['time']}}
            </div>
            <div class="post-source">
                Матеріали взято з
                <a href="{{$post['meta']['source_url']}}" target="_blank">{{$post['meta']['source_name']}}</a>
            </div>
            <div class="post-content">
                {!! $post['content'] !!}
            </div>
        </div>
    </article>
    <div class="container">
        <div class="post-tags">
            @foreach($post['tags'] as $tag)
                <a href="{{url('tag', $tag['slug'])}}">{{$tag['name']}}</a>
            @endforeach
        </div>

        @if(!empty($relatedPosts))
            <div class="related-posts">
                <h2>Схожі новини</h2>
                @include('web.parts.row-cards-posts', ['posts' => $relatedPosts])

            </div>
        @endif
    </div>


    <script>
        window.currentPostId = {{$post['id']}}
    </script>
@endsection
