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
    ],
    "createdRow": function( row, data, dataIndex){
      if( data.status ==  `pending`){
        $(row).css({"background-color":"red"});
      }
    }
  });
});
