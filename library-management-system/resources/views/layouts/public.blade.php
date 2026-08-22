<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Central Library')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=libre-franklin:400,500,600,700,800|source-serif-4:600,700" rel="stylesheet">
    <style>
        :root{--crimson:#a51c30;--crimson-dark:#7d1424;--ink:#1e1e1e;--muted:#5b5b5b;--line:#d6d3ce;--paper:#f7f5f2;--cream:#eeeae4;--white:#fff;--green:#1f6b4f}
        *{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;color:var(--ink);background:var(--white);font-family:"Libre Franklin",Arial,sans-serif;line-height:1.55}a{color:inherit;text-decoration:none}button,input,select,textarea{font:inherit}.container{width:min(1180px,calc(100% - 40px));margin-inline:auto}.skip{position:absolute;left:-999px}.skip:focus{left:16px;top:10px;z-index:99;padding:10px;background:#fff}
        .utility{background:#111;color:#fff;font-size:.78rem}.utility .container{display:flex;justify-content:flex-end;gap:24px;padding:10px 0}.utility a{border-bottom:1px solid transparent}.utility a:hover{border-color:#fff}
        .header{border-bottom:1px solid var(--line);background:#fff}.header-row{display:flex;align-items:center;justify-content:space-between;gap:28px;min-height:104px}.brand{display:flex;align-items:center;gap:14px;font:700 1.6rem "Source Serif 4",Georgia,serif}.brand-mark{display:grid;width:53px;height:60px;place-items:center;color:#fff;background:var(--crimson);font:700 1.65rem Georgia,serif;clip-path:polygon(0 0,100% 0,100% 84%,50% 100%,0 84%)}.nav{display:flex;align-items:center;flex-wrap:wrap;gap:24px;font-weight:700;font-size:.9rem}.nav a{padding:10px 0;border-bottom:3px solid transparent}.nav a:hover,.nav a[aria-current="page"]{color:var(--crimson);border-color:var(--crimson)}.nav .button{padding:10px 16px;color:#fff;background:var(--crimson);border:1px solid var(--crimson)}.nav form{margin:0}.link-button{padding:10px 0;border:0;background:none;color:inherit;font-weight:700;cursor:pointer}
        main{min-height:65vh}.flash{margin-top:22px;padding:14px 18px;border-left:5px solid var(--green);background:#eef7f2}.flash.error{border-color:var(--crimson);background:#fff0f1}.hero{display:grid;min-height:590px;background-image:linear-gradient(90deg,rgba(247,245,242,.96) 0%,rgba(247,245,242,.88) 42%,rgba(247,245,242,.2) 100%),url('{{ asset('images/hero-library.avif') }}');background-position:center;background-repeat:no-repeat;background-size:100% 100%}.hero-copy{display:flex;flex-direction:column;justify-content:center;max-width:900px;padding:70px max(40px,calc((100vw - 1180px)/2))}.eyebrow{margin-bottom:14px;color:var(--crimson);font-size:.78rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase}.display{max-width:760px;margin:0;font:700 clamp(3.2rem,7vw,6.4rem)/.94 "Source Serif 4",Georgia,serif;letter-spacing:-.04em}.hero p,.lede{max-width:710px;color:var(--muted);font-size:1.1rem}.search-panel{margin-top:32px}.search-panel label{display:block;margin-bottom:9px;font-weight:800}.search-row{display:grid;grid-template-columns:minmax(0,1fr) minmax(150px,180px) auto;max-width:860px;background:#fff;box-shadow:0 8px 28px #0001}.search-row input,.search-row select{min-width:0;height:48px;padding:0 16px;border:1px solid var(--line);font-size:1rem;background:#fff}.search-row button,.btn{display:inline-flex;align-items:center;justify-content:center;height:48px;padding:0 18px;border:1px solid var(--crimson);background:var(--crimson);color:#fff;font-weight:800;cursor:pointer;line-height:1}.toolbar .btn,.toolbar .filter input,.toolbar .filter select,.search-row button{height:48px}.btn:hover,.search-row button:hover{background:var(--crimson-dark)}.btn.secondary{background:#fff;color:var(--crimson)}.btn.dark{border-color:#222;background:#222}.btn.small{min-height:36px;padding:0 12px;font-size:.78rem}.btn.danger{border-color:#8e1828;background:#fff;color:#8e1828}
        .section{padding:72px 0}.section.soft{background:var(--paper)}.section-title{max-width:760px;margin:0 0 14px;font:700 clamp(2rem,4vw,3.5rem)/1.05 "Source Serif 4",Georgia,serif}.section-head{display:flex;align-items:end;justify-content:space-between;gap:24px;margin-bottom:30px}.card-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}.card{display:flex;flex-direction:column;min-height:250px;padding:28px;border-top:6px solid var(--crimson);background:#fff;box-shadow:0 5px 20px #0000000d}.card h3{margin:0 0 9px;font:700 1.45rem "Source Serif 4",Georgia,serif}.card p{color:var(--muted)}.card .meta{margin-top:auto}.meta,.muted{color:var(--muted);font-size:.86rem}.tag,.status{display:inline-block;padding:4px 9px;background:var(--cream);font-size:.72rem;font-weight:800;text-transform:uppercase}.status.available,.status.active,.status.paid,.status.returned{color:var(--green);background:#e8f4ee}.status.issued,.status.waiting,.status.unpaid{color:#8a4c00;background:#fff3db}
        .page-head{padding:58px 0 34px;border-bottom:1px solid var(--line);background:var(--paper)}.page-head h1{margin:0;font:700 clamp(2.4rem,5vw,4.7rem)/1 "Source Serif 4",Georgia,serif}.toolbar{display:flex;align-items:end;justify-content:space-between;gap:20px;margin-bottom:28px}.filter{display:flex;gap:0;max-width:none;flex:1}.filter input,.filter select{width:100%;min-height:48px;padding:0 14px;border:1px solid var(--line);background:#fff}.collection-note{margin:-8px 0 26px;padding:16px 18px;border:1px solid var(--line);background:linear-gradient(180deg,#fff, #faf8f5);color:var(--muted);box-shadow:0 5px 18px #00000008}.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:var(--line);border:1px solid var(--line);margin-bottom:34px}.stat{padding:24px;background:#fff}.stat strong{display:block;font:700 2.2rem "Source Serif 4",Georgia,serif}.stat span{color:var(--muted);font-size:.82rem;text-transform:uppercase}
        .table-wrap{overflow:auto;border:1px solid var(--line)}table{width:100%;border-collapse:collapse;background:#fff}th,td{padding:15px 16px;border-bottom:1px solid var(--line);text-align:left;vertical-align:top}th{background:var(--paper);font-size:.76rem;letter-spacing:.04em;text-transform:uppercase}tr:last-child td{border-bottom:0}.actions{display:flex;flex-wrap:wrap;gap:7px}.inline-form{display:flex;align-items:center;gap:7px}.inline-form select{min-height:36px}
        .form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:20px}.field.full{grid-column:1/-1}.field label{display:block;margin-bottom:7px;font-weight:800}.field input,.field select,.field textarea{width:100%;min-height:49px;padding:11px 13px;border:1px solid #aaa;background:#fff}.field textarea{min-height:120px;resize:vertical}.error-text{margin-top:5px;color:var(--crimson);font-size:.82rem}.form-card{max-width:900px;padding:34px;border-top:6px solid var(--crimson);background:#fff;box-shadow:0 6px 22px #0001}.auth{display:grid;grid-template-columns:1fr minmax(350px,500px);gap:70px;align-items:start;padding:75px 0}.auth h1{margin:0;font:700 clamp(2.8rem,5vw,5rem)/1 "Source Serif 4",Georgia,serif}.pagination{margin-top:24px}.pagination nav>div:first-child{display:none}.pagination nav>div:last-child{display:flex;justify-content:space-between;gap:16px}.pagination svg{width:18px}
        .footer{padding-top:55px;background:#151515;color:#fff}.footer-grid{display:grid;grid-template-columns:1.4fr repeat(3,1fr);gap:45px;padding-bottom:45px}.footer h2,.footer h3{font-family:"Source Serif 4",Georgia,serif}.footer a{display:block;margin:9px 0;color:#d3d3d3}.footer-bottom{padding:18px 0;border-top:1px solid #444;color:#aaa;font-size:.78rem}
        @media(max-width:900px){.header-row{align-items:flex-start;flex-direction:column;padding:24px 0}.hero-copy{padding:70px 24px}.card-grid,.stats,.footer-grid{grid-template-columns:repeat(2,1fr)}.auth{grid-template-columns:1fr;gap:35px}}@media(max-width:620px){.container{width:min(100% - 26px,1180px)}.utility{display:none}.nav{gap:13px}.card-grid,.stats,.footer-grid,.form-grid{grid-template-columns:1fr}.field.full{grid-column:auto}.display{font-size:3.2rem}.toolbar,.section-head{align-items:stretch;flex-direction:column}.search-row{grid-template-columns:1fr}.search-row input,.search-row select{height:44px}.search-row button,.toolbar .btn,.toolbar .filter input,.toolbar .filter select{min-height:44px}}
    </style>
</head>
<body>
<a class="skip" href="#content">Skip to main content</a>
<div class="utility"><div class="container"><a href="{{ route('books.index') }}">Search Catalog</a><a href="{{ route('home') }}#services">Library Services</a><a href="{{ route('home') }}#hours">Hours & Locations</a></div></div>
<header class="header">
    <div class="container header-row">
        <a class="brand" href="{{ route('home') }}" aria-label="Central Library home"><span class="brand-mark">C</span><span>Central Library</span></a>
        <nav class="nav" aria-label="Main navigation">
            <a href="{{ route('books.index') }}" @if(request()->routeIs('books.*')) aria-current="page" @endif>Collections</a>
            @auth
                <a href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>Dashboard</a>
                <a href="{{ route('reservations') }}">Reservations</a>
                <a href="{{ route('fines') }}">Fines</a>
                @if(auth()->user()->isStaff())
                    <a href="{{ route('circulation') }}">Circulation</a>
                    <a href="{{ route('reports') }}">Reports</a>
                @endif
                @if(auth()->user()->isAdmin())<a href="{{ route('members') }}">Members</a>@endif
                <a href="{{ route('notifications') }}">Alerts</a>
                <form action="{{ route('logout') }}" method="post">@csrf<button class="link-button" type="submit">Log out</button></form>
            @else
                <a href="{{ route('login') }}">Log in</a>
                <a class="button" href="{{ route('register') }}">Join the library</a>
            @endauth
        </nav>
    </div>
</header>
<main id="content">
    @if(session('success'))<div class="container flash">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="container flash error">{{ session('error') }}</div>@endif
    @yield('content')
</main>
<footer class="footer">
    <div class="container footer-grid">
        <section><h2>Central Library</h2><p class="muted">Discover, borrow, and create knowledge across every branch.</p></section>
        <section><h3>Explore</h3><a href="{{ route('books.index') }}">Collections</a><a href="{{ route('home') }}#services">Services</a></section>
        <section id="hours"><h3>Visit</h3><a href="#">Main Branch</a><a href="#">Science Branch</a><a href="#">09:00–20:00</a></section>
        <section><h3>Account</h3>@auth<a href="{{ route('dashboard') }}">Dashboard</a>@else<a href="{{ route('login') }}">Log in</a><a href="{{ route('register') }}">Register</a>@endauth</section>
    </div>
    <div class="container footer-bottom">© {{ date('Y') }} Central Library · CSE 3110 Database Systems Lab</div>
</footer>
</body>
</html>
