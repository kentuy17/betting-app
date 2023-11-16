$(document).ready(function () {
  const eventsTable = $('#events-table');

  eventsTable.DataTable({
    "bPaginate": true,
    "bLengthChange": false,
    "bFilter": false,
    "bInfo": false,
    "bAutoWidth": false,
    "ajax": '/admin/agent-list',
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
      "data": "user_id"
    }, {
      "data": "user.username"
    }, {
      "data": "rid",
    }, {
      "data": null,
      render: ((row) => {
        return row.current_commission.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
      })
    }, {
      "data": "player_count",
    }, {
      "data": "type",
    }, {
      "data": "created_at",
    }, {
      "data": null,
      render: (data, type, row, meta) => {
        let act = data.status == 'ACTIVE' ? `<i class="fa-solid fa-stop"></i>` : `<i class="fa-solid fa-eye"></i>`;
        return `<a href="javascript:void(0)" data-id="${row.id}" class="btn btn-link text-primary btn-icon btn-sm info">${act}</a>
            <a href="javascript:void(0)" data-id="${row.id}" class="btn btn-link text-secondary btn-icon btn-sm edit"><i class="fa-solid fa-pencil"></i></a>
            <a href="javascript:void(0)" data-id="${row.id}" class="btn btn-link text-danger btn-icon btn-sm remove"><i class="fa-solid fa-xmark"></i></a>`;
      }
    }],
    "createdRow": function (row, data, dataIndex) {
      $(row).find('td').eq(3).attr('style', 'color: yellow !important');
    }
  });

  $('#time-start').val('09:00');
  $('#sched-date').val(moment().format('YYYY-MM-DD'));

  $('#add-derby').on('click', function (e) {
    if ($('#agent-username').val() == '') {
      alert('Please select user to add!');
      $(this).focus();
      return;
    }

    e.preventDefault();
    data = {
      user_id: $('#agent-username').val(),
    }

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      data: data,
      url: '/admin/add-agent',
      success: function (resp) {
        eventsTable.DataTable().ajax.reload();
        alert('Agent Added!');
        $('#agent-username').val('')
      },
      error: function (err) {
        console.log(err);
      }
    })
  })

  eventsTable.on('click', 'tbody td .view', async function () {
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
        eventsTable.DataTable().ajax.reload();
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
