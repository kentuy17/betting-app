$(function () {
  const useState = (defaultValue) => {
    let value = defaultValue;
    const getValue = () => value
    const setValue = newValue => value = newValue
    return [getValue, setValue];
  }

  const [fightNo, setFightNo] = useState(0);

  const [fightStatus, setFightStatus] = useState('____');

  const [lastWinner, setLastWinner] = useState('');

  const [lastPostion, setLastPosition] = useState('');

  $('#done-fight').on('click', function (e) {
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
      updateFightStatus(done = 'D', result);
    });

  });

  fetch('/fight/results') // change to ajax later
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
        if (!element) return;
        if (dataArr[x - 1][0] == 'Meron Wins') {
          element.classList.add("circleRedAll");
          element.innerHTML = dataArr[x - 1][1];
        } else if (dataArr[x - 1][0] == 'Wala Wins') {
          element.classList.add("circleBlueAll");
          element.innerHTML = dataArr[x - 1][1];
        } else if (dataArr[x - 1][0] == 'Draw') {
          element.classList.add("circleGreenAll");
          element.innerHTML = dataArr[x - 1][1];
        } else if (dataArr[x - 1][0] == 'Cancelled') {
          element.classList.add("circleCancelAll");
          element.innerHTML = dataArr[x - 1][1];
        } else if (dataArr[x - 1][0] == 'Pending') {
          element.classList.add("circleCancelAll");
          element.innerHTML = dataArr[x - 1][1];
        }
        if (x == dataArr.length - 1) {
          setLastPosition({ y: y, c: c });
        }
        if (dataArr[x - 1][0] == dataArr[x][0]) {
          if (y == 7)
            y = 1, c++;
          else
            y = y + 1;
        } else {
          y = 1;
          c++;
        }
      }
      getLastWinner(dataArr);
    });

  function getLastWinner(dataArr) {
    if (dataArr[dataArr.length - 2][0] == 'Meron Wins') {
      setLastWinner('M');
    }

    if (dataArr[dataArr.length - 2][0] == 'Wala Wins') {
      setLastWinner('W');
    }

    if (dataArr[dataArr.length - 2][0] == 'Draw') {
      setLastWinner('D');
    }

    if (dataArr[dataArr.length - 2][0] == 'Cancelled') {
      setLastWinner('C');
    }
  }

  window.Echo.channel('fight')
    .listen('.fightUpdated', async (e) => {
      let prev = e.fight.prev;
      if (prev) {
        var pos, p;
        if (lastWinner() == e.fight.prev.game_winner && lastPostion().y != 7) {
          pos = $(`#tdBaccaratAllConsecutive-${lastPostion().y + 1}${lastPostion().c}`)
          p = { y: lastPostion().y + 1, c: lastPostion().c }
        } else {
          pos = $(`#tdBaccaratAllConsecutive-1${lastPostion().c + 1}`)
          p = { y: 1, c: lastPostion().c + 1 }
        }

        pos.html(e.fight.prev.fight_no);

        if (e.fight.prev.game_winner == 'M') {
          pos.addClass('circleRedAll')
        }
        else if (e.fight.prev.game_winner == 'W') {
          pos.addClass('circleBlueAll')
        }
        else if (e.fight.prev.game_winner == 'D') {
          pos.addClass('circleGreenAll')
        }
        else {
          pos.addClass('circleCancelAll')
        }

        setLastWinner(e.fight.prev.game_winner)
        setLastPosition(p)
      }
    })


});

