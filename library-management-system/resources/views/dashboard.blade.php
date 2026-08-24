@extends('layouts.public')
@section('title', 'Dashboard | Central Library')
@section('content')
<header class="page-head"><div class="container"><div class="eyebrow">{{ $role }} account</div><h1>Hello, {{ auth()->user()->name }}.</h1><p class="lede">Here’s what is happening across your library today.</p></div></header>
<section class="section"><div class="container">
    <div class="stats">
        <div class="stat"><strong>{{ $stats['titles'] }}</strong><span>Catalog titles</span></div>
        <div class="stat"><strong>{{ $stats['available'] }}</strong><span>Available copies</span></div>
        <div class="stat"><strong>{{ $stats['on_loan'] }}</strong><span>Books on loan</span></div>
        <div class="stat"><strong>{{ $stats['reservations'] }}</strong><span>Waiting reservations</span></div>
    </div>
    <div class="section-head"><h2 class="section-title">Recent loans</h2><a class="btn secondary" href="{{ route('books.index') }}">Browse collection</a></div>
    <div class="table-wrap"><table><thead><tr><th>Title</th>@if(auth()->user()->isStaff())<th>Member</th>@endif<th>Issued</th><th>Due</th><th>Status</th></tr></thead><tbody>
    @forelse($recentLoans as $loan)
    <tr>
        <td><strong>{{ $loan->title }}</strong><br><span class="muted">{{ $loan->barcode }}</span>
    </td>
    @if(auth()->user()->isStaff())
    <td>{{ $loan->member }}</td>
    @endif
    <td>{{ \Carbon\Carbon::parse($loan->issue_date)->format('d M Y') }}</td>
    <td>{{ \Carbon\Carbon::parse($loan->due_date)->format('d M Y') }}</td>
    <td><span class="status {{ $loan->status }}">{{ $loan->status }}</span></td></tr>
    @empty<tr><td colspan="5">No loans recorded yet.</td></tr>@endforelse
    </tbody></table></div>
</div></section>
@endsection
