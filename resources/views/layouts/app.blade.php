<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Sir Sharaf Academy — CRM')</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;700;800&family=IBM+Plex+Sans+Arabic:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root{
    --navy-950:#081533;
    --navy-900:#0b1f45;
    --navy-850:#0f2a5c;
    --navy-800:#15356e;
    --navy-700:#1e4483;
    --red-600:#d81f26;
    --red-700:#b5171d;
    --red-glow: rgba(216,31,38,.35);
    --white:#ffffff;
    --paper:#f2f4f9;
    --paper-card:#ffffff;
    --ash-300:#c4cfe6;
    --ash-500:#8fa0c7;
    --ink-900:#0e1930;
    --ink-500:#5b6b8c;
    --border:#e3e7f2;
    --radius:14px;
  }
  *{box-sizing:border-box; margin:0; padding:0;}
  html{font-size:16px; -webkit-text-size-adjust:100%; text-size-adjust:100%;}
  body{
    font-family:'IBM Plex Sans Arabic','Segoe UI',Tahoma,Arial,sans-serif;
    background:var(--paper);
    color:var(--ink-900);
    display:flex;
    height:100vh;
    overflow:hidden;
  }
  h1,h2,h3, .brand-name, .nav-title{font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;}
  button, input, select, textarea{font:inherit;}

  /* ============ SIDEBAR ============ */
  .sidebar{
    width:288px;
    min-width:288px;
    height:100vh;
    background: linear-gradient(180deg, var(--navy-950) 0%, var(--navy-900) 55%, var(--navy-850) 100%);
    color:var(--white);
    display:flex;
    flex-direction:column;
    position:relative;
    box-shadow: 6px 0 24px rgba(8,21,51,.18);
    z-index:5;
  }
  .sidebar::before{
    content:"";
    position:absolute;
    inset:0;
    background: radial-gradient(120px 200px at 85% 8%, var(--red-glow), transparent 70%);
    pointer-events:none;
  }
  .brand{
    display:flex; align-items:center; gap:12px;
    padding:22px 20px 18px 20px;
    border-bottom:1px solid rgba(255,255,255,.08);
    position:relative; z-index:1;
  }
  .brand-flame{width:38px; height:38px; flex-shrink:0; position:relative;}
  .brand-text{display:flex; flex-direction:column; line-height:1.15;}
  .brand-eyebrow{font-size:10px; letter-spacing:.12em; color:var(--ash-500); font-weight:500;}
  .brand-name{font-size:16.5px; font-weight:800; color:var(--white);}
  .brand-name span{color:var(--red-600);}
  .brand-slogan{font-size:9.5px; color:var(--ash-500); margin-top:2px; font-family:'IBM Plex Sans Arabic',sans-serif;}

  .sidebar-scroll{flex:1; overflow-y:auto; padding:14px 12px 14px 12px; position:relative; z-index:1;}
  .sidebar-scroll::-webkit-scrollbar{width:5px;}
  .sidebar-scroll::-webkit-scrollbar-thumb{background:rgba(255,255,255,.14); border-radius:6px;}

  .search-box{
    margin:4px 8px 18px 8px; display:flex; align-items:center; gap:8px;
    background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.09);
    border-radius:10px; padding:9px 12px; color:var(--ash-300); font-size:12px;
  }
  .search-box svg{width:15px;height:15px; flex-shrink:0; opacity:.75;}
  .search-box input{background:transparent; border:none; outline:none; color:var(--white); font-family:inherit; font-size:12px; width:100%;}
  .search-box input::placeholder{color:var(--ash-500);}
  .kbd{font-size:9.5px; color:var(--ash-500); border:1px solid rgba(255,255,255,.15); border-radius:5px; padding:1px 5px;}

  .nav-group{margin-bottom:6px;}
  .nav-title{font-size:10px; font-weight:700; letter-spacing:.09em; color:var(--ash-500); padding:14px 12px 6px 12px; text-transform:uppercase;}
  .nav-item{
    display:flex; align-items:center; gap:11px; padding:9px 12px; margin:1px 4px;
    border-radius:10px; color:var(--ash-300); font-size:12.5px; font-weight:500;
    cursor:pointer; position:relative; transition:background .15s ease, color .15s ease; text-decoration:none;
  }
  .nav-item svg{width:17px; height:17px; flex-shrink:0; opacity:.85;}
  .nav-item .label{flex:1;}
  .nav-item .count{font-size:10px; font-weight:700; background:rgba(255,255,255,.08); color:var(--ash-300); padding:1px 7px; border-radius:20px;}
  .nav-item:hover{background:rgba(255,255,255,.06); color:var(--white);}
  .nav-item.active{background: linear-gradient(90deg, var(--red-600), var(--red-700)); color:var(--white); box-shadow: 0 4px 14px var(--red-glow);}
  .nav-item.active svg{opacity:1;}
  .nav-item.active .count{background:rgba(255,255,255,.22); color:var(--white);}
  .nav-item.active::before{
    content:""; position:absolute; right:-4px; top:50%; transform:translateY(-50%);
    width:6px; height:6px; background:var(--white); border-radius:50% 50% 50% 0; rotate:45deg;
  }
  .nav-item.has-badge .label::after{
    content:"جديد"; font-size:9px; font-weight:700; background:var(--red-600); color:var(--white);
    padding:1px 6px; border-radius:6px; margin-inline-start:6px; vertical-align:middle;
  }

  .sidebar-footer{border-top:1px solid rgba(255,255,255,.08); padding:14px 16px 16px 16px; position:relative; z-index:1;}
  .plan-card{background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.08); border-radius:12px; padding:12px; margin-bottom:12px;}
  .plan-card .t{font-size:11px; font-weight:700; color:var(--white);}
  .plan-card .d{font-size:10px; color:var(--ash-500); margin-top:2px; line-height:1.5;}
  .plan-bar{height:5px; background:rgba(255,255,255,.1); border-radius:6px; margin-top:9px; overflow:hidden;}
  .plan-bar i{display:block; height:100%; width:68%; background:linear-gradient(90deg,var(--red-600),#ff5c5c); border-radius:6px;}

  .user-row{display:flex; align-items:center; gap:10px; cursor:pointer;}
  .avatar{
    width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,var(--red-600),var(--navy-700));
    display:flex; align-items:center; justify-content:center; font-weight:800; font-size:12px; color:var(--white);
    border:2px solid rgba(255,255,255,.15);
  }
  .user-meta{flex:1; line-height:1.25;}
  .user-meta .name{font-size:12px; font-weight:700; color:var(--white);}
  .user-meta .role{font-size:10px; color:var(--ash-500);}
  .user-row svg{width:16px;height:16px; color:var(--ash-500);}

  /* ============ MAIN ============ */
  .main{flex:1; overflow-y:auto; padding:26px 30px;}
  .topbar{display:flex; align-items:center; justify-content:space-between; margin-bottom:22px; flex-wrap:wrap; gap:12px;}
  .topbar h1{font-size:21px; font-weight:800; color:var(--ink-900);}
  .topbar p{font-size:12.5px; color:var(--ink-500); margin-top:3px;}
  .top-actions{display:flex; gap:10px;}
  .btn{display:flex; align-items:center; gap:7px; padding:9px 16px; border-radius:10px; font-size:13px; font-weight:700; cursor:pointer; border:none; font-family:inherit;}
  .btn-primary{background:var(--red-600); color:#fff;}
  .btn-ghost{background:#fff; color:var(--ink-900); border:1px solid var(--border);}

  .stat-grid{display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:22px;}
  .stat-card{background:var(--paper-card); border:1px solid var(--border); border-radius:var(--radius); padding:16px;}
  .stat-card .top{display:flex; justify-content:space-between; align-items:flex-start;}
  .stat-icon{width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;}
  .stat-card .val{font-size:22px; font-weight:800; margin-top:10px; color:var(--ink-900);}
  .stat-card .lbl{font-size:12px; color:var(--ink-500); margin-top:2px;}
  .stat-card .delta{font-size:11px; font-weight:700; margin-top:8px;}
  .delta.up{color:#1a9c5c;}

  .panels{display:grid; grid-template-columns:1.5fr 1fr; gap:16px;}
  .panel{background:var(--paper-card); border:1px solid var(--border); border-radius:var(--radius); padding:18px;}
  .panel h3{font-size:14.5px; font-weight:800; margin-bottom:14px;}
  .row{display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid var(--border); font-size:13px;}
  .row:last-child{border:none;}
  .row .dot{width:8px;height:8px;border-radius:50%;}
  .row .name{font-weight:700; flex:1;}
  .row .sub{color:var(--ink-500); font-size:11.5px;}
  .tag{font-size:10.5px; font-weight:700; padding:3px 9px; border-radius:20px;}

  @media (max-width: 960px){
    .sidebar{position:fixed; inset-inline-start:0; transform:translateX(100%); transition:transform .2s ease;}
    .stat-grid{grid-template-columns:repeat(2,1fr);}
    .panels{grid-template-columns:1fr;}
  }
</style>
@yield('extra-styles')
</head>
<body>

  @include('layouts.partials.sidebar')

  <main class="main">
    @yield('content')
  </main>

</body>
</html>
