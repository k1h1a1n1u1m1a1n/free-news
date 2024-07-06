<meta property="og:url" content="{{Request::url()}}">
<meta property="og:type" content="website"/>

@if(!empty($seoData['og_image']))
    <meta property="og:image" content="{{asset('storage/' . $seoData->og_image)}}">
@endif

@if(!empty($seoData['og_title']))
    <meta property="og:title" content="{{$seoData->og_title}}">
@endif

@if(!empty($seoData['description']))
    <meta name="description" content="{{$seoData->description}}">
    <meta property="og:description" content="{{$seoData->description}}">
@endif

@if(!empty($seoData['keywords']))
    <meta name="keywords" content="{{$seoData->keywords}}">
@endif


