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

var betHistoryTable = $('#bethistory-table');

betHistoryTable.DataTable({
  "bPaginate": true,
  "bLengthChange": true,
  "bFilter": true,
  "bInfo": false,
  "bAutoWidth": true,
  "scrollX": true,
  "ajax": '/bet/history',
  "pagingType": 'numbers',
  "processing": true,
  "serverSide": true,
  "pageLength": 25,
  "language": {
    "search": '',
    "lengthMenu": "_MENU_",
  },
  "dom": "<'row'<'col-4'l><'col-8'f>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
  "columnDefs": [
    {
      "targets": [1, 2, 3, 4, 5, 6, 7, 8],
      className: 'dt-head-center',
    },
    {
      "targets": [1, 2, 3, 4, 5, 6, 7, 8],
      className: 'dt-body-center',
    },
  ],
  "columns": [
    {
      className: 'dt-control',
      orderable: false,
      data: null,
      defaultContent: '',
    },
    // {
    //   "data": null,
    //   render: (data, type, row, meta) => {
    //     return row.fight ? row.fight.event.name : 'N/A';
    //   }
    // },
    {
      "data": "fight_no"
    },
    {
      "data": null,
      render: (data, type, row, meta) => {
        return SIDE[row.side]
      }
    },
    {
      "data": null,
      render: (data, type, row, meta) => {
        return row.status != '' ? WINNER[row.status] : "PENDING";
      }
    },

    {
      "data": null,
      render: function(data, type, row, meta) {
        return row.winamount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
      }
    },
    {
      "data": null,
      render: (data, type, row, meta) => {
        return `${row.percent.toFixed(2)}%`
      }
    },
    {
      "data": null,
      render: function(data, type, row, meta) {
        return row.betamount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
      }
    },
    {
      "data": null,
      render: function(data, type, row, meta) {
        return row.current_points.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
      },
    },
    {
      "data": "created_at"
    }
  ],
  "createdRow": function( row, data, dataIndex){
    if( data.status ==  `W`) {
      $(row).find('td').eq(3).attr('style', 'color: green !important');
      $(row).find('td').eq(4).attr('style', 'color: yellow !important');
    }

    if( data.status ==  `L` ) {
      $(row).find('td').eq(3).attr('style', 'color: #d67474eb !important');
    }

    if( data.side == 'M' ) {
      $(row).find('td').eq(2).attr('style', 'color: red !important');
    }

    if( data.side == 'W' ) {
      $(row).find('td').eq(2).attr('style', 'color: blue !important');
    }
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
  var row = betHistoryTable.DataTable().row(tr);

  if (row.child.isShown()) {
    row.child.hide();
    tr.removeClass('shown');
  }
  else {
    row.child(formatBetHistory(row.data())).show();
    tr.addClass('shown');
  }
});



