const TYPE = {
  pending: 'PENDING',
  completed: 'COMPLETED',
  failed: 'FAILED'
}

var transactionsTable = $('#deposit-trans-table');
var pendingCount = 0;

transactionsTable.DataTable({
  "ajax": '/transaction/deposits',
  "bPaginate": true,
  "bLengthChange": false,
  "bFilter": false,
  "bInfo": false,
  "bAutoWidth": true,
  "scrollX": true,
  "columnDefs": [
    {
      "targets": [3],
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
      "data": "user.username"
    },
    {
      "data": "outlet"
    },
    {
      "data": null,
      render: (data) => {
        return data.amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
      }
    },
    {
      "data": "mobile_number"
    },

    {
      "data": null,
      render: (data) => {
        return data.operator != null ? data.operator.username : "--";
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
        if(data.status == "completed" && data.completed_at != null){
              return `<a href="javascript:void(0)" data-id="${data.id}" class="btn btn-link text-primary btn-icon btn-sm view">
              <i class="fa-solid fa-circle-info"></i></a>
              <a href="javascript:void(0)" data-id="${data.id}" class="btn btn-link text-primary btn-icon btn-sm view-undo">
              <i class="fa-solid fa-undo"></i></a>`;
        }
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

function formatDeposit(d) {
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
        <td>${d.amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
      </tr>
      <tr>
        <td>NOTE:</td>
        <td>${d.note}</td>
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
    row.child(formatDeposit(row.data())).show();
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

$('#deposit-form').on('click', 'input[type="submit"]',function(e) {
  e.preventDefault();
  axios.post('/transaction/deposit', {
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
    });
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

//Revert Points
transactionsTable.on('click', 'tbody td .view-undo', async function() {
  clearFields();
  var tr = $(this).closest('tr');
  var row = transactionsTable.DataTable().row(tr);
  $('#modal-undo-points').modal('show')
  $('input#undo-id').val($(this).data('id'));

  let storage = $('#trans-receipt-und').data('storage');
  if(row.data().filename) {
    $('#trans-receipt-undo').attr('src', storage+'/'+row.data().filename);
  }
  if(row.data().amount) {
    $('#trans-pts-undo').val(row.data().amount);
  }
  if(row.data().reference_code) {
    $('#ref-code-undo').val(row.data().reference_code);
  }

  if(row.data().status != 'completed') {
    $('input[type="submit"]').prop('disabled', true)
      .addClass('disabled');
  } else {
    $('input[type="submit"]').prop('disabled', false)
      .removeClass('disabled');
  }
})

$('#deposit-undo-form').on('click', 'input[type="submit"]',function(e) {
  e.preventDefault();
  axios.post('/transaction/deposit/revert', {
    id: $('#undo-id').val(),
    curr_amount: $('#trans-pts-undo').val(),
    amount: $('#updated-trans-pts').val(),
    ref_code: $('#ref-code-undo').val(),
    note: $('#trans-note-undo').val(),
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
      $('#modal-undo-points').modal('hide')
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

function clearFields() {
  $('#updated-trans-pts').val(''), $('#trans-note-undo').val(''),
  $('#trans-note').parent().hide();
}


$('[data-dismiss="modal"]').on('click', function() {
  $('#modal-undo-points').modal('hide');
})

function showNotification(message) {
  const img = "img/sabong-aficionado-icon.png";
  console.log(img);
  const text = message;
  new Notification("Sabong Aficionado", { body: text, icon: img });
}

$('#allow-notifications').on('click', () => {
  if (!("Notification" in window)) {
    // Check if the browser supports notifications
    alert("This browser does not support desktop notification");
  } else if (Notification.permission === "granted") {
    // Check whether notification permissions have already been granted;
    // if so, create a notification
    const notification = new Notification("Hi there!");
    // …
  } else if (Notification.permission !== "denied") {
    // We need to ask the user for permission
    Notification.requestPermission().then((permission) => {
      // If the user accepts, let's create a notification
      if (permission === "granted") {
        const notification = new Notification("Hi there!");
        // …
      }
    });
  }

});
