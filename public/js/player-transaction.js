$(document).ready(function () {
  // DEPOSIT TABLE
  $('#player-transaction-table').DataTable({
    "bPaginate": true,
    "bLengthChange": false,
    "bFilter": false,
    "bInfo": false,
    "bAutoWidth": false,
    "scrollX": true,
    "processing": true,
    "serverSide": true,
    "order": [[0, 'desc']],
    "ajax": '/player/transaction/deposit',
    "pageLength": 25,
    "columnDefs": [
      {
        "targets": [3],
        "className": 'dt-body-right',
      },
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
        "data": null,
        render: (data) => {
          return data.amount == '0.00' ? "N/A" : data.amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }
      },
      {
        "data": "created_at"
      },
      {
        "data": null,
        render: (data) => {
          return data.reference_code == null ? "N/A" : data.reference_code
        }
      },
      {
        "data": null,
        render: (data) => {
          return data.note == null ? "DONE" : data.note
        }
      },
    ],
    "createdRow": function( row, data, dataIndex){
      if( data.status ==  `failed` ) {
        $(row).find('td').eq(0).attr('style', 'color: red !important');
      }

      if( data.status ==  `completed` ) {
        $(row).find('td').eq(0).attr('style', 'color: green !important');
      }
    }
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
    "pageLength": 25,
    "processing": true,
    "serverSide": true,
    "columnDefs": [
      {
        "targets": [3],
        "className": 'dt-body-right',
      },
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
        "data": null,
        render: (data) => {
          return data.amount == '0.00' ? "N/A" : data.amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }
      },
      {
        "data": "created_at"
      },
      {
        "data": null,
        render: (data) => {
          return data.reference_code == null ? "N/A" : data.reference_code
        }
      },
      {
        "data": null,
        render: (data) => {
          return data.note == null ? "DONE" : data.note
        }
      }
    ],
    "createdRow": function( row, data, dataIndex){
      if( data.status ==  `failed` ) {
        $(row).find('td').eq(0).attr('style', 'color: red !important');
      }

      if( data.status ==  `completed` ) {
        $(row).find('td').eq(0).attr('style', 'color: green !important');
      }


    }
  });
});

$('[data-bs-toggle="tab"]').on('shown.bs.tab', function(e){
  $($.fn.dataTable.tables(true)).DataTable()
     .columns.adjust();
});
