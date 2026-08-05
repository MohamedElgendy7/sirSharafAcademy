@extends('layouts.app')

@section('content')

<style>
  .student-page{
    height:100%;
    display:flex;
    flex-direction:column;
    overflow:hidden;
    box-sizing:border-box;
  }

  .student-page .page-head{
    flex-shrink:0;
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    margin-bottom:clamp(12px, 2vh, 22px);
  }
  .student-page .page-head h1{
    font-size:clamp(18px, 2vw, 24px);
    font-weight:800;
    color:var(--ink-900, #0e1930);
  }
  .student-page .page-head p{
    font-size:clamp(11px, 1vw, 13px);
    color:var(--ink-500, #5b6b8c);
    margin-top:4px;
  }

  .alert-success{
    flex-shrink:0;
    background:#eafbf1;
    color:#1a9c5c;
    border:1px solid #b9ecd0;
    border-radius:10px;
    padding:10px 16px;
    font-size:13px;
    font-weight:600;
    margin-bottom:14px;
  }

  .form-panel{
    flex:1;
    min-height:0;
    overflow-y:auto;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    background:var(--paper-card, #fff);
    border:1px solid rgba(0, 0, 0, 0.2);
    border-radius:var(--radius, 14px);
    padding:clamp(16px, 2.5vw, 32px);
    box-sizing:border-box;
  }

  .profile-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(160px, 1fr));
    gap:clamp(10px, 2.2vh, 22px) clamp(10px, 1.8vw, 22px);
    align-content:start;
    flex-shrink:0;
  }
  .profile-field .lbl{
    font-size:clamp(12px, 0.95vw, 13.5px);
    font-weight:700;
    color:var(--ink-900, #0e1930);
    margin-bottom:6px;
  }
  .profile-field input,
  .profile-field select{
    width:100%;
    font-size:clamp(13px, 1vw, 14.5px);
    font-weight:700;
    color:var(--ink-900, #0e1930);
    padding:clamp(9px, 1.2vh, 13px) 14px;
    border:1.5px solid rgba(0, 0, 0, 0.2);
    border-radius:10px;
    background:#fff;
    box-sizing:border-box;
    font-family:inherit;
  }
  .profile-field input:focus,
  .profile-field select:focus{
    outline:none;
    border-color:var(--red-600, #d81f26);
  }
  .profile-field .error{
    color:var(--red-600, #d81f26);
    font-size:11.5px;
    margin-top:4px;
  }

  .form-actions{
    flex-shrink:0;
    display:flex;
    gap:10px;
    padding-top:clamp(10px, 2vh, 18px);
    margin-top:clamp(10px, 2vh, 18px);
    border-top:1px solid var(--border, #e3e7f2);
  }
  .form-actions .btn{
    padding:clamp(10px, 1.4vh, 13px) 22px;
  }
</style>

<div class="student-page">

  <div class="page-head">
    <div>
      <h1>تعديل ملف الطالب</h1>
      <p>عدّل بيانات الطالب واحفظ التغييرات</p>
    </div>
    <a href="{{ route('students.index') }}" class="btn btn-ghost">رجوع للقائمة</a>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  <form method="POST" action="{{ route('students.update', $student) }}">
    @csrf
    @method('PUT')

    <div class="form-panel">

      <div class="profile-grid">

        <div class="profile-field">
          <div class="lbl">اسم الطالب</div>
          <input type="text" name="name" value="{{ old('name', $student->name) }}">
          @error('name') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="profile-field">
          <div class="lbl">رقم الهاتف</div>
          <input type="text" name="phone" value="{{ old('phone', $student->phone) }}">
          @error('phone') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="profile-field">
          <div class="lbl">رقم الواتساب</div>
          <input type="text" name="whatsapp" value="{{ old('whatsapp', $student->whatsapp) }}">
          @error('whatsapp') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="profile-field">
          <div class="lbl">البريد الإلكتروني</div>
          <input type="email" name="email" value="{{ old('email', $student->email) }}">
          @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="profile-field">
          <div class="lbl">السن</div>
          <input type="number" name="age" min="3" max="100" value="{{ old('age', $student->age) }}">
          @error('age') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="profile-field">
          <div class="lbl">النوع</div>
          <select name="gender">
            <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>ذكر</option>
            <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>أنثى</option>
          </select>
          @error('gender') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="profile-field">
          <div class="lbl">رقم ولي الأمر</div>
          <input type="text" name="guardian_phone" value="{{ old('guardian_phone', $student->guardian_phone) }}">
          @error('guardian_phone') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="profile-field">
          <div class="lbl">الفرع</div>
          <select name="branch">
            <option value="">— اختر —</option>
            <option value="cairo" {{ old('branch', $student->branch) == 'cairo' ? 'selected' : '' }}>القاهرة</option>
            <option value="tanta" {{ old('branch', $student->branch) == 'tanta' ? 'selected' : '' }}>طنطا</option>
            <option value="kafr_elsheikh" {{ old('branch', $student->branch) == 'kafr_elsheikh' ? 'selected' : '' }}>كفر الشيخ</option>
            <option value="online" {{ old('branch', $student->branch) == 'online' ? 'selected' : '' }}>أونلاين</option>
          </select>
          @error('branch') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="profile-field">
          <div class="lbl">المستوى</div>
          <input type="number" name="level" min="1" max="10" value="{{ old('level', $student->level) }}">
          @error('level') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="profile-field">
          <div class="lbl">الكورس المطلوب</div>
          <select name="course">
            <option value="">— اختر —</option>
            @foreach(['American Accent','Business English','General English','Conversation','IELTS preps','TOEFL preps'] as $courseOption)
              <option value="{{ $courseOption }}" {{ old('course', $student->course) == $courseOption ? 'selected' : '' }}>{{ $courseOption }}</option>
            @endforeach
          </select>
          @error('course') <div class="error">{{ $message }}</div> @enderror
        </div>

      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">حفظ التعديل</button>
      </div>
    </div>
  </form>
</div>

@endsection
