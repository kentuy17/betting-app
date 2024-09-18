const TYPE = {
  pending: 'PENDING',
  completed: 'COMPLETED',
  failed: 'FAILED',
};

var transactionsTable = $('#deposit-trans-table');
var pendingCount = 0;
var unpaidCount = 0;

function delay(time) {
  return new Promise((resolve) => setTimeout(resolve, time));
}

let dateRange = $('#datepicker').val().split('-');
let dateFrom = localStorage.getItem('dateFrom') ?? dateRange[0];
let dateTo = localStorage.getItem('dateTo') ?? dateRange[1];
let statuss = localStorage.getItem('status');

DataTable.ext.errMode = 'none';

transactionsTable
  .on('error.dt', function (e, settings, techNote, message) {
    console.log(e, 'error');
    console.log(message, 'message');
    let prompt = confirm('You are not currently logged in');
    if (prompt) {
      window.location.href = '/login';
    }
  })
  .DataTable({
    ajax: {
      type: 'GET',
      url: '/transaction/deposits',
      data: {
        date_from: dateFrom,
        date_to: dateTo,
        status: statuss ? JSON.parse(statuss) : ['pending', 'completed'],
        morph: [0],
      },
    },
    bPaginate: true,
    bLengthChange: true,
    bFilter: true,
    bInfo: false,
    bAutoWidth: true,
    scrollX: true,
    processing: true,
    serverSide: true,
    pagingType: 'numbers',
    language: {
      search: '',
      lengthMenu: '_MENU_',
    },
    order: [[5, 'DESC']],
    // onerror
    dom:
      "<'row'<'col-4'l><'col-8'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-md-12'p>>",
    preInit: function (e, settings) {
      pendingCount = 0;
      unpaidCount = 0;
    },
    columnDefs: [
      {
        targets: [1],
        className: 'dt-body-right',
      },
      {
        targets: [0, 2, 3, 4],
        className: 'dt-body-center',
      },
      {
        targets: [0, 2, 3, 4, 5],
        className: 'dt-head-center',
      },
    ],
    columns: [
      // {
      //   className: 'dt-control dt-body-left',
      //   orderable: false,
      //   data: null,
      //   defaultContent: '',
      //   data: "user_id",
      // },
      {
        data: 'user.name',
      },
      // {
      //   "data": "outlet"
      // },
      {
        data: null,
        render: (data) => {
          return data.amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        },
      },
      {
        data: 'mobile_number',
      },

      {
        data: null,
        render: (data) => {
          return data.operator != null ? data.operator.username : '--';
        },
      },
      {
        data: 'reference_code',
      },

      {
        data: 'created_at',
      },
      {
        data: null,
        render: (data) => {
          return data.status.toUpperCase();
        },
      },
      {
        data: null,
        render: (data) => {
          return `<a href="javascript:void(0)" data-id="${data.id}" class="btn btn-link text-primary btn-icon btn-sm view">
          <i class="fa-solid fa-circle-info"></i></a>`;
        },
      },
    ],
    createdRow: function (row, data, dataIndex) {
      $(row).attr('data-id', data.id).addClass('cursor-pointer expandable');
      if (data.status == `pending`) {
        $(row).css({ 'background-color': 'var(--bs-red)' });
        pendingCount++;

        let timeDiff = moment(data.created_at, 'MM-DD-YYYY hh:mm:ss').fromNow();
        $(row).find('td').eq(5).text(timeDiff);

        setInterval(() => {
          timeDiff = moment(data.created_at, 'MM-DD-YYYY hh:mm:ss').fromNow();
          $(row).find('td').eq(5).text(timeDiff);
        }, 5000);
      }

      if (data.status == `completed` && data.reference_code == null) {
        $(row).css({ 'background-color': 'var(--bs-yellow)' });
        $(row).addClass('bg-warning');
      }

      if (data.status == `failed`) {
        $(row).addClass('failed');
      }

      if (data.morph === 1) {
        $(row).find('td').eq(1).css({ 'text-decoration': 'line-through' });
      }
    },
    initComplete: function (settings, json) {
      unpaidCount = json.unpaid_count;

      if (pendingCount > 0) {
        $('#badge-deposit').show().text(pendingCount);
      } else {
        $('#badge-deposit').hide().text(pendingCount);
      }

      if (unpaidCount > 0) {
        $('#badge-unpaid').show().text(unpaidCount);
      } else {
        $('#badge-unpaid').hide().text(unpaidCount);
      }
    },
  });

