$(document).ready(function () {
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

    $('#bethistory-table').DataTable({
      "bPaginate": true,
      "bLengthChange": false,
      "bFilter": true,
      "bInfo": false,
      "bAutoWidth": false,
      "scrollX": true,
      "ajax": '/bet/history',
      "columnDefs": [
        {
            "targets": [3,6],
            className: 'dt-body-center'
        }
      ],
      "columns": [
        {
          "data": null,
          render: (data, type, row, meta) => {
            let fightName = row.fight ? row.fight.event.name : 'N/A';
            return fightName;
          }
        },
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
          render: function(data, type, row, meta) {
            return row.betamount.toFixed(2);
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
          render: (data, type, row, meta) => {
            return row.status != '' ? WINNER[row.status] : "PENDING";
          }
        },
        {
          "data": null,
          render: function(data, type, row, meta) {
            return row.winamount.toFixed(2);
          }
        },
        {
          "data": "created_at"
        }
      ],
      "createdRow": function( row, data, dataIndex){
        if( data.status ==  `W`) {
          $(row).find('td').eq(5).attr('style', 'color: green !important');
          $(row).find('td').eq(6).attr('style', 'color: yellow !important');
        }

        if( data.status ==  `L` ) {
          $(row).find('td').eq(5).attr('style', 'color: red !important');
        }

        if( data.side == 'M' ) {
          $(row).find('td').eq(2).attr('style', 'color: red !important');
        }

        if( data.side == 'W' ) {
          $(row).find('td').eq(2).attr('style', 'color: blue !important');
        }
      }
    });
  });
