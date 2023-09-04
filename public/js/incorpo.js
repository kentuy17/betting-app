$(document).ready(function () {
  const corpoTable = $('#corpo-table');

  corpoTable.DataTable({
    "bPaginate": true,
    "bLengthChange": false,
    "bFilter": false,
    "bInfo": false,
    "bAutoWidth": false,
    "ajax": '/admin/incorpo-list',
    "scrollX": true,
    "pageLength": 25,
    "columnDefs": [{
        "targets": [3],
        "className": 'dt-body-right',
      },
      {
        "targets": [4],
        "className": 'dt-body-center',
      },
    ],
    "columns": [{
      "data": "id"
    }, {
      "data": "user_id"
    }, {
      "data": "bracket",
    }, {
      "data": null,
      render: ((row) => {
        // return row.current_commission.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        return '1000.00'
      })
    }, {
      "data": "player_count",
    }, {
      "data": "created_at",
    }, {
      "data": null,
      render: (data, type, row, meta) => {
        let act = data.status == 'ACTIVE' ? `<i class="fa-solid fa-stop"></i>` : `<i class="fa-solid fa-eye"></i>`;
        return `<a href="javascript:void(0)" data-id="${row.id}" class="btn btn-link text-primary btn-icon btn-sm info">${act}</a>
            <a href="javascript:void(0)" data-id="${row.id}" class="btn btn-link text-secondary btn-icon btn-sm edit"><i class="fa-solid fa-pencil"></i></a>
            <a href="javascript:void(0)" data-id="${row.id}" class="btn btn-link text-success btn-icon btn-sm plus"><i class="fa-solid fa-plus"></i></a>`;
      }
    }],
    "createdRow": function (row, data, dataIndex) {
      $(row).find('td').eq(3).attr('style', 'color: yellow !important');
    }
  });

  $('#time-start').val('09:00');
  $('#sched-date').val(moment().format('YYYY-MM-DD'));

  $('#add-corpo').on('click', function (e) {
    if ($('#corpo-id').val() == '') {
      alert('Please choose corpo!');
      $(this).focus();
      return;
    }

    e.preventDefault();
    data = {
      user_id: $('#corpo-id').val(),
      bracket: $('#bracket-name').val(),
    }

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      data: data,
      url: '/admin/add-corpo-agent',
      success: function (resp) {
        corpoTable.DataTable().ajax.reload();
        alert('Corpo Added!');
        $('#corpo-id').val('')
        $('#bracket-name').val('')
      },
      error: function (err) {
        console.log(err);
      }
    })
  })

  // View Agents
  corpoTable.on('click', 'tbody td .view', async function () {
    try {
      var id = $(this).data('id');
      response = await axios.post('/event/activate', {
        id: id
      });
      Swal.fire({
        icon: 'success',
        confirmButtonColor: 'green',
        title: response.data.message,
      }).then(() => {
        corpoTable.DataTable().ajax.reload();
      });
    } catch (error) {
      Swal.fire({
        icon: 'error',
        confirmButtonColor: 'red',
        title: error.response.data.message,
      })
    }
  });

  // Add Agents Under Corpo
  corpoTable.on('click', 'tbody td .plus', async function (e) {
    e.preventDefault();
    let agentCount = parseInt(prompt("How many?", "0"), 100);
    try {
      var id = $(this).data('id');
      response = await axios.post('/admin/add-agents', {
        agent_id: id,
        agent_count: agentCount,
      });
      Swal.fire({
        icon: 'success',
        confirmButtonColor: 'green',
        title: response.data.message,
      }).then(() => {
        corpoTable.DataTable().ajax.reload();
      });
    } catch (error) {
      Swal.fire({
        icon: 'error',
        confirmButtonColor: 'red',
        title: error.response.data.message,
      })
    }
  });


});
