@extends('layouts.public')
@section('title', 'Central Library')
@section('content')
<section class="hero">
    <div class="hero-copy">
        <div class="eyebrow">Knowledge, within reach</div>
        <h1 class="display">Welcome to Central Library</h1>
        <p>Our collections and librarians help you discover, use, and create knowledge—at every branch and from wherever you are.</p>
        <form class="search-panel" action="{{ route('home') }}" method="get" role="search">
            <label for="library-search">Search the library</label>
            <div class="search-row">
                <input id="library-search" name="q" value="{{ $query }}" type="search" placeholder="Title, author, category, or ISBN">
                <select id="library-sort" name="sort" aria-label="Filter catalog results">
                    <option value="title" @selected($sort === 'title')>Title</option>
                    <option value="publisher" @selected($sort === 'publisher')>Publisher</option>
                    <option value="category" @selected($sort === 'category')>Category</option>
                    <option value="author" @selected($sort === 'author')>Author</option>
                    <option value="isbn" @selected($sort === 'isbn')>ISBN</option>
                </select>
                <button type="submit">Search</button>
            </div>
        </form>
    </div>
</section>
@if($query !== '')
<section class="section"><div class="container">
    <div class="section-head"><div><div class="eyebrow">Catalog results</div><h2 class="section-title">{{ $books->count() }} result{{ $books->count() === 1 ? '' : 's' }} for “{{ $query }}”</h2></div></div>
    <div class="card-grid">
        @forelse($books as $book)
        <article class="card"><span class="tag">{{ $book->category }}</span><h3>{{ $book->title }}</h3><p>{{ $book->author }}</p><div class="meta">{{ $book->available_copies ?? 0 }} of {{ $book->total_copies }} copies available</div></article>
        @empty <p>No catalog records matched. Try a broader term.</p> @endforelse
    </div>
</div></section>
@endif
<section class="section" id="services"><div class="container">
    <div class="eyebrow">Explore what’s possible</div><h2 class="section-title">One library, every part of your research journey.</h2>
    <p class="lede">Find the right title, see real-time availability, reserve materials, and keep every loan and fine in one clear account.</p>
    <div class="card-grid" style="margin-top:30px">
        <a class="card" href="{{ route('books.index') }}"><h3>Discover collections →</h3><p>Search across titles, authors, subjects, and ISBNs.</p></a>
        <a class="card" href="{{ auth()->check() ? route('reservations') : route('register') }}"><h3>Reserve a title →</h3><p>Join a fair queue when the book you need is unavailable.</p></a>
        <a class="card" href="{{ auth()->check() ? route('dashboard') : route('login') }}"><h3>Manage your account →</h3><p>Review due dates, notices, reservations, and fines.</p></a>
    </div>
</div></section>
@if($featured->isNotEmpty())
<section class="section soft"><div class="container">
    <div class="eyebrow">From the shelves</div><h2 class="section-title">Featured titles</h2>
    <div class="card-grid">
        @foreach($featured as $book)<article class="card"><span class="tag">{{ $book->category }}</span><h3>{{ $book->title }}</h3><p>By {{ $book->author }}</p><div class="meta">{{ $book->publisher }} · {{ $book->available_copies ?? 0 }} available</div></article>@endforeach
    </div>
</div></section>
@endif
@endsection
