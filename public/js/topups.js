var topupsTable = $("#agent-trans-table");
var pendingCount = 0;
var unpaidCount = 0;

function delay(time) {
  return new Promise((resolve) => setTimeout(resolve, time));
}

topupsTable.DataTable({
  ajax: "/transaction/topups",
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
  order: [[5, "DESC"]],
  dom:
    "<'row'<'col-4'l><'col-8'f>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-md-12'p>>",
  preInit: function (e, settings) {
    pendingCount = 0;
    unpaidCount = 0;
  },
  columnDefs: [
    {
      targets: [1],
      className: "dt-body-right",
    },
  ],
  columns: [
    // {
    //   className: 'dt-control dt-body-left',
    //   orderable: false,
    //   data: null,
    //   defaultContent: '',
    //   data: "user_id",
    // },
    {
      data: "user.name",
    },
    // {
    //   "data": "outlet"
    // },
    {
      data: null,
      render: (data) => {
        return data.amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,");
      },
    },
    {
      data: "mobile_number",
    },

    {
      data: null,
      render: (data) => {
        return data.operator != null ? data.operator.username : "--";
      },
    },

    {
      data: "created_at",
    },
    {
      data: null,
      render: (data) => {
        // if (data.status == "completed" && data.completed_at != null) {
        //   return `<a href="javascript:void(0)" data-id="${data.id}" class="btn btn-link text-primary btn-icon btn-sm view">
        //       <i class="fa-solid fa-circle-info"></i></a>
        //       <a href="javascript:void(0)" data-id="${data.id}" class="btn btn-link text-primary btn-icon btn-sm view-undo">
        //       <i class="fa-solid fa-undo"></i></a>`;
        // }
        return `<a href="javascript:void(0)" data-id="${data.id}" class="btn btn-link text-primary btn-icon btn-sm">
          <i class="fa-solid fa-circle-info"></i></a>`;
      },
    },
  ],
  createdRow: function (row, data, dataIndex) {
    $(row).attr("data-id", data.id).addClass("cursor-pointer expandable");
    if (data.status == `pending`) {
      $(row).css({ "background-color": "var(--bs-red)" });
      pendingCount++;

      let timeDiff = moment(data.created_at, "MM-DD-YYYY hh:mm:ss").fromNow();
      $(row).find("td").eq(5).text(timeDiff);

      setInterval(() => {
        timeDiff = moment(data.created_at, "MM-DD-YYYY hh:mm:ss").fromNow();
        $(row).find("td").eq(5).text(timeDiff);
      }, 5000);
    }

    if (data.status == `failed`) {
      $(row).addClass("failed");
    }
  },
});

function formatTopup(d) {
  let userId = d.user_id;
  let userName = d.user.username;

  if (d.user_id == 666) {
    userId = DUMMY_ID;
    userName = d.user.name;
  }
  let note = d.note ? `<tr><td>NOTE:</td><td>${d.note}</td></tr>` : "";
  let refCode =
    d.reference_code && d.reference_code != null
      ? `<tr><td>REFCODE:</td><td>${d.reference_code}</td></tr>`
      : "";
  return `<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">
      <tr>
        <td>ID:</td>
        <td>#${userId}</td>
      </tr>
      <tr>
        <td>PLAYER:</td>
        <td>${userName}</td>
      </tr>
      <tr>
        <td>MOBILE#:</td>
        <td>${d.mobile_number} </td>
      </tr>
      <tr>
        <td>AMOUNT:</td>
        <td>${d.amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,")}</td>
      </tr>
      ${note}
      ${refCode}
    </table>`;
}

topupsTable.on("click", "tr.expandable", function () {
  var tr = $(this);
  var row = topupsTable.DataTable().row(tr[0]);
  if (row.child.isShown()) {
    row.child.hide();
    tr.removeClass("shown");
  } else {
    row.child(formatTopup(row.data())).show();
    tr.addClass("shown");
  }
});

topupsTable.on("click", "tbody td .view", async function () {
  var tr = $(this).closest("tr");
  var row = topupsTable.DataTable().row(tr);
  $("#modal-center").modal("show");
  $(".modal-title").text(row.data().action.toUpperCase());
  $("input#trans-id").val($(this).data("id"));

  let storage = $("#trans-receipt").data("storage");
  if (row.data().filename) {
    $("#trans-receipt").attr("src", storage + "/" + row.data().filename);
  }

  if (row.data().status != "pending") {
    $('input[type="submit"]').prop("disabled", true).addClass("disabled");
  } else {
    $('input[type="submit"]').prop("disabled", false).removeClass("disabled");
  }
});

async function viewTopup(id) {
  clearFields();
  var tr = $("tbody").find(`tr[data-id='${id}']`);
  var row = topupsTable.DataTable().row(tr[0]);
  $("#modal-center").modal("show");
  $(".modal-title").text(row.data().action.toUpperCase());
  $("input#trans-id").val(row.data().id);

  let storage = $("#trans-receipt").data("storage");
  if (row.data().filename) {
    $("#trans-receipt").attr("src", storage + "/" + row.data().filename);
  }
}

function clearFields() {
  $("#trans-pts").val("");
  $("#ref-code").val("");
  $("#trans-note").val("");
  $("#trans-action").val("approve");
  $("#trans-note").parent().hide();
  $("#withdraw-note").val("");
  $("#withdraw-ref-code").val("");
  $("#manual-request-ref").val("");
  $("#manual-request-note").val("");
  $("#manual-request-amount").val("");
  $("#player-username").text("");
}

function isNumeric(str) {
  if (typeof str != "string") return false; // we only process strings!
  return (
    !isNaN(str) && // use type coercion to parse the _entirety_ of the string (`parseFloat` alone does not do this)...
    !isNaN(parseFloat(str))
  ); // ...and ensure strings of whitespace fail
}

// $("#allow-notifications").on("click", () => {
//   if (!("Notification" in window)) {
//     // Check if the browser supports notifications
//     alert("This browser does not support desktop notification");
//   } else if (Notification.permission === "granted") {
//     // Check whether notification permissions have already been granted;
//     // if so, create a notification
//     const notification = new Notification("Hi there!");
//     // …
//   } else if (Notification.permission !== "denied") {
//     // We need to ask the user for permission
//     Notification.requestPermission().then((permission) => {
//       // If the user accepts, let's create a notification
//       if (permission === "granted") {
//         const notification = new Notification("Hi there!");
//         // …
//       }
//     });
//   }
// });
