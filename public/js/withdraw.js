var withdrawTable = $('#withdraw-trans-table');
withdrawTable.DataTable({
  "ajax": '/transaction/withdrawals',
  "bPaginate": true,
  "bLengthChange": false,
  "bFilter": false,
  "bInfo": false,
  "bAutoWidth": false,
  "scrollX": true,
  "columns": [
    {
      className: 'dt-control',
      orderable: false,
      data: null,
      defaultContent: '',
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
      "data": null,
      render: (data) => {
        return data.status.toUpperCase();
      }
    },
    {
      "data": null,
      render: (data) => {
        return `<a href="javascript:void(0)" data-id="${data.id}" class="btn btn-link text-primary btn-icon btn-sm view">
          <i class="fa-solid fa-circle-info"></i></a>`;
      }
    },
  ],
  "createdRow": function( row, data, dataIndex){
    if( data.status ==  `pending`){
      $(row).css({"background-color":"var(--bs-red)"});
    }
  }
});

function format(d) {
  // `d` is the original data object for the row
  return (
    `<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">
      <tr>
        <td>PLAYER:</td>
        <td>${d.user.username}</td>
      </tr>
      <tr>
        <td>MOBILE#:</td>
        <td>${d.mobile_number}</td>
      </tr>
      <tr>
        <td>AMOUNT:</td>
        <td>${d.amount}</td>
      </tr>
    </table>`
  );
}

$('#withdraw-trans-table tbody').on('click', 'td.dt-control', function () {
  var tr = $(this).closest('tr');
  var row = withdrawTable.DataTable().row(tr);

  if (row.child.isShown()) {
    row.child.hide();
    tr.removeClass('shown');
  } 
  else {
    console.log(row.data());
    row.child(format(row.data())).show();
    tr.addClass('shown');
  }
});

withdrawTable.on('click', 'tbody td .view', async function() {
  var id = $(this).data('id');
  var tr = $(this).closest('tr');
  var row = withdrawTable.DataTable().row(tr);
  // console.log(id);
  $('#modal-center').modal('show')
  // $('.modal-body').text(id)
  $('.modal-title').text(row.data().action.toUpperCase())
  let storage = $('#trans-receipt').data('storage');
  let imgSrc = '../img/image-not-found.png';

  if(row.data().filename) {
    imgSrc = row.data().filename;
  } 

  $('#trans-receipt').attr('src', storage+'/'+imgSrc);
  

  // withdrawTable.DataTable().ajax.reload();
})

$('[data-dismiss="modal"]').on('click', function() {
  $('#modal-center').modal('hide');
})

