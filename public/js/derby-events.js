$(document).ready(function () {
  const eventsTable = $('#events-table');

  eventsTable.DataTable({
    "bPaginate": true,
    "bLengthChange": false,
    "bFilter": false,
    "bInfo": false,
    "bAutoWidth": false,
    "ajax": '/event/lists',
    "scrollX": true,
    "order": [[0, 'DESC']],
    "columns": [
      {
        "data": "id"
      }, {
        "data": "name"
      }, {
        "data":null,
        render: ((row) => {
          return row.schedule_date ?? 'TBD';
        })
      }, {
        "data": null,
        render: ((row) => {
          return row.schedule_time ?? 'TBD';
        })
      }, {
        "data": null,
        render: ((data, type, row, meta) => {
          return data.status
        })
      },
      {
        "data": null,
        render: (data, type, row, meta) => {
          let act = data.status == 'ACTIVE' ? `<i class="fa-solid fa-stop"></i>` : `<i class="fa-solid fa-play"></i>`;
          return `<a href="javascript:void(0)" data-id="${row.id}" class="btn btn-link text-primary btn-icon btn-sm play">${act}</a>
          <a href="javascript:void(0)" data-id="${row.id}" class="btn btn-link text-secondary btn-icon btn-sm edit"><i class="fa-solid fa-pencil"></i></a>
          <a href="javascript:void(0)" data-id="${row.id}" class="btn btn-link text-danger btn-icon btn-sm remove"><i class="fa-solid fa-xmark"></i></a>
          </td>`
        }
      }
    ],
    "createdRow": function( row, data, dataIndex){
      if( data.status ==  `pending`){
        $(row).css({"background-color":"red"});
      }

      if(data.status == 'ACTIVE') {
        $(row).addClass('table-success');
      }
    }
  });

  $('#time-start').val('08:00');
  $('#sched-date').val(moment().format('YYYY-MM-DD'));

  $('#add-derby').on('click', function(e) {
    if($('#event-name').val() == '') {
      alert('Event Name is Required!');
      $(this).focus();
      return;
    }

    e.preventDefault();
    data = {
      name: $('#event-name').val(),
      schedule_date: $('#sched-date').val(),
      schedule_time: $('#time-start').val()
    }

    $.ajax({
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      type:'POST',
      data: data,
      url: '/event/create',
      success: function(resp) {
        eventsTable.DataTable().ajax.reload();
        alert('Event Fight Created!');
        $('#event-name').val('')
      },
      error: function(err) {
        console.log(err);
      }
    })
  })

  eventsTable.on('click', 'tbody td .play', async function() {
    try {
      var id  = $(this).data('id');
      response = await axios.post('/event/activate', { id: id });
      Swal.fire({
        icon: 'success',
        confirmButtonColor: 'green',
        title: response.data.message,
      }).then(() =>  {
        eventsTable.DataTable().ajax.reload();
      });
    }
    catch (error) {
      Swal.fire({
        icon: 'error',
        confirmButtonColor: 'red',
        title: error.response.data.message,
      })
    }
  });
});
