$(document).ready(function () {
  // DEPOSIT TABLE
  $('#player-transaction-table').DataTable({
    "bPaginate": true,
    "bLengthChange": false,
    "bFilter": false,
    "bInfo": false,
    "bAutoWidth": false,
    "scrollX": true,
    "order": [[0, 'desc']],
    "ajax": '/player/transaction/deposit',
    "columnDefs": [
      {"className": "dt-center", "targets": "_all"}
    ],
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
          return data.amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
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
    ],
  });

  // WITHDRAW TABLE
  $('#player-withdraw-table').DataTable({
    "bPaginate": true,
    "bLengthChange": false,
    "bFilter": false,
    "bInfo": false,
    "bAutoWidth": false,
    "scrollX": true,
    "order": [[0, 'desc']],
    "ajax": '/player/transaction/withdraw',
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
          return data.amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
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

$('[data-bs-toggle="tab"]').on('shown.bs.tab', function(e){
  $($.fn.dataTable.tables(true)).DataTable()
     .columns.adjust();
});
