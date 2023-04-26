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
          "data": "fight_no"
        },
        {
          "data": "user_id"
        },
        {
          "data": "side"
        },
        {
          "data": "betamount"
        },
        {
          "data": "winamount"
        },
        {
          "data": "percent"
        },
        {
          "data": "created_at"
        },
        {
          "data": "status"
        }
      ]
    });
  });
  