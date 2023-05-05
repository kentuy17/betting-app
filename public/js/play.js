$(function() {
  // console.log('fuck yeah');
  const useState = (defaultValue) => {
    let value = defaultValue;
    const getValue = () => value
    const setValue = newValue => value = newValue
    return [getValue, setValue];
  }
  
  const [fightNo, setFightNo] = useState(0);

  const [fightStatus, setFightStatus] = useState('____');

  $.ajax({
    url: 'fight/current',
    type: 'GET',
    data: {},
    success: async function(response){
      var statusDiv = $('#player-fight-status').removeClass('gradient-status-open gradient-status-close');
      await setFightNo(response.data.fight_no);
      $('#fight-no').html(fightNo());

      if(response.data.status == 'C') {
        await setFightStatus('CLOSED');
        statusDiv.addClass('gradient-status-close');
        $('#done-fight').removeClass('disabled').prop('disabled',false);
      }

      if(response.data.status == 'O') {
        await setFightStatus('OPEN');
        statusDiv.addClass('gradient-status-open')
      }

      statusDiv.html(fightStatus());
      $('#fight-status').html(fightStatus());
    }
  });

  $('#done-fight').on('click', function(e) {
    e.preventDefault();
    Swal.fire({
      title: 'RESULT:',
      showCancelButton: true,
      showCloseButton: false,
      showDenyButton: true,
      allowOutsideClick: false,
      confirmButtonText: 'MERON',
      confirmButtonColor: 'red',
      denyButtonText: 'WALA',
      denyButtonColor: 'blue',
      cancelButtonText: 'DRAW',
      allowEscapeKey: false
    }).then((result) => {
      if (result.isConfirmed) {
        alert('MERON WINS')
        return 'M';
      } else if (result.isDenied) {
        alert('WALA WINS');
        return 'W';
      } else {
        alert('DRAW');
        return 'D';
      }
    }).then((result) => {
      updateFightStatus(done='D',result);
    });

    // 
    // $(this).addClass('disabled').prop('disabled',true);
    // $('#open-fight').removeClass('disabled').prop('disabled', false);
    // $('#done-fight').removeClass('disabled').prop('disabled', false);

  });

  fetch('js/results.json') // change to ajax later
    .then((response) => response.json())
    .then((json) => {
      for (var j = 1; j <= 7; j++) {
        var tr = $('<tr>');
        for (var i = 1; i <= 200; i++) {
          tr.append('<td> <div id="tdBaccaratAllConsecutive-' + j + i + '"></div></td>');
        }
        tr.append("</tr>");
        $('#tblBaccaratResultConsecutive').append(tr);

      }
      var dataArr = Object.values(json); 
      var y = 1;
      var c = 1;
      for (var x = 1; x < dataArr.length; x++) {
        var element = document.getElementById("tdBaccaratAllConsecutive-" + y + c);
        if (!element) {
          return;
        }
        if (dataArr[x - 1][0] == 'Meron Wins') {
          element.classList.add("circleRedAll");
          element.innerHTML = dataArr[x-1][1];
        } else if (dataArr[x - 1][0] == 'Wala Wins') {
          element.classList.add("circleBlueAll");
          element.innerHTML = dataArr[x-1][1];
        } else if (dataArr[x - 1][0] == 'Draw') {
          element.classList.add("circleGreenAll");
          element.innerHTML = dataArr[x-1][1];
        } else if (dataArr[x - 1][0] == 'Cancelled') {
          element.classList.add("circleCancelAll");
          element.innerHTML = dataArr[x-1][1];
        } else if (dataArr[x - 1][0] == 'Pending') {
          element.classList.add("circleCancelAll");
          element.innerHTML = dataArr[x-1][1];
        }
        if(dataArr[x - 1][0] == dataArr[x][0]){
          if (y == 7){ 
            y = 1;
            c++;
          } else {
            y = y + 1;
          }
        } else {
          y = 1;
          c++;
        }
      }
    });

});
