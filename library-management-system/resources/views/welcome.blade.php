@extends('layouts.public')

@section('title', 'Central Library')

@section('content')
    <section class="hero" aria-labelledby="home-title">
        <div class="eyebrow">Library services</div>
        <h1 id="home-title">Welcome to Central Library</h1>
        <p>
            We are the libraries and archives of Central University. Our librarians help you discover, use, and create knowledge.
        </p>

        <form class="search-panel" action="#" method="get" role="search">
            <label for="library-search">Search the library</label>
            <div class="search-row">
                <input id="library-search" name="q" type="search" placeholder="Search by book title, author, category, or member ID">
                <button type="submit">Search</button>
            </div>
        </form>
    </section>
@endsection
