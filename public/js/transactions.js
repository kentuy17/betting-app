const TYPE = {
  pending: 'PENDING',
  completed: 'COMPLETED',
  failed: 'FAILED'
}

var transactionsTable = $('#transactions-table');
transactionsTable.DataTable({
  "ajax": '/transaction/records',
  "bPaginate": true,
  "bLengthChange": false,
  "bFilter": false,
  "bInfo": false,
  "bAutoWidth": false,
  "scrollX": true,
  "columns": [
    {
      "data": "id"
    },
    {
      "data": null,
      render: (data, type, row, meta) => {
        return data.action.toUpperCase();
      }
    },
    {
      "data": "user.username"
    },
    {
      "data": "operator.username"
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
    {
      "data": null,
      render: (data, type, row, meta) => {
        return data.status != `pending` ? `<i class="fa-solid check"></i>` : 
        `<a href="javascript:void(0)" data-id="${row.id}" class="btn btn-link text-secondary btn-icon btn-sm approve"><i class="fa fa-check-square" aria-hidden="true"></i></a>`
      }
    },
  ],
  "createdRow": function( row, data, dataIndex){
    if( data.status ==  `pending`){
      $(row).css({"background-color":"var(--bs-red)"});
    }
  }
});

transactionsTable.on('click', 'tbody td .approve', async function() {
  var id = $(this).data('id');
  console.log(id);
  transactionsTable.DataTable().ajax.reload();
})

