$(document).ready(function () {
  const corpoTable = $("#corpo-table");

  corpoTable.DataTable({
    bPaginate: true,
    bLengthChange: false,
    bFilter: false,
    bInfo: false,
    bAutoWidth: false,
    ajax: "/admin/incorpo-list",
    scrollX: true,
    pageLength: 25,
    columnDefs: [
      {
        targets: [3],
        className: "dt-body-right",
      },
      {
        targets: [1, 2, 4],
        className: "dt-body-center",
      },
    ],
    columns: [
      {
        data: "user_id",
      },
      {
        data: "user.username",
      },
      {
        data: null,
        render: (row) => {
          return `<a href="#" onclick="viewAgents(${row.user_id},'${row.bracket}')" class="link-primary corpo-bracket-name" data-corpo="${row.user_id}">${row.bracket}</a>`;
        },
      },
      {
        data: null,
        render: (row) => {
          // return row.current_commission.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
          return "0.00";
        },
      },
      {
        data: "player_count",
      },
      {
        data: "created_at",
      },
      {
        data: null,
        render: (data, type, row, meta) => {
          let act =
            data.status == "ACTIVE"
              ? `<i class="fa-solid fa-stop"></i>`
              : `<i class="fa-solid fa-eye"></i>`;
          return `<a href="javascript:void(0)" data-id="${row.user_id}" data-bracket="${row.bracket}" class="btn btn-link text-primary btn-icon btn-sm view">${act}</a>
            <a href="javascript:void(0)" data-id="${row.user_id}" class="btn btn-link text-secondary btn-icon btn-sm edit"><i class="fa-solid fa-pencil"></i></a>
            <a href="javascript:void(0)" data-id="${row.user_id}" class="btn btn-link text-success btn-icon btn-sm plus"><i class="fa-solid fa-plus"></i></a>`;
        },
      },
    ],
    createdRow: function (row, data, dataIndex) {
      $(row).find("td").eq(3).attr("style", "color: yellow !important");
    },
  });

  $("#time-start").val("09:00");
  $("#sched-date").val(moment().format("YYYY-MM-DD"));

  $("#add-corpo").on("click", function (e) {
    if ($("#corpo-id").val() == "") {
      alert("Please choose corpo!");
      $(this).focus();
      return;
    }

    e.preventDefault();
    data = {
      user_id: $("#corpo-id").val(),
      bracket: $("#bracket-name").val(),
    };

    $.ajax({
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      type: "POST",
      data: data,
      url: "/admin/add-corpo-agent",
      success: function (resp) {
        corpoTable.DataTable().ajax.reload();
        alert("Corpo Added!");
        $("#corpo-id").val("");
        $("#bracket-name").val("");
      },
      error: function (err) {
        console.log(err);
      },
    });
  });

  // View Agents
  corpoTable.on("click", "tbody td .view", async function () {
    viewAgents($(this).data('id'),$(this).data('bracket'));
  });

  // Add Agents Under Corpo
  corpoTable.on("click", "tbody td .plus", async function (e) {
    e.preventDefault();
    let agentCount = parseInt(prompt("How many?", "0"));
    console.log(agentCount);
    try {
      var id = $(this).data("id");
      response = await axios.post("/admin/add-agents", {
        user_id: id,
        agent_count: agentCount,
      });
      Swal.fire({
        icon: "success",
        confirmButtonColor: "green",
        title: response.data.message,
      }).then(() => {
        corpoTable.DataTable().ajax.reload();
      });
    } catch (error) {
      Swal.fire({
        icon: "error",
        confirmButtonColor: "red",
        title: error.response.data.message,
      });
    }
  });
});

$('[data-dismiss="modal"]').on('click', function() {
  $('#sub-agents-modal').modal('hide');
})

async function viewAgents(agentId, bracketName) {
  const corpoId = agentId;
  const subAgentsTable = $("#sub-agents-table");
  await subAgentsTable.DataTable().clear().destroy();
  $("#sub-agents-modal").modal("show");
  $("#bracket-head").text(bracketName);
  subAgentsTable.DataTable({
    bPaginate: true,
    bLengthChange: true,
    bFilter: true,
    bInfo: false,
    bAutoWidth: true,
    scrollX: true,
    ajax: "/admin/sub-agents/" + corpoId,
    pagingType: "numbers",
    processing: true,
    serverSide: true,
    order: [[3, "desc"]],
    language: {
      search: "",
      lengthMenu: "_MENU_",
    },
    dom:
      "<'row'<'col-4'l><'col-8'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    columnDefs: [
      {
        targets: [1, 2, 3, 4, 5],
        className: "dt-body-center",
      },
    ],
    columns: [
      {
        data: "user_id",
      },
      {
        data: "user.username",
      },
      {
        data: "default_pass",
      },
      {
        data: null,
        render: (row) => {
          return row.agent_commission ? row.agent_commission.commission
          .toFixed(2)
          .replace(/\d(?=(\d{3})+\.)/g, "$&,") : '0.00';
        },
      },
      {
        data: "player_count",
      },
      {
        data: "user.rid",
      },
      {
        data: "created_at",
      },
    ],
    // createdRow: function (row, data, dataIndex) {
    //   if (data.status == `W`) {
    //     $(row).find("td").eq(4).attr("style", "color: green !important");
    //     $(row).find("td").eq(5).attr("style", "color: yellow !important");
    //   }

    //   if (data.status == `L`) {
    //     $(row).find("td").eq(4).attr("style", "color: red !important");
    //   }

    //   if (data.side == "M") {
    //     $(row).find("td").eq(1).attr("style", "color: red !important");
    //   }

    //   if (data.side == "W") {
    //     $(row).find("td").eq(1).attr("style", "color: blue !important");
    //   }
    // },
  });
}
