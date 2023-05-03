$(document).ready(function () {
  $('#transactions-table').DataTable({
    "ajax": '/transaction/records',
    "bPaginate": true,
    "bLengthChange": false,
    "bFilter": false,
    "bInfo": false,
    "bAutoWidth": false,
    "scrollX": true,
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
