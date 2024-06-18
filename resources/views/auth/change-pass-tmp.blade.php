@extends('layouts.app-sub')
@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-10 offset-2">
        <div class="panel panel-default">
          <h5>Change Password</h5>

          <div class="panel-body">
            @if (session('error'))
              <div class="alert alert-danger">
                {{ session('error') }}
              </div>
            @endif
            @if (session('success'))
              <div class="alert alert-success">
                {{ session('success') }}
              </div>
            @endif
            @if ($errors)
              @foreach ($errors->all() as $error)
                <div class="alert alert-danger">{{ $error }}</div>
              @endforeach
            @endif
            <form class="form-horizontal" method="POST" action="{{ route('changePasswordPost') }}">
              {{ csrf_field() }}

              <div class="form-group{{ $errors->has('new-password') ? ' has-error' : '' }}">
                <label for="new-password" class="col-md-4 control-label">New Password</label>

                <div class="col-md-6">
                  <input id="new-password" type="password" class="form-control" name="new-password" required>

                  @if ($errors->has('new-password'))
                    <span class="help-block">
                      <strong>{{ $errors->first('new-password') }}</strong>
                    </span>
                  @endif
                </div>
              </div>
              <br />
              <div class="form-group">
                <label for="new-password-confirm" class="col-md-4 control-label">Confirm New Password</label>

                <div class="col-md-6">
                  <input id="new-password-confirm" type="password" class="form-control" name="new-password_confirmation" required>
                </div>
              </div>
              <br />
              <div class="form-group">
                <div class="col-md-6 col-md-offset-4">
                  <button type="submit" class="btn btn-primary">
                    Change Password
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