transactionsTable;

// $.fn.dataTable.ext.errMode = 'none';

const Alert = (action = null, ti = null) => {
  let oldTitle = localStorage.getItem('OLD_TITLE') ?? document.title,
    newTitle = `(${pendingCount} ${action}) - ${oldTitle}`;

  let intervalId = setInterval(
    () => {
      document.title = document.title == newTitle ? oldTitle : newTitle;
    },
    ti ? ti : 1500
  );

  localStorage.setItem('OLD_TITLE', oldTitle);
  return intervalId;
};

window.socket.on('notify-deposit', () => {
  delay(5000)
    .then(() => transactionsTable.DataTable().ajax.reload())
    .then(() => Alert(`(${pendingCount}) Cashin - ${document.title}`));
});

window.socket.on('notify-deposit-processed', () => {
  transactionsTable.DataTable().ajax.reload();
});

window.socket.on('notify-withdraw', () => {
  let withdrawTab = document.getElementById('withdraw-tab');
  let withdrawTable = $('#withdraw-trans-table');
  withdrawTab.click();
  delay(5000)
    .then(() => withdrawTable.DataTable().ajax.reload())
    .then(() => Alert(`(${unpaidCount}) Cashout - ${document.title}`));
});

function formatDeposit(d) {
  let userId = d.user_id;
  let userName = d.user.username;

  if (d.user_id == 666) {
    userId = DUMMY_ID;
    userName = d.user.name;
  }

  let copyRefCode = `<button data-bs-toggle="tooltip" title="Copied!" data-bs-trigger="click" class="btn btn-link text-primary btn-icon copy-ref-code py-0" id="copy-ref-code" data-ref-code="${d.reference_code}">
    <i class="fa-solid fa-copy"></i></button>`;
  let note = d.note ? `<tr><td>NOTE:</td><td>${d.note}</td></tr>` : '';
  let refCode =
    d.reference_code && d.reference_code != null
      ? `<tr><td>REFCODE:</td><td>${d.reference_code} ${copyRefCode}</td></tr>`
      : '';
  let btnCopy = `<button data-bs-toggle="tooltip" title="Copied!" data-bs-trigger="click" class="btn btn-link text-primary btn-icon copy-phone" id="copy-phone" data-phone-number="${d.mobile_number}">
    <i class="fa-solid fa-copy"></i></button>`;
  let action = `<tr><td>ACTION:</td><td><button id="view-deposit" data-id="${d.id}" class="btn btn-link text-primary btn-icon" style="padding-left:0;">
      <i class="fa-solid fa-circle-info"></i></button></td></tr>`;
  return `<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">
      <tr>
        <td>ID:</td>
        <td>#${userId}</td>
      </tr>
      <tr>
        <td>PLAYER:</td>
        <td>${userName}</td>
      </tr>
      <tr>
        <td>MOBILE#:</td>
        <td>${d.mobile_number} ${btnCopy}</td>
      </tr>
      <tr>
        <td>AMOUNT:</td>
        <td>${d.amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
      </tr>
      ${note}
      ${refCode}
      ${action}
    </table>`;
}

$(transactionsTable, 'tbody td').on(
  'click',
  'button#copy-phone',
  async function (e) {
    e.preventDefault();
    const phoneNum = e.currentTarget.dataset.phoneNumber;
    let $temp = $('<input>');
    $('body').append($temp);
    $temp.val(phoneNum).select();
    document.execCommand('copy');
    $temp.remove();
    $(this).removeClass('text-primary').addClass('text-success');
    $(this).find('i').removeClass('fa-copy').addClass('fa-check');
    setTimeout(() => {
      $(this).tooltip('hide');
      $(this).removeClass('text-success').addClass('text-primary');
      $(this).find('i').removeClass('fa-check').addClass('fa-copy');
    }, 3000);
  }
);

// function copyPhone(e) {
//   const num = $(e).data('phone-number');
// }

$(transactionsTable, 'tbody td').on(
  'click',
  'button#view-deposit',
  async function (e) {
    e.preventDefault();
    clearFields();
    const viewDepositBtn = e.currentTarget;
    const id = viewDepositBtn.dataset.id;

    let tr = $('tbody').find(`tr[data-id='${id}']`);
    let row = transactionsTable.DataTable().row(tr[0]);
    let storage = $('#trans-receipt').data('storage');

    $('#modal-center').modal('show');
    $('.modal-title').text(row.data().action.toUpperCase());
    $('input#trans-id').val(row.data().id);

    if (row.data().filename) {
      $('#trans-receipt').attr('src', storage + '/' + row.data().filename);
    }
  }
);

