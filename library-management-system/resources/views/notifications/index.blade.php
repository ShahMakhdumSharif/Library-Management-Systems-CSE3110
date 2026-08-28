@extends('layouts.public')
@section('title', 'Notifications | Central Library')
@section('content')
<header class="page-head"><div class="container"><div class="eyebrow">Account activity</div><h1>Notifications.</h1></div></header>
<section class="section"><div class="container"><div class="table-wrap"><table><thead><tr><th>Type</th><th>Message</th><th>Date</th><th></th></tr></thead><tbody>
@forelse($notifications as $notice)<tr style="{{ $notice->is_read ? '' : 'font-weight:700;background:#fff8e9' }}"><td><span class="tag">{{ $notice->type }}</span></td><td>{{ $notice->message }}</td><td>{{ \Carbon\Carbon::parse($notice->created_at)->format('d M Y, H:i') }}</td><td>@if(!$notice->is_read)<form method="post" action="{{ route('notifications.read', $notice->id) }}">@csrf<button class="btn small secondary">Mark read</button></form>@endif</td></tr>
@empty<tr><td colspan="4">No notifications yet.</td></tr>@endforelse
</tbody></table></div><div class="pagination">{{ $notifications->links() }}</div></div></section>
@endsection
