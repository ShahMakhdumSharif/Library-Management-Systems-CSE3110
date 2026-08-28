@extends('layouts.public')
@section('title', 'Fines | Central Library')
@section('content')
<header class="page-head"><div class="container"><div class="eyebrow">Account charges</div><h1>Fines.</h1><p class="lede">Late returns are calculated automatically at Tk 5 per overdue day.</p></div></header>
<section class="section"><div class="container"><div class="table-wrap"><table><thead><tr><th>Title</th>@if(auth()->user()->isStaff())<th>Member</th>@endif<th>Amount</th><th>Status</th><th>Action</th></tr></thead><tbody>
@forelse($fines as $fine)<tr><td><strong>{{ $fine->title }}</strong></td>@if(auth()->user()->isStaff())<td>{{ $fine->member }}</td>@endif<td>Tk {{ number_format($fine->amount, 2) }}</td><td><span class="status {{ $fine->status }}">{{ $fine->status }}</span></td><td>@if($fine->status === 'unpaid')<form method="post" action="{{ route('fines.pay', $fine->id) }}">@csrf<button class="btn small">Mark paid</button></form>@else{{ $fine->paid_at ? \Carbon\Carbon::parse($fine->paid_at)->format('d M Y') : 'Paid' }}@endif</td></tr>
@empty<tr><td colspan="5">No fines found.</td></tr>@endforelse
</tbody></table></div><div class="pagination">{{ $fines->links() }}</div></div></section>
@endsection
