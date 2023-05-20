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
      "bFilter": false,
      "bInfo": false,
      "bAutoWidth": false,
      "scrollX": true,
      "ajax": '/bet/history',
      "columns": [
        {
          // "data": "fight.event.name"
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
          render: function(data, type, row, meta) {
            return row.winamount.toFixed(2);
          }
        },
        {
          "data": null,
          render: (data, type, row, meta) => {
            return `${row.percent.toFixed(2)}%`
          }
        },
        {
          "data": "created_at"
        },
        {
          "data": null,
          render: (data, type, row, meta) => {
            return row.status != '' ? WINNER[row.status] : "PENDING";
          }
        }
      ]
    });
  });
  