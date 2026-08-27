@extends('layouts.public')
@section('title', 'Reservations | Central Library')
@section('content')
<header class="page-head"><div class="container"><div class="eyebrow">Request queue</div><h1>Reservations.</h1></div></header>
<section class="section"><div class="container"><div class="table-wrap"><table><thead><tr><th>Title</th>@if(auth()->user()->isStaff())<th>Member</th>@endif<th>Requested</th><th>Status</th>@if(auth()->user()->isStaff())<th>Action</th>@endif</tr></thead><tbody>
@forelse($reservations as $reservation)<tr><td><strong>{{ $reservation->title }}</strong></td>@if(auth()->user()->isStaff())<td>{{ $reservation->member }}</td>@endif<td>{{ \Carbon\Carbon::parse($reservation->reserved_at)->format('d M Y, H:i') }}</td><td><span class="status {{ $reservation->status }}">{{ $reservation->status }}</span></td>@if(auth()->user()->isStaff())<td>@if($reservation->status === 'waiting')<form method="post" action="{{ route('reservations.complete', $reservation->id) }}">@csrf<button class="btn small">Notify member</button></form>@else—@endif</td>@endif</tr>
@empty<tr><td colspan="5">No reservations found.</td></tr>@endforelse
</tbody></table></div><div class="pagination">{{ $reservations->links() }}</div></div></section>
@endsection
