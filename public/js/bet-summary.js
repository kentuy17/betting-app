$(document).ready(function () {
  const getResult = (r) => {
    switch (r) {
      case 'M':
        return 'MERON'
      case 'W':
        return 'WALA'
      case 'D':
        return 'DRAW'
      case 'C':
        return 'CANCELLED'
      default:
        return 'FIGHTING'
    }
  }

  const fetchBetSummary = async (schedDate) => {
    const betSummaryTable = $('#bet-summary-table');
    await betSummaryTable.DataTable().clear().destroy();

    betSummaryTable.DataTable({
      "bPaginate": true,
      "bLengthChange": true,
      "bFilter": true,
      "bInfo": false,
      "bAutoWidth": false,
      "pagingType": 'numbers',
      "processing": true,
      "serverSide": true,
      "pageLength": 10,
      "ajax": {
        "type": "GET",
        "url": "/summary-bet/filter-date",
        "data": {
          "event_date": schedDate,
        }
      },
      "language": {
        "search": "",
        "lengthMenu": "_MENU_",
      },
      dom:
        "<'row'<'col-4'l><'col-8'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
      "scrollX": true,
      "order": [[1, 'DESC']],
      "columnDefs": [
        {
          "targets": [1, 2],
          className: 'dt-body-center',
        },
        {
          "targets": [3, 4, 5],
          className: 'dt-body-right',
        },
      ],
      "columns": [
        {
          "data": null,
          render: (data) => moment(data.event.schedule_date).format('MMMM DD, YYYY').toUpperCase()
        }, {
          "data": "fight_no"
        }, {
          "data": null,
          render: (data) => getResult(data.game_winner)
        }, {
          "data": null,
          render: (data) => {
            let totalMeron = data.bet_legit_meron_sum_amount ?? 0
            return parseFloat(totalMeron).toFixed(2)
          }
        }, {
          "data": null,
          render: (data) => {
            let totalWala = data.bet_legit_wala_sum_amount ?? 0
            return parseFloat(totalWala).toFixed(2)
          }
        }, {
          "data": null,
          render: (data) => {
            return parseFloat(data.net).toFixed(2)
          }
        },
        {
          "data": "created_at",
        }
      ],
      "createdRow": function (row, data) {
        if (data.game_winner == 'M') {
          $(row).find('td').eq(2).attr('style', 'color: red !important');
        }

        if (data.game_winner == 'W') {
          $(row).find('td').eq(2).attr('style', 'color: blue !important');
        }

        if (data.net != 0) {
          $(row).find('td').eq(5).attr('style', `color: ${data.net > 0 ? 'yellow' : 'red'} !important`);
        }
      }
    });
  }

  $('#time-start').val('08:00');
  $('#sched-date').val(moment().format('YYYY-MM-DD'));

  var schedDate = $('#sched-date').val();
  fetchBetSummary(schedDate)

  $('#filter-date').on('click', function (e) {
    e.preventDefault();
    let schedDate = $('#sched-date').val()
    fetchBetSummary(schedDate)
  })
});
