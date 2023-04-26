$(document).ready(function () {
    $('#bethistory-table').DataTable({
      "bPaginate": true,
      "bLengthChange": false,
      "bFilter": false,
      "bInfo": false,
      "bAutoWidth": false,
      "ajax": '/bet/history',
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
  