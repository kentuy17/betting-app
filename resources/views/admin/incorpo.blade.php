@extends('layouts.app')
@section('additional-styles')
<style>
  .select2-container {
    width: 100% !important;
  }
</style>

<!-- padidi -->
@endsection
@section('content')
<div class="container">
  <div class="row col-md-12 justify-content-center pt-3">
    <div class="col-lg-6 m-auto px-3">
      <div class="card card-info mb-3">
        <div class="card-header">
          <h3 class="card-title">ADD INCORPO</h3>
        </div>
        <form class="form-horizontal">
          <div class="card-body">
            <div class="form-group row">
              <label for="agent-username" class="col-sm-2 col-form-label">Username</label>
              <div class="col-sm-10">
                <input type="text" required class="form-control" id="corpo-id" placeholder="Corpo Username">
              </div>
            </div>
            <div class="form-group row">
              <label for="bracket-name" class="col-sm-2 col-form-label">Bracket</label>
              <div class="col-sm-10">
                <input type="text" required class="form-control" id="bracket-name" placeholder="Bracket Name">
              </div>
            </div>
          </div>
          <div class="card-footer">
            <button id="add-corpo" class="btn btn-primary float-right mb-2">Add</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="row col-md-12 justify-content-center">
    <div class="col-lg-12 m-auto px-3">
      <div class="card">
        <div class="card-body">
          <table class="table table-striped w-100" id="corpo-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>CORPO</th>
                <th>BRACKET</th>
                <th>COMMISSION</th>
                <th>PLAYERS</th>
                {{-- <th>LINK</th> --}}
                <th>START DATE</th>
                <th>ACTION</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('additional-scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="{{ asset('js/incorpo.js') }}" defer></script>
<script>
  $(function(){
    $('#corpo-id').select2({
      minimumInputLength: 2,
      ajax: {
        url: '/admin/non-agents',
        dataType: 'json',
        type: "GET",
        data: function (term) {
          return {
            term: term
          };
        },
        processResults: function (data) {
          return {
            results: $.map(data.data, function (item) {
              return {
                text: item.username,
                id: item.id
              }
            })
          };
        }
      }
    });
  });
</script>
@endsection