var remitTable = $('#remit-trans-table');
var wPending = 0;
var unverified = 0;

remitTable.DataTable({
  "ajax": '/transaction/remit',
  "bPaginate": true,
  "bLengthChange": false,
  "bFilter": true,
  "bInfo": false,
  "bAutoWidth": true,
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
      "data": "reference_code"
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
      wPending++;
    }

    if( data.reference_code == null && data.status == 'completed') {
      $(row).addClass('bg-warning');
      unverified++;
    }

    if(wPending > 0) {
      $('#badge-remit').show().text(wPending);
    } else {
      $('#badge-remit').hide().text(wPending);
    }

    if(unverified > 0) {
      $('#badge-remit-unverified').show().text(unverified);
    } else {
      $('#badge-remit-unverified').hide().text(unverified);
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

$('#remit-trans-table tbody').on('click', 'td.dt-control', function () {
  var tr = $(this).closest('tr');
  var row = remitTable.DataTable().row(tr);

  if (row.child.isShown()) {
    row.child.hide();
    tr.removeClass('shown');
  }
  else {
    row.child(format(row.data())).show();
    tr.addClass('shown');
  }
});

remitTable.on('click', 'tbody td .view', async function() {
  clearFields();
  var tr = $(this).closest('tr');
  var row = remitTable.DataTable().row(tr);
  $('#remit-modal').modal('show')
  $('.modal-title').text(row.data().action.toUpperCase())
  $('input#remit-id').val($(this).data('id'));

  if(row.data().status != 'pending') {
    $('input[type="submit"]').prop('disabled', true)
      .addClass('disabled');
  } else {
    $('input[type="submit"]').prop('disabled', false)
      .removeClass('disabled');
  }
})

$('#remit-form').on('click', 'input[type="submit"]',function(e) {
  e.preventDefault();
  axios.post('/transaction/remit', {
    id: $('#remit-id').val(),
    ref_code: $('#remit-ref-code').val(),
    action: $('#remit-action').val(),
    note: $('#remit-note').val(),
  })
  .then((res) => {
    Swal.fire({
      icon: 'success',
      confirmButtonColor: 'green',
      title: res.data.msg,
      timer: 1500
    }).then(() =>  {
      $('#remit-modal').modal('hide')
      $('#operator-pts').html(res.data.points)
      clearFields();
    });

    remitTable.DataTable().ajax.reload();
    wPending = 0, unverified = 0;
  })
  .catch((err) => {
    console.log(err);
  })

})

$('#remit-action').on('change', function(e) {
  e.preventDefault();
  let action = $(this).val();
  if(action == 'update' ) {
    $('#remit-ref-code').prop('disabled',false);
    $('input[type="submit"]').prop('disabled', false)
      .removeClass('disabled');
  }
  else {
    $('#remit-ref-code').prop('disabled',true);
    $('input[type="submit"]').prop('disabled', true)
      .addClass('disabled');
  }
});

$('[data-dismiss="modal"]').on('click', function() {
  $('#remit-modal').modal('hide');
})

$('#badge-remit-unverified').tooltip().show()

$('[data-bs-toggle="tab"]').on('shown.bs.tab', function(e){
  $($.fn.dataTable.tables(true)).DataTable()
     .columns.adjust();
});
