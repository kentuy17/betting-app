@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <div class="card col-md-6">
        <div class="card-header">{{ __('x100 Exp.') }}</div>
        <video width="100%" controls>
          <source src="{{ asset('videos/e-sabong.mp4') }}" type="video/mp4">
          Your browser does not support the video tag.
        </video>
        <div class="card-body">
          @if (session('status'))
          <div class="alert alert-success" role="alert">
            {{ session('status') }}
          </div>
          @endif

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
