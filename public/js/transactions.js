$(document).ready(function () {
  $('#transactions-table').DataTable({
    ajax: '/transaction/records',
    columns: [
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
