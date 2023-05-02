@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <div class="card col-md-12">
        <div class="card-header font-bold">{{ __('Profile') }}</div>
        <div class="card-body">
        <form action="{{url('profile/post/'.$user->id)}}" method="POST">
          @csrf
          @method('PUT')
          <li>
              <label for="fname">Name:</label>
              <input type="text" name="name" value="{{ $user->name }}" readonly>
          </li>
          <li>
              <label for="lname">Username:</label>
              <input type="text" name="username" value="{{ $user->username }}" readonly>
          </li>
          <li>
              <label for="lname">Cell Number:</label>
              <input type="text" name="phone_no" value="{{ $user->phone_no }}">
          </li>
          <li>
              <label for="lname">Credit Points:</label>
              <input type="text" name="username" value="{{ $user->points }}" readonly>
          </li>

          <button type="submit" class="btn btn-primary float-end">Update</button>
        </form>
        </div>
          
      </div>
    </div>
  </div>
</div>
@endsection

@section('additional-scripts')
<script src="{{ asset('js/userprofile.js') }}" defer></script>
@endsection
