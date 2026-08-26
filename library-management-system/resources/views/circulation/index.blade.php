@extends('layouts.public')
@section('title', 'Circulation | Central Library')
@section('content')
<header class="page-head"><div class="container"><div class="eyebrow">Staff workspace</div><h1>Issue and return.</h1></div></header>
<section class="section soft"><div class="container">
    <form class="form-card" method="post" action="{{ route('circulation.issue') }}">@csrf
        <h2>Issue a book</h2>
        <div class="form-grid">
            <div class="field"><label for="user_id">Member</label><select id="user_id" name="user_id" required><option value="">Choose member</option>@foreach($members as $member)<option value="{{ $member->id }}">{{ $member->name }} · {{ $member->email }}</option>@endforeach</select></div>
            <div class="field"><label for="copy_id">Available copy</label><select id="copy_id" name="copy_id" required><option value="">Choose book copy</option>@foreach($copies as $copy)<option value="{{ $copy->id }}">{{ $copy->title }} · {{ $copy->barcode }} · {{ $copy->branch }}</option>@endforeach</select></div>
        </div><button class="btn" style="margin-top:20px">Issue for 14 days</button>
    </form>
</div></section>
<section class="section"><div class="container"><h2 class="section-title">Circulation history</h2>
<div class="table-wrap"><table><thead><tr><th>Book</th><th>Member</th><th>Issue / due</th><th>Status</th><th>Action</th></tr></thead><tbody>
@forelse($loans as $loan)<tr><td><strong>{{ $loan->title }}</strong><br><span class="muted">{{ $loan->barcode }}</span></td><td>{{ $loan->member }}</td><td>{{ \Carbon\Carbon::parse($loan->issue_date)->format('d M Y') }}<br><span class="muted">Due {{ \Carbon\Carbon::parse($loan->due_date)->format('d M Y') }}</span></td><td><span class="status {{ $loan->status }}">{{ $loan->status }}</span></td><td>@if($loan->status === 'issued')<form method="post" action="{{ route('circulation.return', $loan->id) }}">@csrf<button class="btn small">Record return</button></form>@else{{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->format('d M Y') : '—' }}@endif</td></tr>
@empty<tr><td colspan="5">No transactions yet.</td></tr>@endforelse
</tbody></table></div><div class="pagination">{{ $loans->links() }}</div></div></section>
@endsection
