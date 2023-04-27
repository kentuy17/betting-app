$(document).ready(function () {
  const eventsTable = $('#events-table').DataTable({
    "bPaginate": true,
    "bLengthChange": false,
    "bFilter": false,
    "bInfo": false,
    "bAutoWidth": false,
    "ajax": '/event/lists',
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
        render: (() => {
          return 'WAITING'
        })
      },
      {
        "data": null,
        render: (data, type, row, meta) => {
          return `Edit | Delete`
        }
      }
    ],
    "createdRow": function( row, data, dataIndex){
      if( data.status ==  `pending`){
        $(row).css({"background-color":"red"});
      }
    }
  });

  $('#time-start').val(moment().format('HH:mm'));
    $('#sched-date').val(moment().format('YYYY-MM-DD'));

    $('#add-derby').on('click', function(e) {
      e.preventDefault();
      data = {
        name: $('#event-name').val(),
        schedule_date: $('#sched-date').val(),
        schedule_time: $('#time-start').val()
      }

      $.ajax({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:'POST',
        data: data,
        url: '/event/create',
        success: function(resp) {
          console.log(resp);
          eventsTable.ajax.reload();
          alert('Event Fight Created!');
        },
        error: function(err) {
          console.log(err);
        }
      })
    })
  
});