$('#deposit-trans-table tbody').on('click', 'tr.expandable', function () {
  var tr = $(this);
  var row = transactionsTable.DataTable().row(tr[0]);
  if (row.child.isShown()) {
    row.child.hide();
    tr.removeClass('shown');
  } else {
    row.child(formatDeposit(row.data())).show();
    tr.addClass('shown');
  }
});

transactionsTable.on('click', 'tbody td .view', async function () {
  var tr = $(this).closest('tr');
  var row = transactionsTable.DataTable().row(tr);
  $('#modal-center').modal('show');
  $('.modal-title').text(row.data().action.toUpperCase());
  $('input#trans-id').val($(this).data('id'));

  let storage = $('#trans-receipt').data('storage');
  if (row.data().filename) {
    $('#trans-receipt').attr('src', storage + '/' + row.data().filename);
  }

  if (row.data().status != 'pending') {
    $('input[type="submit"]').prop('disabled', true).addClass('disabled');
  } else {
    $('input[type="submit"]').prop('disabled', false).removeClass('disabled');
  }
});

$('#filter-settings-form').on('click', 'input[type="submit"]', function (e) {
  e.preventDefault();
  let dateRange = $('#datepicker').val().split('-');
  let statuses = [];

  localStorage.setItem('dateFrom', dateRange[0]);
  localStorage.setItem(
    'dateTo',
    dateRange[1] === undefined ? dateRange[0] : dateRange[1]
  );

  ['pending', 'completed', 'failed'].forEach((stat) => {
    let isChecked = $('#checkbox-status-' + stat).is(':checked');
    if (isChecked) statuses.push(stat);
  });
  localStorage.setItem('status', JSON.stringify(statuses));
  // transactionsTable.DataTable().ajax.reload();
  window.location.reload();
});

$('#deposit-form').on('click', 'input[type="submit"]', function (e) {
  e.preventDefault();
  axios
    .post('/transaction/deposit', {
      id: $('#trans-id').val(),
      amount: $('#trans-pts').val().replace(',', ''),
      ref_code: $('#ref-code').val(),
      action: $('#trans-action').val(),
      note: $('#trans-note').val(),
    })
    .then((res) => {
      Swal.fire({
        icon: 'success',
        confirmButtonColor: 'green',
        title: res.data.msg,
        timer: 1500,
      }).then(() => {
        $('#modal-center').modal('hide');
        $('#operator-pts').html(res.data.points);
        clearFields();
      });

      transactionsTable.DataTable().ajax.reload();
      pendingCount = 0;
    })
    .then(() => {
      window.socket.emit('deposit-processed', 'done');
    })
    .catch((err) => {
      Swal.fire({
        icon: 'error',
        confirmButtonColor: 'red',
        title: err.response.data.msg,
        timer: 1500,
      });
    });
});

$('#manual-request-form').on('click', 'input[type="submit"]', function (e) {
  e.preventDefault();
  const amount = $('#manual-request-amount').val();
  if (!isNumeric(amount)) {
    Swal.fire({
      icon: 'error',
      confirmButtonColor: 'red',
      title: 'Invalid amount!',
      timer: 1500,
    });
    return;
  }

  axios
    .post('/transaction/utang', {
      id: $('#player-username').val(),
      amount: amount.replace(',', ''),
      ref_code: $('#manual-request-ref').val(),
      action: $('#manual-request-action').val(),
      note: $('#manual-request-note').val(),
    })
    .then((res) => {
      Swal.fire({
        icon: 'success',
        confirmButtonColor: 'green',
        title: res.data.msg,
        timer: 1500,
      }).then(() => {
        $('#manual-request-modal').modal('hide');
        clearFields();
      });

      transactionsTable.DataTable().ajax.reload();
      pendingCount = 0;
    })
    .then(() => {
      window.socket.emit('deposit-processed', 'done');
    })
    .catch((err) => {
      Swal.fire({
        icon: 'error',
        confirmButtonColor: 'red',
        title: err.response.data.msg,
        timer: 1500,
      });
    });
});

