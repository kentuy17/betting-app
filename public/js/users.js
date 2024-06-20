const TYPE = {
  pending: 'PENDING',
  completed: 'COMPLETED',
  failed: 'FAILED'
}

var usersTable = $('#admin-users-table');
var onlineCount = 0;

var adminId = 0;

function setAdminId(id) {
  adminId = id;
}

$(function () {
  usersTable.DataTable({
    "ajax": '/admin/users',
    "bPaginate": true,
    "bLengthChange": false,
    "bFilter": true,
    "bInfo": false,
    "bAutoWidth": true,
    "scrollX": true,
    "serverSide": true,
    "processing": true,
    "order": [[4, 'desc']],
    "searchDelay": 1500,
    "columnDefs": [
      {
        "targets": [2],
        "className": 'dt-body-right pr-5',
      }
    ],
    "pagingType": "numbers",
    "language": {
      "search": "",
      'lengthMenu': "_MENU_",
    },
    "dom":
      "<'row'<'col-4'l><'col-8'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-md-12'p>>",

    "columns": [
      {
        className: 'dt-control dt-body-left',
        orderable: false,
        data: "id",
        defaultContent: ''
      },
      {
        "data": "username"
      },
      {
        "data": null,
        render: (data) => {
          return data.points.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        },
      },
      {
        "data": "email"
      },
      {
        "data": "status"
      },
      {
        "data": null,
        render: (data) => {
          // return data.roles?.split('')
          let roles = '';
          let more = false;
          let display = 'inherit';
          data.roles.split(',').forEach((x, ctr) => {
            if (ctr > 2) {
              more = true;
              display = 'none';
            }

            roles += `<span style="display:${display}" class="badge bg-${x == 'Player' ? 'secondary' : 'danger'} mr-1">${x.replace(' Operator', '')}</span>`
          })

          return more
            ? `${roles} <span id="more-role" data-roles="${data.roles}" class="badge bg-primary">+${data.roles.split(',').length - 3}</span>`
            : roles;
        }
      },
      {
        "data": "last_activity"
      },
      {
        "data": null,
        render: (data) => {
          return `<a href="javascript:void(0)" data-id="${data.id}" class="btn btn-link text-primary btn-icon btn-sm view">
          <i class="fa-solid fa-circle-info"></i></a>`;
        }
      },
    ],
    "createdRow": function (row, data, dataIndex) {
      $(row).find('td').eq(0).attr('style', 'color: transparent !important');

      if (data.active) {
        $(row).addClass('table-success');
        onlineCount++;
      }

      $(row).find('td').eq(5).addClass('w-52');
    },
    "drawCallback": function (settings) {
      let response = settings.json;
      $('#badge-online-users').show().text(response.online_count);
      setAdminId(response.admin_id)
    },
    "preDrawCallback": function (settings) {
      $('.dataTables_filter input').on('keydown', function () {
        if (settings.jqXHR.readyState == 1) {
          settings.jqXHR.abort();
        }
      })
    },
  });


  function formatDeposit(d) {
    let points = d.points.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    let action = `<button id="access-user-btn" data-id="${d.id}" class="btn btn-link text-primary btn-icon" style="padding-left:0;">
      <i class="fa-solid fa-lock-open fa-lg"></i></button>`;
    let cashin = adminId == 1
      ? `<tr><td>CASHIN:</td>
          <td><button id="manual-ci-btn" data-id="${d.id}" class="btn btn-link text-primary btn-icon" style="padding-left:0;">
            cashin</button></td></tr>`
      : ``;
    return (
      `<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">
        <tr>
          <td>ID:</td>
          <td>#${d.id}</td>
        </tr>
        <tr>
          <td>PLAYER:</td>
          <td>${d.username}</td>
        </tr>
        <tr>
          <td>PHONE#:</td>
          <td>${d.phone_no}</td>
        </tr>
        <tr>
          <td>POINTS:</td>
          <td>${points}</td>
        </tr>
        <tr>
          <td>ACCESS:</td>
          <td>${action}</td>
        </tr>
        ${cashin}
      </table>`
    );
  }

  $('form#cashin-form').on('submit', async function (e) {
    try {
      e.preventDefault();
      let serialized = $(this).serialize();
      const loadUser = await axios.post('/admin/load-user', serialized);
      if (loadUser.data.status == 'success') {
        $('#modal-cashin').modal('hide')
        confirm('good job doy!')
        usersTable.DataTable().ajax.reload();
      }
    } catch (error) {
      $('#modal-cashin').modal('hide')
      alert('error! check logs fuck!')
    }
  });

  $('.cancel-ci').on('click', function () {
    $('#modal-cashin').modal('hide')
  })

  $('#admin-users-table tbody').on('click', 'td.dt-control', function () {
    var tr = $(this).closest('tr');
    var row = usersTable.DataTable().row(tr);

    if (row.child.isShown()) {
      row.child.hide();
      tr.removeClass('shown');
    }
    else {
      row.child(formatDeposit(row.data())).show();
      tr.addClass('shown');
    }
  });

  usersTable.on('click', 'tbody td #access-user-btn', function () {
    let id = $(this).data('id');
    Swal.fire({
      title: 'Login to this User?',
      showCancelButton: true,
      confirmButtonText: 'Yes',
    }).then((result) => {
      if (result.isConfirmed) {
        window.open('/admin/access/' + id, '_blank');
      }
      return;
    })
  })

  usersTable.on('click', 'tbody td #manual-ci-btn', function () {
    let id = $(this).data('id')
    $('#amount,#morph').val(0)
    $('#modal-cashin').modal('show')
    $('#id').val(id)
  })

  usersTable.on('click', 'tbody td .view', async function () {
    clearFields();
    var tr = $(this).closest('tr');
    var row = usersTable.DataTable().row(tr);
    var id = $(this).data('id');
    $('#modal-center').modal('show')
    $('.modal-title').text(row.data().username)
    $('input#user_id').val($(this).data('id'));
    $('input#username').val(row.data().username);
    $('input#phone_no').val(row.data().phone_no);
    $('.page-access').each((index, el) => $(el).prop('checked', false))

    getUserPermissions(id)
      .then((permissions) => {
        let perms = [];
        let user = permissions.data.user;
        permissions.data.data.forEach((p) => {
          perms.push(p.role_id);
        })
        $('input#name').val(user.id == 666 ? user.username : user.name);
        $('select#role').val(user.role_id);
        return perms;
      })
      .then((perms) => {
        for (let i = 0; i < perms.length; i++) {
          const el = perms[i];
          $('#page_access_' + el).prop('checked', true);
        }
      })

    let storage = $('#trans-receipt').data('storage');
    if (row.data().filename) {
      $('#trans-receipt').attr('src', storage + '/' + row.data().filename);
    }

    // if(row.data().status != 'pending') {
    //   $('input[type="submit"]').prop('disabled', true)
    //     .addClass('disabled');
    // } else {
    //   $('input[type="submit"]').prop('disabled', false)
    //     .removeClass('disabled');
    // }
  })

  usersTable.on('click', 'tbody td #more-role', function () {
    let more = $(this).parent().parent().find('.badge');
    console.log(more);
    more.each((_, el) => $(el).show())
    $(this).hide();
  })

  $('[data-dismiss="modal"]').on('click', function () {
    $('#modal-center').modal('hide');
  })

  function clearFields() {
    $('#updated-trans-pts').val(''), $('#trans-note-undo').val(''),
      $('#trans-note').parent().hide();
  }

  $('[data-dismiss="modal"]').on('click', function () {
    $('#modal-undo-points').modal('hide');
  })

  $('form#user-form').on('submit', function (e) {
    e.preventDefault();
    let serialized = $(this).serialize();
    updateUser(serialized).then((user) => {
      Swal.fire({
        icon: 'success',
        confirmButtonColor: 'green',
        title: user.data.message,
        timer: 1500
      }).then(() => {
        $('#modal-center').modal('hide');
        usersTable.DataTable().ajax.reload();
      });
    });
  });

  async function getUserPermissions(userId) {
    try {
      const response = await axios.get(`/admin/user-permissions/${userId}`);
      return response;
    }
    catch (error) {
      console.log(error);
    }
  }

  async function updateUser(data) {
    try {
      const user = await axios.post('/admin/user', data);
      // console.log(user);
      return user;
    }
    catch (error) {
      console.log(error.message);
    }

  }
})




// const accesUserBtn = document.getElementById('access-user-btn');
// if (accesUserBtn) {
//   accesUserBtn.addEventListener('click', function (e) {
//     e.preventDefault();
//     let id = $(this).data('id');
//     console.log(id, 'otin');
//     accessUser(id)
//   })
// }




