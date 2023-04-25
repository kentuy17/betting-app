$(document).ready(function () {
  $('#transactions-table').DataTable({
    "bPaginate": true,
    "bLengthChange": false,
    "bFilter": false,
    "bInfo": false,
    "bAutoWidth": false,
    "ajax": '/transaction/records',
    "columns": [
      {
        "data": "user.id"
      },
      {
        "data": "user.name"
      },
      {
        "data": "amount"
      },
      {
        "data": "mobile_number"
      },
      {
        "data": "created_at"
      },
      {
        "data": "status"
      },
    ]
  });
});
