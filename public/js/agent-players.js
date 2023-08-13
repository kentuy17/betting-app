const SIDE = {
  M: 'MERON',
  W: 'WALA',
  D: 'DRAW'
}

const WINNER = {
  P: 'PENDING',
  W: 'WIN',
  L: 'LOSE',
  D: 'DRAW',
  C: 'CANCELLED'
}

var agentPlayersTable = $('#agent-players-table');

agentPlayersTable.DataTable({
  "bPaginate": true,
  "bLengthChange": true,
  "bFilter": true,
  "bInfo": false,
  "bAutoWidth": true,
  "scrollX": true,
  "ajax": '/agent/players-list',
  "pagingType": 'numbers',
  "pageLength": 25,
  "language": {
    "search": '',
    "lengthMenu": "_MENU_",
  },
  "order": [[2, 'desc'], [4, 'desc']],
  "dom": "<'row'<'col-4'l><'col-8'f>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
  "columnDefs": [
    {
        "targets": [2,4],
        className: 'dt-body-right'
    }
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
      render: (data, type, row, meta) => {
        return data.user ? data.user.username : "---";
      }
    },
    {
      "data": null,
      render: (data, type, row, meta) => {
        if(!data.user) return "---";
        return data.commission
          ? data.commission.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')
          : "0.00";
      }
    },
    {
      "data": "created_at"
    },
    {
      "data": null,
      render: (data, type, row, meta) => {
        return data.user ? data.user.points.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') : "---";
      }
    },
    {
      "data": null,
      render: (data, type, row, meta) => {
        if(!data.user) return "---";
        return data.user.active ? "ONLINE" : "OFFLINE";
      }
    },
    {
      "data": null,
      render: (data, type, row, meta) => {
        return `<a href="javascript:void(0)" data-id="${row.id}" class="btn btn-link text-primary btn-icon btn-sm play"><i class="fa-solid fa-eye"></i></a>
          <a href="javascript:void(0)" data-id="${row.id}" class="btn btn-link text-secondary btn-icon btn-sm edit"><i class="fa-solid fa-pencil"></i></a>
          <a href="javascript:void(0)" data-id="${row.id}" class="btn btn-link text-danger btn-icon btn-sm remove"><i class="fa-solid fa-xmark"></i></a>
          </td>`
      }
    },
  ],
  "createdRow": function( row, data, dataIndex){

  }
})

function formatBetHistory(d) {
  let win = '', status = '', side = '';
  if(d.status == 'W') {
    win = 'style="color:yellow"';
    status = 'style="color:green"';
  }

  if(d.status == 'L') {
    status = 'style="color:red"';
  }

  if(d.side == 'M') {
    side = 'style="color:red"'
  }

  if(d.side == 'W') {
    side = 'style="color:blue"';
  }

  return (
    `<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">
      <tr>
        <td>FIGHT#:</td>
        <td>${d.fight_no}</td>
      </tr>
      <tr>
        <td>SIDE:</td>
        <td ${side}>${SIDE[d.side]}</td>
      </tr>
      <tr>
        <td>BET AMOUNT:</td>
        <td>${d.betamount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
      </tr>
      <tr>
        <td>PERCENT:</td>
        <td>${d.percent.toFixed(2)}%</td>
      </tr>
      <tr>
        <td>STATUS:</td>
        <td ${status}>${WINNER[d.status]}</td>
      </tr>
      <tr>
        <td>WIN:</td>
        <td ${win}>${d.winamount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
      </tr>
    </table>`
  );
}

$('#bethistory-table tbody').on('click', 'td.dt-control', function () {
  var tr = $(this).closest('tr');
  var row = agentPlayersTable.DataTable().row(tr);

  if (row.child.isShown()) {
    row.child.hide();
    tr.removeClass('shown');
  }
  else {
    row.child(formatBetHistory(row.data())).show();
    tr.addClass('shown');
  }
});



