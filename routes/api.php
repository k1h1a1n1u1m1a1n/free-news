<?php

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserApplication;
use App\Models\UserLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/post', function (Request $request) {
    $data = $request->all();


    $post = Post::where('title', $data['title'])->first();
    if ($post) {
        return response()->json(['error' => 'Post with this title already exists']);
    }
// Create necessary directories
    foreach (['full', 's', 'xs', 'sqr'] as $dir) {
        Storage::makeDirectory("public/posts/$dir");
    }

// Save original image to local storage
    $imageExtension = pathinfo($data['image'], PATHINFO_EXTENSION) ?? 'jpg';
    $imageSlug = Str::limit(Str::slug($data['title']), 20, '') . time();
    $imageName = "posts/full/{$imageSlug}.{$imageExtension}";
    Storage::put("public/$imageName", file_get_contents($data['image']));

    $manager = new ImageManager(
        new Intervention\Image\Drivers\Gd\Driver()
    );

// Define sizes and paths
    $sizes = [
        's' => 600,
        'sqr' => [150, 150]
    ];
    $paths = [];

// Process images
    foreach ($sizes as $key => $size) {
        $image = $manager->read(Storage::get("public/$imageName"));
        $image = is_array($size) ? $image->cover($size[0], $size[1]) : $image->scale($size);
        $path = "posts/{$key}/{$imageSlug}.{$imageExtension}";
        $image->save(storage_path("app/public/$path"), 80);
        $paths[$key] = $path;
    }

// Create WebP versions
    foreach ($paths as $key => $path) {
        $image = $manager->read(Storage::get("public/$path"))->encode(new WebpEncoder(80));
        $webpPath = str_replace(".{$imageExtension}", '.webp', $path);
        $image->save(storage_path("app/public/$webpPath"), 80);
        $paths["webp_$key"] = $webpPath;
    }

// Save images to S3
    foreach ($paths as $path) {
        Storage::disk('s3')->put($path, Storage::get("public/$path"));
    }

// Delete local images
    Storage::delete(array_map(fn($path) => "public/$path", $paths));

    $images = [
        "s" => $paths['s'],
        "sqr" => $paths['sqr'],
        "webp" => [
            "s" => $paths['webp_s'],
            "sqr" => $paths['webp_sqr'],
        ]
    ];
    $post = Post::create([
        'title' => $data['title'],
        'content' => $data['content'],
        'short_content' => $data['short_content'],
        'images' => $images,
        'slug' => Str::slug($data['title']),
        'meta' => [
            'time' => $data['time'],
            'source_name' => $data['source_name'],
            'source_url' => $data['source_url'],
            'main_image' => $data['image'],
        ]
    ]);

    // Save tags
    if (!empty($data['tags'])) {
        $tagIds = [];
        foreach ($data['tags'] as $tag) {
            $tagModel = Tag::firstOrCreate(['name' => $tag]);
            $tagIds[] = $tagModel->id;
        }
        $post->tags()->sync($tagIds);
    }


    return response()->json(true);
});

Route::get('/liked-posts', function (Request $request) {
    $ids = explode(',', $request->get('ids'));
    $posts = Post::query()->whereIn('id', $ids)->select(['title', 'images', 'meta', 'slug'])->get();
    $content = view('web.parts.row-cards-posts', ['posts' => $posts])->render();

    return response()->json(['posts' => $content]);
});

Route::get('/last-title', function () {
    $lastPost = Post::query()->latest()->first();
    return response()->json(['title' => $lastPost?->title]);
});

Route::get('/infinity', function (Request $request) {
    $offset = intval($request->get('offset'));
    $posts = Post::query()->select(['title', 'images', 'meta', 'slug'])->latest()->skip($offset)->limit(20)->get();
    $content = view('web.parts.row-cards-posts', ['posts' => $posts])->render();

    return response()->json(['html' => $content, 'offset' => $offset + 20]);
});
