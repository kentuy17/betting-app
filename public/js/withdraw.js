var withdrawTable = $('#withdraw-trans-table');
var wPending = 0;
var unverified = 0;

const SIDE = {
  M: 'MERON',
  W: 'WALA',
  D: 'DRAW',
};

const WINNER = {
  P: 'PENDING',
  W: 'WIN',
  L: 'LOSE',
  D: 'DRAW',
  C: 'CANCELLED',
};

let dateRange = $('#datepicker').val().split('-');
let dateFrom = localStorage.getItem('dateFrom') ?? dateRange[0];
let dateTo = localStorage.getItem('dateTo') ?? dateRange[1];
let statuss = localStorage.getItem('status');

withdrawTable
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
      url: '/transaction/withdrawals',
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
    // "pageLength": 25,
    order: [[6, 'DESC']],
    pagingType: 'numbers',
    language: {
      search: '',
      lengthMenu: '_MENU_',
    },
    dom:
      "<'row'<'col-4'l><'col-8'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
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
        targets: [0, 1, 2, 3, 4, 5, 6, 7],
        className: 'dt-head-center',
      },
    ],
    columns: [
      // {
      //   className: "dt-control dt-body-left",
      //   orderable: false,
      //   data: null,
      //   defaultContent: "",
      //   data: "user_id",
      // },
      {
        data: 'user.name',
      },
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
      // $(row).find("td").eq(0).attr("style", "color: transparent !important");
      $(row).attr('data-id', data.id).addClass('cursor-pointer expandable');

      if (data.status == `pending`) {
        $(row).css({ 'background-color': 'var(--bs-red)' });
        wPending++;

        let timeDiff = moment(data.created_at, 'MM-DD-YYYY hh:mm:ss').fromNow();
        $(row).find('td').eq(5).text(timeDiff);
      }

      if (data.reference_code == null && data.status == 'completed') {
        $(row).addClass('bg-warning');
        unverified++;
      }

      if (data.status == `failed`) {
        $(row).addClass('failed');
      }

      if (wPending > 0) {
        $('#badge-withdraw').show().text(wPending);
      } else {
        $('#badge-withdraw').hide().text(wPending);
      }

      if (unverified > 0) {
        $('#badge-withdraw-unverified').show().text(unverified);
      } else {
        $('#badge-withdraw-unverified').hide().text(unverified);
      }
    },
    drawCallback: function (settings) {
      let response = settings.json;
      if (response.pending_count > 0) {
        $('#badge-withdraw').show().text(response.pending_count);
      } else {
        $('#badge-withdraw').hide().text(response.pending_count);
      }
    },
  });

