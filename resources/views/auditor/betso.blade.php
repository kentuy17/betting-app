@extends('layouts.app-sub')

@section('content')
  <div class="container">
    <div class="row col-md-12 justify-content-center pt-3">
      <div class="card">
        <div class="card-body">
          <table class="table table-striped w-100" id="betso-table">
            <thead>
              <tr>
                <th>FIGHT#</th>
                <th>RESULT</th>
                <th>MERON</th>
                <th>WALA</th>
                <th>NET</th>
                <th>CREATED</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('additional-scripts')
  <script defer>
    $(document).ready(function() {
      const betsoTable = $('#betso-table')
      betsoTable.DataTable({
        ajax: '/reports/data',
        bPaginate: true,
        bLengthChange: true,
        bFilter: true,
        bInfo: false,
        bAutoWidth: true,
        scrollX: true,
        processing: true,
        serverSide: true,
        pagingType: "numbers",

        language: {
          search: "",
          lengthMenu: "_MENU_",
        },
        order: [
          [5, "DESC"]
        ],
        dom: "<'row'<'col-4'l><'col-8'f>>" +
          "<'row'<'col-sm-12'tr>>" +
          "<'row'<'col-md-12'p>>",
        preInit: function(e, settings) {
          pendingCount = 0;
          unpaidCount = 0;
        },
        columnDefs: [{
          targets: [2, 3, 4],
          className: 'dt-body-right',
        }],
        columns: [{
            data: 'fight_no',
          },
          {
            data: 'game_winner',
          },
          {
            data: null,
            render: (data) => {
              let meron = 0;
              for (let index = 0; index < data.bethistory.length; index++) {
                const element = data.bethistory[index];
                if (element.side == 'M') meron += element.betamount
              }
              return parseFloat(meron).toFixed(2);
            }
          },
          {
            data: null,
            render: (data) => {
              let wala = 0;
              for (let index = 0; index < data.bethistory.length; index++) {
                const element = data.bethistory[index];
                if (element.side == 'W') wala += element.betamount
              }
              return parseFloat(wala).toFixed(2);
            }
          },
          {
            data: null,
            render: (data) => {
              if (['C', 'D', 'X', null].includes(data.game_winner))
                return 0;

              let net = 0;
              for (let index = 0; index < data.bethistory.length; index++) {
                const element = data.bethistory[index];
                net += element.betamount - element.winamount
              }
              return parseFloat(net).toFixed(2);
            }
          }, {
            data: 'created_at'
          }
        ],
        createdRow: function(row, data) {
          if (data.game_winner == 'M') {
            $(row).find('td').eq(1).attr('style', 'color: red !important');
          }

          if (data.game_winner == 'W') {
            $(row).find('td').eq(1).attr('style', 'color: blue !important');
          }

          const net = $(row).find('td').eq(4).text()
          $(row).find('td').eq(4).attr('style', `color: ${parseFloat(net) > 0 ? 'yellow' : 'red'} !important`);

          $(row).attr('data-id', data.id)
            .addClass('cursor-pointer expandable');
        },
      });

      function formatBets(d) {
        let playahBets = `<tr>
            <th>USER</th>
            <th>SIDE</th>
            <th>AMOUNT</th>
            <th>PERCENT</th>
            <th>WIN</th>
            <th>BAL</th>
          </tr>`;

        d.player.forEach((bet) => {
          playahBets = playahBets + `<tr>
            <td>${bet.username}</td>
            <td>${bet.side}</td>
            <td>${bet.amount}</td>
            <td>${bet.percent}</td>
            <td>${parseFloat(bet.win).toFixed(2)}</td>
            <td>${parseFloat(bet.bal).toFixed(2)}</td>
          </tr>`;
        });
        return `<table cellpadding="5" cellspacing="1" border="1" style="padding-left:50px;">${playahBets}</table>`;
      }

      $('#betso-table tbody').on('click', 'tr.expandable', function(e) {
        var tr = $(this);
        var row = betsoTable.DataTable().row(tr[0]);
        if (row.child.isShown()) {
          row.child.hide();
          tr.removeClass('shown');
        } else {
          row.child(formatBets(row.data())).show();
          tr.addClass('shown');
        }
      })

      window.Echo.private("bet")
        .listen("Bet", async (e) => {
          betsoTable.DataTable().ajax.reload();
        })

      window.Echo.channel('fight')
        .listen('.fightUpdated', async (e) => {
          betsoTable.DataTable().ajax.reload()
        })
    })
  </script>
@endsection
