<?php

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function (Request $request) {
    $offset = $request->get('offset', 0);

    $posts = Post::query()->latest()->select(['title', 'images', 'short_content', 'meta', 'slug'])->limit(20 + $offset)->get();
    $mainPost = $posts->shift();
    return view('web.front-page', ['posts' => $posts->toArray(), 'mainPost' => $mainPost->toArray()]);
});

Route::get('/tag/{slug}', function ($slug) {
    $posts = Post::query()->whereHas('tags', function ($query) use ($slug) {
        $query->where('tags.slug', $slug);
    })->latest()->select(['title', 'images', 'short_content', 'meta', 'slug'])->limit(20)->get();
    $tagName = Tag::query()->where('slug', $slug)->first()?->name;

    if(!$tagName) {
        abort(404);
    }

    return view('web.single-tag', ['posts' => $posts->toArray(), 'tagName' => $tagName]);
});

Route::get('/likes', function () {
    $likeIds = array_filter(explode(',', $_COOKIE['userLikes'] ?? '') ?? []);
    $likeIds = array_reverse($likeIds);
    if(empty($likeIds)) {
        return view('web.likes', ['posts' => []]);
    }

    $posts = Post::query()->whereIn('id', $likeIds)
        ->orderByRaw('FIELD(id, ' . implode(',', $likeIds) . ')')
        ->select(['title', 'images', 'short_content', 'meta', 'slug'])
        ->get();
    return view('web.likes', ['posts' => $posts->toArray()]);
});

Route::get('/search', function () {
    $latestPosts = Post::query()->latest()->select(['title', 'images', 'meta', 'slug'])->limit(10)->get();
    return view('web.search', ['latestPosts' => $latestPosts->toArray()]);
});
Route::get('/s', function (Request $request) {
    $query = $request->get('query');

    $posts = Post::query()
        ->where('title', 'like', "%{$query}%")
        ->orWhere('content', 'like', "%{$query}%")
        ->select(['title', 'images', 'meta', 'slug'])
        ->latest()
        ->limit(20)->get();

    return response()->json(view('web.parts.row-cards-posts', ['posts' => $posts->toArray()])->render());
});


Route::get('{slug}', function ($slug) {
    $post = Cache::remember("post_{$slug}", 60, function () use ($slug) {
        return Post::with('tags')->where('slug', $slug)->firstOrFail();
    });

    $relatedPosts = Cache::remember("related_posts_{$slug}", 60, function () use ($post) {
        return Post::query()->whereHas('tags', function ($query) use ($post) {
            $query->whereIn('tags.id', $post->tags->pluck('id'));
        })->where('id', '!=', $post->id)->select(['id', 'title', 'images', 'short_content', 'meta', 'slug'])->limit(5)->get();
    });

    $isLiked = in_array($post->id, array_filter(explode(',', $_COOKIE['userLikes'] ?? '') ?? []));

    return view('web.single-post', [
        'post' => $post->toArray(),
        'relatedPosts' => $relatedPosts->toArray(),
        'isLiked' => $isLiked,
    ]);
});
