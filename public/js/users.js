const TYPE = {
  pending: 'PENDING',
  completed: 'COMPLETED',
  failed: 'FAILED'
}

var usersTable = $('#admin-users-table');
var onlineCount = 0;

usersTable.DataTable({
  "ajax": '/admin/users',
  "bPaginate": true,
  "bLengthChange": false,
  "bFilter": true,
  "bInfo": false,
  "bAutoWidth": true,
  "scrollX": true,
  "columns": [
    {
      className: 'dt-control',
      orderable: false,
      data: null,
      defaultContent: '',
    },
    {
      "data": "username"
    },
    {
      "data": "phone_no"
    },
    {
      "data": null,
      render: (data) => {
        let roles = '';
        data.roles.forEach((x) => {
          roles +=`<label class="badge bg-success mr-1">${x.name}</label>`
        })
        return roles;
      }
    },
    {
      "data": "points"
    },
    {
      "data": null,
      render: (data) => {
        return data.active ? 'ONLINE' : 'OFFLINE';
      }
    },
    {
      "data": "created_at"
    },
    {
      "data": null,
      render: (data) => {
          return `<a href="javascript:void(0)" data-id="${data.id}" class="btn btn-link text-primary btn-icon btn-sm view">
          <i class="fa-solid fa-circle-info"></i></a>`;
      }
    },
  ],
  "createdRow": function( row, data, dataIndex){
    if( data.active){
      $(row).addClass('table-success');
      onlineCount++;
    }

    if(data.roles.length > 1) {
      let rolesCol = $(row).find('td').eq(3);
      rolesCol.addClass('flex flex-wrap gap-1');
    }

    if(onlineCount > 0) {
      $('#badge-online-users').show().text(onlineCount);
    } else {
      $('#badge-online-users').hide().text(onlineCount);
    }
  }
});

function formatDeposit(d) {
  return (
    `<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">
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
        <td>${d.points}</td>
      </tr>
    </table>`
  );
}

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

usersTable.on('click', 'tbody td .view', async function() {
  clearFields();
  var tr = $(this).closest('tr');
  var row = usersTable.DataTable().row(tr);
  var id = $(this).data('id');
  $('#modal-center').modal('show')
  $('.modal-title').text(row.data().username)
  $('input#user_id').val($(this).data('id'));
  $('input#username').val(row.data().username);
  $('input#phone_no').val(row.data().phone_no);
  $('.page-access').each((index, el) => $(el).prop('checked',false))

  getUserPermissions(id)
    .then((permissions) => {
      let perms = [];
      let user = permissions.data.user;
      permissions.data.data.forEach((p) => {
        perms.push(p.role_id);
      })
      $('input#name').val(user.name);
      $('select#role').val(user.role_id);
      return perms;
    })
    .then((perms) => {
      for (let i = 0; i < perms.length; i++) {
        const el = perms[i];
        $('#page_access_'+el).prop('checked',true);
      }
    })

  let storage = $('#trans-receipt').data('storage');
  if(row.data().filename) {
    $('#trans-receipt').attr('src', storage+'/'+row.data().filename);
  }

  // if(row.data().status != 'pending') {
  //   $('input[type="submit"]').prop('disabled', true)
  //     .addClass('disabled');
  // } else {
  //   $('input[type="submit"]').prop('disabled', false)
  //     .removeClass('disabled');
  // }
})

function clearFields() {
  $('#trans-pts').val(''), $('#ref-code').val(''), $('#trans-note').val(''),
    $('#trans-action').val('approve'), $('#trans-note').parent().hide();
}

$('[data-dismiss="modal"]').on('click', function() {
  $('#modal-center').modal('hide');
})

function clearFields() {
  $('#updated-trans-pts').val(''), $('#trans-note-undo').val(''),
  $('#trans-note').parent().hide();
}

$('[data-dismiss="modal"]').on('click', function() {
  $('#modal-undo-points').modal('hide');
})

$('form#user-form').on('submit', function(e) {
  e.preventDefault();
  let serialized = $(this).serialize();
  updateUser(serialized).then((user) => {
    Swal.fire({
      icon: 'success',
      confirmButtonColor: 'green',
      title: user.data.message,
      timer: 1500
    }).then(() =>  {
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
