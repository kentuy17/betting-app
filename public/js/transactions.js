const TYPE = {
  pending: 'PENDING',
  completed: 'COMPLETED',
  failed: 'FAILED'
}

$(document).ready(function () {
  $('#transactions-table').DataTable({
    "ajax": '/transaction/records',
    "bPaginate": true,
    "bLengthChange": false,
    "bFilter": false,
    "bInfo": false,
    "bAutoWidth": false,
    "scrollX": true,
    "columns": [
      {
        "data": "id"
      },
      {
        "data": null,
        render: (data, type, row, meta) => {
          return data.status != `pending` ? `<i class="fa-solid check"></i>` : 
          `<a href="javascript:void(0)" onclick="javascript:postData(${row.id});" data-id="${row.id}" class="btn btn-link text-secondary btn-icon btn-sm edit"><i class="fa fa-check-square" aria-hidden="true"></i></a>`
        }
      },
      {
        "data": "action"
      },
      {
        "data": "processedBy"
      },
      {
        "data": "user.id"
      },
      {
        "data": "user.name"
      },
      {
        "data": "amount"
      },
      {
        "data": "mobile_number"
      },
      {
        "data": "created_at"
      },
      {
        "data": "status"
      },
    ],
    "createdRow": function( row, data, dataIndex){
      if( data.status ==  `pending`){
        $(row).css({"background-color":"red"});
      }
    }
  });

});


function postApproval(id) {
  data = {
    transId:id,
  }

  // $.ajax({
  //   headers: {
  //        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  //      },
  //   type: "POST",
  //   url: "/transactions/deposit/"+id,
  //   data: data,
  //   success: function (result) {
  //      console.log(result);
  //   },
  //   dataType: "json"
  // });
}

async function postData(id) {
  let data = {
    transId:id,
  };

  try {
    const response = await axios.post("/transactions/deposit/"+id, data);
    console.log("Request successful!");
  } catch (error) {
    if (error.response) {
      console.log(error.reponse.status);
    } else {
      console.log(error.message);
    }
  }
}

