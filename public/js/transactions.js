const TYPE = {
  pending: 'PENDING',
  completed: 'COMPLETED',
  failed: 'FAILED'
}

var transactionsTable = $('#deposit-trans-table');
transactionsTable.DataTable({
  "ajax": '/transaction/deposits',
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

$('#deposit-trans-table tbody').on('click', 'td.dt-control', function () {
  var tr = $(this).closest('tr');
  var row = transactionsTable.DataTable().row(tr);

  if (row.child.isShown()) {
    row.child.hide();
    tr.removeClass('shown');
  } 
  else {
    row.child(format(row.data())).show();
    tr.addClass('shown');
  }
});

transactionsTable.on('click', 'tbody td .view', async function() {
  clearFields();
  var tr = $(this).closest('tr');
  var row = transactionsTable.DataTable().row(tr);
  $('#modal-center').modal('show')
  $('.modal-title').text(row.data().action.toUpperCase())
  $('input#trans-id').val($(this).data('id'));

  let storage = $('#trans-receipt').data('storage');
  if(row.data().filename) {
    $('#trans-receipt').attr('src', storage+'/'+row.data().filename);
  }

  if(row.data().status == 'completed') {
    $('input[type="submit"]').prop('disabled', true)
      .addClass('disabled');
  } else {
    $('input[type="submit"]').prop('disabled', false)
      .removeClass('disabled');
  }
})

$('#deposit-form').on('submit', function(e) {
  e.preventDefault();
  axios.post('/transaction/deposit', {
    id: $('#trans-id').val(),
    amount: $('#trans-pts').val(),
    ref_code: $('#ref-code').val()
  }).then((res) => {
    Swal.fire({
      icon: 'success',
      confirmButtonColor: 'green',
      title: res.data.msg,
      timer: 1500
    }).then(() =>  {
      $('#modal-center').modal('hide')
      clearFields();
    });
    transactionsTable.DataTable().ajax.reload();
  })
  .catch((err) => {
    console.log(err);
  })

})

function clearFields() {
  $('#trans-pts').val(''), $('#ref-code').val('')
}


$('[data-dismiss="modal"]').on('click', function() {
  $('#modal-center').modal('hide');
})

