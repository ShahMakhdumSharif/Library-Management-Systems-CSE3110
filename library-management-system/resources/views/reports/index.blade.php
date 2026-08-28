@extends('layouts.public')
@section('title', 'Reports | Central Library')
@section('content')
<header class="page-head"><div class="container"><div class="eyebrow">Operational reports</div><h1>Library at a glance.</h1></div></header>
<section class="section"><div class="container">
    <div class="stat" style="max-width:300px;border-top:6px solid var(--crimson);margin-bottom:35px"><strong>Tk {{ number_format($fineTotal, 2) }}</strong><span>Outstanding fines</span></div>
    <div class="section-head"><h2 class="section-title">Inventory</h2></div>
    <div class="table-wrap"><table><thead><tr><th>Title</th><th>Total copies</th><th>Available</th></tr></thead><tbody>@foreach($inventory as $item)<tr><td>{{ $item->title }}</td><td>{{ $item->total_copies }}</td><td>{{ $item->available_copies ?? 0 }}</td></tr>@endforeach</tbody></table></div>
    <div class="section-head" style="margin-top:55px"><h2 class="section-title">Overdue loans</h2></div>
    <div class="table-wrap"><table><thead><tr><th>Title</th><th>Member</th><th>Due date</th><th>Days late</th></tr></thead><tbody>@forelse($overdue as $loan)<tr><td>{{ $loan->title }}</td><td>{{ $loan->member }}</td><td>{{ \Carbon\Carbon::parse($loan->due_date)->format('d M Y') }}</td><td>{{ \Carbon\Carbon::parse($loan->due_date)->diffInDays(today()) }}</td></tr>@empty<tr><td colspan="4">No overdue loans.</td></tr>@endforelse</tbody></table></div>
</div></section>
@endsection
