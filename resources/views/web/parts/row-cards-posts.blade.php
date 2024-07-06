@foreach($posts as $post)
    <article class="row-card">
        <a href="{{url($post['slug'])}}">
            <div class="row-card__content">
                <h2 class="row-card__title">{{$post['title']}}</h2>
                <time class="row-card__time">{{$post['meta']['time']}}</time>
            </div>
            <div class="row-card__img">
                <picture>
                    <source type="image/webp" srcset="{{Storage::disk('s3')->url($post['images']['webp']['sqr'])}}">

                    <img width="90" height="90"
                         loading="lazy"
                         src="{{Storage::disk('s3')->url($post['images']['sqr'])}}" alt="{{$post['title']}}"
                    >
                </picture>
            </div>
        </a>
    </article>
@endforeach

@if(empty($posts))
    <div class="empty">
        Нічого не знайдено
    </div>
@endif
