const TYPE = {
    pending: 'PENDING',
    completed: 'COMPLETED',
    failed: 'FAILED'
  }

var transactionsTable = $('#refill-trans-table');
var pendingCount = 0;

transactionsTable.DataTable({
  "ajax": '/transaction/refill',
  "bPaginate": true,
  "bLengthChange": false,
  "bFilter": false,
  "bInfo": false,
  "bAutoWidth": false,
  "scrollX": true,
  "columnDefs": [
    {
      "targets": [4],
      "className": 'dt-body-right',
    },
  ],
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
      "data": null,
      render: (data) => {
        return data.operator != null ? data.operator.username : "--";
      }
    },
    {
      "data": null,
      render: (data) => {
        return data.amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
      },
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
      pendingCount++;
    }

    if(pendingCount > 0) {
      $('#badge-deposit').show().text(pendingCount);
    } else {
      $('#badge-deposit').hide().text(pendingCount);
    }
  }
});

$('#refill-form').on('click', 'input[type="submit"]',function(e) {
  e.preventDefault();
  axios.post('/transaction/refill', {
    id: $('#trans-id').val(),
    amount: $('#trans-pts').val().replace(",", ""),
    ref_code: $('#ref-code').val(),
    action: $('#trans-action').val(),
    note: $('#trans-note').val(),
  })
  .then((res) => {
    Swal.fire({
      icon: 'success',
      confirmButtonColor: 'green',
      title: res.data.msg,
      timer: 1500
    })
    .then(() =>  {
      console.log(res);
      $('#modal-center').modal('hide')
      $('#operator-pts').html(res.data.points)
      clearFields();
    });

    transactionsTable.DataTable().ajax.reload();
    pendingCount = 0;
  })
  .catch((err) => {
    Swal.fire({
      icon: 'error',
      confirmButtonColor: 'red',
      title: err.response.data.msg,
      timer: 1500
    });    })

})

$('#trans-action').on('change', function(e) {
  e.preventDefault();
  let action = $(this).val();
  if(action == 'reject') {
    $('#trans-pts,#ref-code').prop('disabled',true);
    $('#trans-note').parent().show()
  }
  else {
    $('#trans-pts,#ref-code').prop('disabled',false);
    $('#trans-note').parent().hide()
  }
});

function clearFields() {
  $('#trans-pts').val(''), $('#ref-code').val(''), $('#trans-note').val(''),
    $('#trans-action').val('approve'), $('#trans-note').parent().hide();
}

function formatRefill(d) {
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
      <tr>
        <td>NOTE:</td>
        <td>${d.note}</td>
      </tr>
    </table>`
  );
}

$('#refill-trans-table tbody').on('click', 'td.dt-control', function () {
  var tr = $(this).closest('tr');
  var row = transactionsTable.DataTable().row(tr);

  if (row.child.isShown()) {
    row.child.hide();
    tr.removeClass('shown');
  }
  else {
    row.child(formatRefill(row.data())).show();
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

  if(row.data().status != 'pending') {
    $('input[type="submit"]').prop('disabled', true)
      .addClass('disabled');
  } else {
    $('input[type="submit"]').prop('disabled', false)
      .removeClass('disabled');
  }
})

$('#refill-form').on('click', 'input[type="submit"]',function(e) {
  e.preventDefault();
  let amount = $('#trans-pts').val();
  axios.post('/transaction/refill', {
    id: $('#trans-id').val(),
    amount: amount.replaceAll(",", ""),
    ref_code: $('#ref-code').val(),
    action: $('#trans-action').val(),
    note: $('#trans-note').val(),
  })
  .then((res) => {
    Swal.fire({
      icon: 'success',
      confirmButtonColor: 'green',
      title: res.data.msg,
      timer: 1500
    })
    .then(() =>  {
      console.log(res);
      $('#modal-center').modal('hide')
      $('#operator-pts').html(res.data.points)
      clearFields();
    });

    transactionsTable.DataTable().ajax.reload();
    pendingCount = 0;
  })
  .catch((err) => {
    console.log(err);
  })

})

$('#trans-action').on('change', function(e) {
  e.preventDefault();
  let action = $(this).val();
  if(action == 'reject') {
    $('#trans-pts,#ref-code').prop('disabled',true);
    $('#trans-note').parent().show()
  }
  else {
    $('#trans-pts,#ref-code').prop('disabled',false);
    $('#trans-note').parent().hide()
  }
});

function clearFields() {
  $('#trans-pts').val(''), $('#ref-code').val(''), $('#trans-note').val(''),
    $('#trans-action').val('approve'), $('#trans-note').parent().hide();
}


$('[data-dismiss="modal"]').on('click', function() {
  $('#modal-center').modal('hide');
})

