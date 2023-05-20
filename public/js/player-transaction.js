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
        "data": null,
        render: (data) => {
          return data.status.toUpperCase();
        }
      },
      {
        "data": "outlet"
      },
      {
        "data": "mobile_number"
      },
      {
        "data": "reference_code"
      },      
      {
        "data": null,
        render: (data) => {
          return data.amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
      },
      {
        "data": "created_at"
      },
      {
        "data": "completed_at"
      },
      {
        "data": "note",
      }
    ]
  });
});
  