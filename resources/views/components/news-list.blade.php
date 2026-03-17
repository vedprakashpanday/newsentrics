@foreach($newsList as $news)
    @include('components.news-card', ['news' => $news])
@endforeach