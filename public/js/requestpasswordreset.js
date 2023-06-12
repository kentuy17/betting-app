var requestsTable = $('#requests-table');
var wPending = 0;
var unverified = 0;

requestsTable.DataTable({
  "ajax": '/passwordreset-request/data',
  "bPaginate": true,
  "bLengthChange": false,
  "bFilter": false,
  "bInfo": false,
  "bAutoWidth": false,
  "scrollX": true,
  "columns": [
    {
      className: 'dt-control',
      orderable: false,
      data: null,
      defaultContent: '',
    },
    {
        "data": null,
        render: (data) => {
          return data.status.toUpperCase();
        }
    },
    {
        "data": 'username',
    },    
    {
        "data": "phone_no"
    },
    {
        "data": "password"
    },
    {
      "data": "created_at"
    },
    {
        "data": null,
        // render: (data) => {
        //   return data.action.toUpperCase();
        // }
    }
  ],
  "createdRow": function( row, data, dataIndex){
    if( data.status ==  `pending`){
      $(row).css({"background-color":"var(--bs-red)"});
      wPending++;
    }

    if( data.reference_code == null && data.status == 'completed') {
      $(row).addClass('bg-warning');
      unverified++;
    }

    if(wPending > 0) {
      $('#badge-withdraw').show().text(wPending);
    } else {
      $('#badge-withdraw').hide().text(wPending);
    }

    if(unverified > 0) {
      $('#badge-withdraw-unverified').show().text(unverified);
    } else {
      $('#badge-withdraw-unverified').hide().text(unverified);
    }
  }
});

function format(d) {
  var btnCopy = `<button data-bs-toggle="tooltip" title="Copied!" data-bs-trigger="click" class="btn btn-link text-primary btn-icon copy-phone" id="copy-phone" data-phone-number="${d.mobile_number}"
      onclick="copyPhone(this);"><i class="fa-solid fa-copy"></i></button>`;
  var expandContent = `<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">
      <tr>
        <td>MOBILE#:</td>
        <td>${d.mobile_number} ${btnCopy}</td>
      </tr>
      <tr>
        <td>AMOUNT:</td>
        <td>${d.amount}</td>
      </tr>
      <tr>
        <td>STATUS:</td>
        <td>${d.status.toUpperCase()}</td>
      </tr>
    </table>`;
  return expandContent;
}

$('#requests-table tbody').on('click', 'td.dt-control', function () {
  var tr = $(this).closest('tr');
  var row = requestsTable.DataTable().row(tr);

  if (row.child.isShown()) {
    row.child.hide();
    tr.removeClass('shown');
  }
  else {
    row.child(format(row.data())).show();
    tr.addClass('shown');
    $('[data-bs-toggle="tooltip"]').tooltip({placement:"top"})
  }
});

function copyPhone(e) {
  const num = $(e).data('phone-number');
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val(num).select();
  document.execCommand("copy");
  $temp.remove();
  $(e).removeClass('text-primary').addClass('text-success')
  $(e).find('i').removeClass('fa-copy').addClass('fa-check')
  setTimeout(() => {
    $(e).tooltip('hide')
    $(e).removeClass('text-success').addClass('text-primary')
    $(e).find('i').removeClass('fa-check').addClass('fa-copy')
  }, 3000);

}