$('#trans-action').on('change', function (e) {
  e.preventDefault();
  let action = $(this).val();
  if (action == 'reject') {
    $('#trans-pts,#ref-code').prop('disabled', true);
    $('#trans-note').parent().show();
  } else if (action == 'update') {
    $('#ref-code').prop('disabled', false);
    $('#trans-pts').prop('disabled', true);
    $('#trans-note').parent().hide();
  } else {
    $('#trans-pts,#ref-code').prop('disabled', false);
    $('#trans-note').parent().hide();
  }
});

$('#manual-request-action').on('change', function (e) {
  e.preventDefault();
  $('#manual-request-ref').parent().toggle();
  $('#manual-request-note').parent().toggle();
});

function clearFields() {
  $('#trans-pts').val('');
  $('#ref-code').val('');
  $('#trans-note').val('');
  $('#trans-action').val('approve');
  $('#trans-note').parent().hide();
  $('#withdraw-note').val('');
  $('#withdraw-ref-code').val('');
  $('#manual-request-ref').val('');
  $('#manual-request-note').val('');
  $('#manual-request-amount').val('');
  $('#player-username').text('');
}

$('[data-dismiss="modal"]').on('click', function () {
  $('#modal-center').modal('hide');
});

//Revert Points
transactionsTable.on('click', 'tbody td .view-undo', async function () {
  clearFields();
  var tr = $(this).closest('tr');
  var row = transactionsTable.DataTable().row(tr);
  $('#modal-undo-points').modal('show');
  $('input#undo-id').val($(this).data('id'));

  let storage = $('#trans-receipt-und').data('storage');
  if (row.data().filename) {
    $('#trans-receipt-undo').attr('src', storage + '/' + row.data().filename);
  }
  if (row.data().amount) {
    $('#trans-pts-undo').val(row.data().amount);
  }
  if (row.data().reference_code) {
    $('#ref-code-undo').val(row.data().reference_code);
  }

  if (row.data().status != 'completed') {
    $('input[type="submit"]').prop('disabled', true).addClass('disabled');
  } else {
    $('input[type="submit"]').prop('disabled', false).removeClass('disabled');
  }
});

$('#deposit-undo-form').on('click', 'input[type="submit"]', function (e) {
  e.preventDefault();
  axios
    .post('/transaction/deposit/revert', {
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
        timer: 1500,
      }).then(() => {
        $('#modal-undo-points').modal('hide');
        $('#operator-pts').html(res.data.points);
        clearFields();
      });

      transactionsTable.DataTable().ajax.reload();
      pendingCount = 0;
    })
    .catch((err) => {
      console.log(err);
    });
});

function copyRefCode(e) {
  const num = $(e).data('ref-code');
  var $temp = $('<input>');
  $('body').append($temp);
  $temp.val(num).select();
  document.execCommand('copy');
  $temp.remove();
  $(e).removeClass('text-primary').addClass('text-success');
  $(e).find('i').removeClass('fa-copy').addClass('fa-check');
  setTimeout(() => {
    $(e).tooltip('hide');
    $(e).removeClass('text-success').addClass('text-primary');
    $(e).find('i').removeClass('fa-check').addClass('fa-copy');
  }, 3000);
}

$('[data-dismiss="modal"]').on('click', function () {
  $('#modal-undo-points').modal('hide');
  $('#manual-request-modal').modal('hide');
});

function showNotification(message) {
  const img = 'img/sabong-aficionado-icon.png';
  const text = message;
  new Notification('Sabong Aficionado', {
    body: text,
    icon: img,
  });
}

function isNumeric(str) {
  if (typeof str != 'string') return false; // we only process strings!
  return (
    !isNaN(str) && // use type coercion to parse the _entirety_ of the string (`parseFloat` alone does not do this)...
    !isNaN(parseFloat(str))
  ); // ...and ensure strings of whitespace fail
}

// $("#allow-notifications").on("click", () => {
//   if (!("Notification" in window)) {
//     // Check if the browser supports notifications
//     alert("This browser does not support desktop notification");
//   } else if (Notification.permission === "granted") {
//     // Check whether notification permissions have already been granted;
//     // if so, create a notification
//     const notification = new Notification("Hi there!");
//     // …
//   } else if (Notification.permission !== "denied") {
//     // We need to ask the user for permission
//     Notification.requestPermission().then((permission) => {
//       // If the user accepts, let's create a notification
//       if (permission === "granted") {
//         const notification = new Notification("Hi there!");
//         // …
//       }
//     });
//   }
// });
