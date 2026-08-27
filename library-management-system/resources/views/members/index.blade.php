@extends('layouts.public')
@section('title', 'Members | Central Library')
@section('content')
<header class="page-head"><div class="container"><div class="eyebrow">Administration</div><h1>Users and access.</h1></div></header>
<section class="section"><div class="container"><div class="table-wrap"><table><thead><tr><th>User</th><th>Joined</th><th>Role and status</th><th>Action</th></tr></thead><tbody>
@foreach($members as $member)<tr><td><strong>{{ $member->name }}</strong><br><span class="muted">{{ $member->email }}</span></td><td>{{ \Carbon\Carbon::parse($member->created_at)->format('d M Y') }}</td><td><span class="tag">{{ $member->role_name }}</span> <span class="status {{ $member->status }}">{{ $member->status }}</span></td><td><form class="inline-form" method="post" action="{{ route('members.update', $member->id) }}">@csrf @method('PUT')<select name="role_id">@foreach($roles as $role)<option value="{{ $role->id }}" @selected($role->id == $member->role_id)>{{ $role->role_name }}</option>@endforeach</select><select name="status"><option value="active" @selected($member->status === 'active')>Active</option><option value="suspended" @selected($member->status === 'suspended')>Suspended</option></select><button class="btn small">Save</button></form></td></tr>@endforeach
</tbody></table></div><div class="pagination">{{ $members->links() }}</div></div></section>
@endsection
