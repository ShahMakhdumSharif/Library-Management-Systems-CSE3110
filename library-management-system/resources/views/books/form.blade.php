@extends('layouts.public')
@section('title', ($book ? 'Edit' : 'Add').' Book | Central Library')
@section('content')
<header class="page-head"><div class="container"><div class="eyebrow">Collection management</div><h1>{{ $book ? 'Edit this title.' : 'Add a new title.' }}</h1></div></header>
<section class="section soft"><div class="container">
<form class="form-card" method="post" action="{{ $book ? route('books.update', $book->id) : route('books.store') }}">@csrf @if($book)@method('PUT')@endif
    @if($errors->any())<div class="flash error" style="margin:0 0 24px">{{ $errors->first() }}</div>@endif
    <div class="form-grid">
        <div class="field full"><label for="title">Title</label><input id="title" name="title" value="{{ old('title', $book->title ?? '') }}" required></div>
        <div class="field"><label for="author">Author</label><input id="author" name="author" value="{{ old('author', $details->author ?? '') }}" required></div>
        <div class="field"><label for="category">Category</label><input id="category" name="category" value="{{ old('category', $details->category ?? '') }}" required></div>
        <div class="field"><label for="isbn">ISBN</label><input id="isbn" name="isbn" value="{{ old('isbn', $book->isbn ?? '') }}" required></div>
        <div class="field"><label for="publisher">Publisher</label><input id="publisher" name="publisher" value="{{ old('publisher', $book->publisher ?? '') }}" required></div>
        <div class="field"><label for="published_year">Publication year</label><input id="published_year" name="published_year" type="number" value="{{ old('published_year', $book->published_year ?? '') }}"></div>
        <div class="field"><label for="branch_id">Branch for {{ $book ? 'new' : '' }} copies</label><select id="branch_id" name="branch_id" required><option value="">Select branch</option>@foreach($branches as $branch)<option value="{{ $branch->id }}" @selected(old('branch_id') == $branch->id)>{{ $branch->name }}</option>@endforeach</select></div>
        <div class="field"><label for="copy_count">{{ $book ? 'Additional copies' : 'Number of copies' }}</label><input id="copy_count" name="copy_count" type="number" min="{{ $book ? 0 : 1 }}" max="50" value="{{ old('copy_count', $book ? 0 : 1) }}"></div>
        <div class="field full"><label for="description">Description</label><textarea id="description" name="description">{{ old('description', $book->description ?? '') }}</textarea></div>
    </div>
    <div class="actions" style="margin-top:24px"><button class="btn">{{ $book ? 'Save changes' : 'Add to collection' }}</button><a class="btn secondary" href="{{ route('books.index') }}">Cancel</a></div>
</form>
</div></section>
@endsection