function format(d) {
  // `d` is the original data object for the row
  // console.log(d);
  let userId = d.user_id;
  let userName = d.user.username;

  if (d.user_id == 666) {
    userId = DUMMY_ID;
    userName = d.user.name;
  }
  var operation = `<button onclick="operation(${d.id})" class="btn btn-link text-primary btn-icon operation" style="padding-left:0;">
      <i class="fa-solid fa-circle-info"></i></button>`;
  var btnCopy = `<button data-bs-toggle="tooltip" title="Copied!" data-bs-trigger="click" class="btn btn-link text-primary btn-icon copy-phone" id="copy-phone" data-phone-number="${d.mobile_number}">
    <i class="fa-solid fa-copy"></i></button>`;
  let betHistory = `<button onclick="betHistory(${userId},'${userName}')" class="btn btn-link btn-suucess btn-icon pl-0 bet-history-show">
    <i class="fa-solid fa-money-bill text-success"></i></button>`;
  var expandContent = `<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">
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
      <tr>
        <td>NOTE:</td>
        <td>${d.note ? d.note : 'N/A'}</td>
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

$('#withdraw-trans-table tbody').on('click', 'tr.expandable', function () {
  // var tr = $(this).closest("tr");
  // var row = withdrawTable.DataTable().row(tr);
  var tr = $(this);
  var row = withdrawTable.DataTable().row(tr[0]);

  if (row.child.isShown()) {
    row.child.hide();
    tr.removeClass('shown');
  } else {
    row.child(format(row.data())).show();
    tr.addClass('shown');
    $('[data-bs-toggle="tooltip"]').tooltip({ placement: 'top' });
  }
});

withdrawTable.on('click', 'tbody td .view', async function () {
  var tr = $(this).closest('tr');
  let id = $(this).data('id');
  viewWithdrawForm(tr, id);
});

async function operation(id) {
  var tr = $('tbody').find(`tr[data-id='${id}']`);
  viewWithdrawForm(tr[0], id);
}

async function viewWithdrawForm(tr, id) {
  clearFields();
  var row = withdrawTable.DataTable().row(tr);
  $('#withdraw-modal').modal('show');
  $('.modal-title').text(row.data().action.toUpperCase());
  $('input#withdraw-id').val(id);
  $('#withdraw-action').val('approve');
  $('#withdraw-note').parent().hide();
  $('#withdraw-ref-code').prop('disabled', false);

  if (row.data().status != 'pending') {
    $('input[type="submit"]').prop('disabled', true).addClass('disabled');
  } else {
    $('input[type="submit"]').prop('disabled', false).removeClass('disabled');
  }
}

function copyPhone(e) {
  const num = $(e).data('phone-number');
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

$('#withdraw-form').on('click', 'input[type="submit"]', function (e) {
  e.preventDefault();
  axios
    .post('/transaction/withdraw', {
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
        timer: 1500,
      }).then(() => {
        $('#withdraw-modal').modal('hide');
        $('#operator-pts').html(res.data.points);
        clearFields();
      });

      withdrawTable.DataTable().ajax.reload();
      (wPending = 0), (unverified = 0);
    })
    .catch((err) => {
      console.log(err);
    });
});

$('#withdraw-action').on('change', function (e) {
  e.preventDefault();
  let action = $(this).val();
  $('#withdraw-note').parent().hide();

  if (action == 'update') {
    $('#withdraw-ref-code').prop('disabled', false);
    $('input[type="submit"]').prop('disabled', false).removeClass('disabled');
  } else if (action == 'reject' || action == 'cancel') {
    $('#withdraw-ref-code').prop('disabled', true);
    $('input[type="submit"]').prop('disabled', false).removeClass('disabled');
    $('#withdraw-note').parent().show();
  } else {
    $('#withdraw-ref-code').prop('disabled', true);
    $('input[type="submit"]').prop('disabled', true).addClass('disabled');
  }
});

$('[data-dismiss="modal"]').on('click', function () {
  $('#withdraw-modal').modal('hide');
  $('#bethistory-modal').modal('hide');
  $('#settings-modal').modal('hide');
});

// $('#date-from').val(
//   localStorage.getItem('dateFrom') ?? moment().format('YYYY-MM-DD')
// );
// $('#date-to').val(
//   localStorage.getItem('dateTo') ?? moment().format('YYYY-MM-DD')
// );

// $('#badge-withdraw-unverified').tooltip().show()

$('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
  $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
});

// ----------------------------------------------------------------

async function betHistory(id, username = '') {
  const betHistoryTable = $('#bethistory-table');
  await betHistoryTable.DataTable().clear().destroy();
  $('#bethistory-modal').modal('show');
  $('#bethistory-head').text(username);
  betHistoryTable.DataTable({
    bPaginate: true,
    bLengthChange: true,
    bFilter: true,
    bInfo: false,
    bAutoWidth: true,
    scrollX: true,
    ajax: '/transaction/user-bet-history/' + id,
    pagingType: 'numbers',
    processing: true,
    serverSide: true,
    order: [[6, 'desc']],
    language: {
      search: '',
      lengthMenu: '_MENU_',
    },
    dom:
      "<'row'<'col-4'l><'col-8'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    columnDefs: [
      {
        targets: [1, 2, 3, 4, 5, 6],
        className: 'dt-body-center',
      },
    ],
    columns: [
      {
        data: 'fight_no',
      },
      {
        data: null,
        render: (data, type, row, meta) => {
          return SIDE[row.side];
        },
      },
      {
        data: null,
        render: function (data, type, row, meta) {
          return row.betamount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        },
      },
      {
        data: null,
        render: (data, type, row, meta) => {
          return `${row.percent.toFixed(2)}%`;
        },
      },
      {
        data: null,
        render: (data, type, row, meta) => {
          return row.status != '' ? WINNER[row.status] : 'PENDING';
        },
      },
      {
        data: null,
        render: function (data, type, row, meta) {
          return row.winamount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        },
      },
      {
        data: null,
        render: function (data, type, row, meta) {
          return row.current_points
            .toFixed(2)
            .replace(/\d(?=(\d{3})+\.)/g, '$&,');
        },
      },
      {
        data: 'created_at',
      },
    ],
    createdRow: function (row, data, dataIndex) {
      if (data.status == `W`) {
        $(row).find('td').eq(4).attr('style', 'color: green !important');
        $(row).find('td').eq(5).attr('style', 'color: yellow !important');
      }

      if (data.status == `L`) {
        $(row).find('td').eq(4).attr('style', 'color: red !important');
      }

      if (data.side == 'M') {
        $(row).find('td').eq(1).attr('style', 'color: red !important');
      }

      if (data.side == 'W') {
        $(row).find('td').eq(1).attr('style', 'color: blue !important');
      }
    },
  });
}

function formatBetHistory(d) {
  let win = '',
    status = '',
    side = '';
  if (d.status == 'W') {
    win = 'style="color:yellow"';
    status = 'style="color:green"';
  }

  if (d.status == 'L') {
    status = 'style="color:red"';
  }

  if (d.side == 'M') {
    side = 'style="color:red"';
  }

  if (d.side == 'W') {
    side = 'style="color:blue"';
  }
}

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
