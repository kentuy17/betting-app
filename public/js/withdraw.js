var withdrawTable = $('#withdraw-trans-table');
var wPending = 0;
var unverified = 0;

withdrawTable.DataTable({
  "ajax": '/transaction/withdrawals',
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
      }
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
      $('#badge-withdraw').show().text(wPending);
    } else {
      $('#badge-withdraw').hide().text(wPending);
    }

    if(unverified > 0) {
      $('#badge-withdraw-unverified').show().text(unverified);
    } else {
      $('#badge-withdraw-unverified').hide().text(unverified);
    }
  }
});

function format(d) {
  // `d` is the original data object for the row
  var operation = `<button onclick="operation(${d.id})" class="btn btn-link text-primary btn-icon operation" style="padding-left:0;">
      <i class="fa-solid fa-circle-info"></i></button>`;
  var btnCopy = `<button data-bs-toggle="tooltip" title="Copied!" data-bs-trigger="click" class="btn btn-link text-primary btn-icon copy-phone" id="copy-phone" data-phone-number="${d.mobile_number}"
      onclick="copyPhone(this);"><i class="fa-solid fa-copy"></i></button>`;
  let betHistory = `<a href="javascript:void(0)" data-id="${d.id}" class="btn btn-link text-primary btn-icon pl-0 show">view
      <i class="fa-solid fa-eye"></i></a>`;
  var expandContent = `<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">
      <tr>
        <td>PLAYER:</td>
        <td>${d.user.username}</td>
      </tr>
      <tr>
        <td>MOBILE#:</td>
        <td>${d.mobile_number} ${btnCopy}</td>
      </tr>
      <tr>
        <td>AMOUNT:</td>
        <td>${d.amount}</td>
      </tr>
      <tr>
        <td>ACTION:</td>
        <td>${operation}</td>
      </tr>
      <tr>
        <td>BETS:</td>
        <td>${betHistory}</td>
      </tr>
    </table>`;
  return expandContent;
}

$('#withdraw-trans-table tbody').on('click', 'td.dt-control', function () {
  var tr = $(this).closest('tr');
  var row = withdrawTable.DataTable().row(tr);

  if (row.child.isShown()) {
    row.child.hide();
    tr.removeClass('shown');
  }
  else {
    row.child(format(row.data())).show();
    tr.addClass('shown');
    $('[data-bs-toggle="tooltip"]').tooltip({placement:"top"})
  }
});

withdrawTable.on('click', 'tbody td .view', async function() {
  clearFields();
  var tr = $(this).closest('tr');
  var row = withdrawTable.DataTable().row(tr);
  $('#withdraw-modal').modal('show')
  $('.modal-title').text(row.data().action.toUpperCase())
  $('input#withdraw-id').val($(this).data('id'));

  if(row.data().status != 'pending') {
    $('input[type="submit"]').prop('disabled', true)
      .addClass('disabled');
  } else {
    $('input[type="submit"]').prop('disabled', false)
      .removeClass('disabled');
  }
})

function copyPhone(e) {
  const num = $(e).data('phone-number');
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val(num).select();
  document.execCommand("copy");
  $temp.remove();
  $(e).removeClass('text-primary').addClass('text-success')
  $(e).find('i').removeClass('fa-copy').addClass('fa-check')
  setTimeout(() => {
    $(e).tooltip('hide')
    $(e).removeClass('text-success').addClass('text-primary')
    $(e).find('i').removeClass('fa-check').addClass('fa-copy')
  }, 3000);

}

async function operation(id) {
  $('#withdraw-modal').modal('show')
  $('.modal-title').text('WITHDRAW')
  $('input#withdraw-id').val(id);

}

$('#withdraw-form').on('click', 'input[type="submit"]',function(e) {
  e.preventDefault();
  axios.post('/transaction/withdraw', {
    id: $('#withdraw-id').val(),
    ref_code: $('#withdraw-ref-code').val(),
    action: $('#withdraw-action').val(),
    note: $('#withdraw-note').val(),
  })
  .then((res) => {
    Swal.fire({
      icon: 'success',
      confirmButtonColor: 'green',
      title: res.data.msg,
      timer: 1500
    }).then(() =>  {
      $('#withdraw-modal').modal('hide')
      $('#operator-pts').html(res.data.points)
      clearFields();
    });

    withdrawTable.DataTable().ajax.reload();
    wPending = 0, unverified = 0;
  })
  .catch((err) => {
    console.log(err);
  })

})

$('#withdraw-action').on('change', function(e) {
  e.preventDefault();
  let action = $(this).val();
  if(action == 'update' ) {
    $('#withdraw-ref-code').prop('disabled',false);
    $('input[type="submit"]').prop('disabled', false)
      .removeClass('disabled');
  }
  else {
    $('#withdraw-ref-code').prop('disabled',true);
    $('input[type="submit"]').prop('disabled', true)
      .addClass('disabled');
  }
});

$('[data-dismiss="modal"]').on('click', function() {
  $('#withdraw-modal').modal('hide');
})

$('#badge-withdraw-unverified').tooltip().show()
