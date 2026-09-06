@extends('layouts.app')

@section('title', 'المكتبة')

@section('extra-styles')
<style>
    .lib-section{ margin-bottom: 28px; }
    .lib-section h3{ font-size: 15px; font-weight: 800; margin-bottom: 14px; color: var(--ink-900); }
    .lib-grid{
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 14px;
    }
    .lib-card{
        background: var(--paper-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .lib-card .icon{
        width: 40px; height: 40px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        background: var(--navy-900); color: #fff;
    }
    .lib-card .title{ font-weight: 700; font-size: 13.5px; color: var(--ink-900); }
    .lib-card .desc{ font-size: 12px; color: var(--ink-500); flex-grow: 1; }
    .lib-card .badge-general{
        font-size: 10px; font-weight: 700; padding: 2px 9px; border-radius: 20px;
        background:#fff3cd; color:#8a6d00; width: fit-content;
    }
    .lib-card a.btn-open{
        display: inline-block; text-align: center; background: var(--red-600); color: #fff;
        padding: 8px; border-radius: 8px; font-size: 12.5px; font-weight: 700; text-decoration: none;
    }
    .lib-card a.btn-open:hover{ background: var(--red-700); color: #fff; }
    .empty-state{ color: var(--ink-500); font-size: 13.5px; padding: 20px; text-align: center; }
</style>
@endsection

@section('content')
    <div class="topbar exams-topbar">
        <div>
            <h1>المكتبة</h1>
            <p>الفيديوهات والكتب المتاحة لمستواك الحالي</p>
        </div>
    </div>

    <div class="lib-section">
        <h3>الفيديوهات</h3>
        @if ($videos->isEmpty())
            <div class="empty-state">مفيش فيديوهات متاحة حاليًا.</div>
        @else
            <div class="lib-grid">
                @foreach ($videos as $video)
                    <div class="lib-card">
                        <div class="icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="m22 8-6 4 6 4V8Z"/><rect x="2" y="6" width="14" height="12" rx="2"/></svg>
                        </div>
                        @if ($video->is_general)
                            <span class="badge-general">عامة</span>
                        @endif
                        <div class="title">{{ $video->title }}</div>
                        @if ($video->description)
                            <div class="desc">{{ $video->description }}</div>
                        @endif
                        <a href="{{ route('materials.stream', $video) }}" target="_blank" class="btn-open">مشاهدة الفيديو</a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="lib-section">
        <h3>الكتب</h3>
        @if ($books->isEmpty())
            <div class="empty-state">مفيش كتب متاحة حاليًا.</div>
        @else
            <div class="lib-grid">
                @foreach ($books as $book)
                    <div class="lib-card">
                        <div class="icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/></svg>
                        </div>
                        @if ($book->is_general)
                            <span class="badge-general">عامة</span>
                        @endif
                        <div class="title">{{ $book->title }}</div>
                        @if ($book->description)
                            <div class="desc">{{ $book->description }}</div>
                        @endif
                        <a href="{{ route('materials.stream', $book) }}" target="_blank" class="btn-open">فتح الكتاب</a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
