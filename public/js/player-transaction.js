$(document).ready(function () {
  $('#player-transaction-table').DataTable({
    "bPaginate": true,
    "bLengthChange": false,
    "bFilter": false,
    "bInfo": false,
    "bAutoWidth": false,
    "scrollX": true,
    "order": [[0, 'desc']],
    "ajax": '/player/transaction',
    "columns": [
      {
        "data": "created_at"
      },
      {
        "data": null,
        render: (data) => {
          return data.action.toUpperCase();
        }
      },
      {
        "data": "user.username"
      },
      {
        "data": null,
        render: (data) => {
          return data.operator != null ? data.operator.username : "--";
        }
      },
      {
        "data": "mobile_number"
      },
      {
        "data": "amount"
      },
      {
        "data": null,
        render: (data) => {
          return data.status.toUpperCase();
        }
      }
    ]
  });
});
  