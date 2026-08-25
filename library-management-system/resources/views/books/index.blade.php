@extends('layouts.public')
@section('title', 'Collections | Central Library')
@section('content')
<header class="page-head"><div class="container"><div class="eyebrow">Library catalog</div><h1>Explore the collection.</h1></div></header>
<section class="section"><div class="container">
    <div class="toolbar" style="align-items:stretch;flex-wrap:wrap">
        <form class="filter" method="get" style="display:grid;grid-template-columns:minmax(0,1fr) minmax(150px,180px) auto auto;gap:10px;flex:1;max-width:none">
            <div class="field" style="margin:0">
                <label for="q" style="display:block;margin-bottom:7px;font-weight:800;font-size:.76rem;letter-spacing:.04em;text-transform:uppercase;color:var(--muted)">Search</label>
                <input id="q" name="q" value="{{ $search }}" placeholder="Search title, author, subject, or ISBN">
            </div>
            <div class="field" style="margin:0">
                <label for="sort" style="display:block;margin-bottom:7px;font-weight:800;font-size:.76rem;letter-spacing:.04em;text-transform:uppercase;color:var(--muted)">Filter</label>
                <select id="sort" name="sort">
                    <option value="title" @selected($sort === 'title')>Title</option>
                    <option value="publisher" @selected($sort === 'publisher')>Publisher</option>
                    <option value="category" @selected($sort === 'category')>Category</option>
                    <option value="author" @selected($sort === 'author')>Author</option>
                    <option value="isbn" @selected($sort === 'isbn')>ISBN</option>
                </select>
            </div>
            <button class="btn" type="submit">Update</button>
            <a class="btn secondary" href="{{ route('books.index') }}">Reset</a>
        </form>
        @if(auth()->user()->isStaff())<a class="btn" href="{{ route('books.create') }}">Add a book</a>@endif
    </div>
    <div class="card-grid">
    @forelse($books as $book)
        <article class="card">
            <span class="tag">{{ $book->category }}</span><h3>{{ $book->title }}</h3><p>By {{ $book->author }}</p>
            <div class="meta">{{ $book->publisher }}@if($book->published_year) · {{ $book->published_year }}@endif<br><strong>{{ $book->available_copies ?? 0 }}</strong> of {{ $book->total_copies }} available</div>
            <div class="actions" style="margin-top:18px">
                <form method="post" action="{{ route('books.reserve', $book->id) }}">@csrf<button class="btn small secondary">Reserve</button></form>
                @if(auth()->user()->isStaff())<a class="btn small dark" href="{{ route('books.edit', $book->id) }}">Edit</a><form method="post" action="{{ route('books.delete', $book->id) }}" onsubmit="return confirm('Remove this book?')">@csrf @method('DELETE')<button class="btn small danger">Delete</button></form>@endif
            </div>
        </article>
    @empty <p>No books match your search.</p> @endforelse
    </div>
    <div class="pagination">{{ $books->links() }}</div>
</div></section>
@endsection
