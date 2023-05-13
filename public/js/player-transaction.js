$(document).ready(function () {
    $('#player-transaction-table').DataTable({
      "bPaginate": true,
      "bLengthChange": false,
      "bFilter": false,
      "bInfo": false,
      "bAutoWidth": false,
      "scrollX": true,
      "ajax": '/player/transaction',
      "columns": [
        {
            "data": "created_at"
        },
        {
          "data": "action"
        },
        {
            "data": "user.username"
        },
        {
          "data": "operator.username"
        },
        {
            "data": "mobile_number"
        },
        {
            "data": "amount"
        },
        {
            "data": "status"
        }
      ]
    });
  });
  