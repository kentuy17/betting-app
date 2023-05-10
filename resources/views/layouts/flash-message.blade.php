@foreach (['danger', 'warning', 'success', 'info'] as $msg)
  @if(session()->has($msg))
  <div class="fade-message alert alert-{{ $msg }}">
    <strong>{{ session()->get($msg) }}</strong>
  </div>
  @endif
@endforeach

