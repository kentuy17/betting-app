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
      await setFightNo(response.data.fight_no);
      $('#fight-no').html(fightNo());

      if(response.data.status == 'C') {
        await setFightStatus('CLOSED');
        $('#done-fight').removeClass('disabled').prop('disabled',false);
      }

      if(response.data.status == 'O') {
        await setFightStatus('OPEN');
      }

      $('#fight-status').html(fightStatus());
    }
  });

  function updateFightStatus(status,result=null) {
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:'POST',
      data: {status:status, result:result},
      url: '/fight/update-status',
      success: async function(resp) {
        if(status == 'C') {
          await setFightStatus('CLOSED');
        }
  
        if(status == 'O') {
          await setFightStatus('OPEN');
        }

        if(status == 'D') {
          await setFightStatus('____');
          await setFightNo(resp.data.fight_no);
          $('#fight-no').html(fightNo());
        }
  
        $('#fight-status').html(fightStatus());
      },
      error: function (request, status, error) {
        console.log(error);
      }

    })
  }

  $('#open-fight').on('click', function(e) {
    e.preventDefault();
    updateFightStatus('O');
    $(this).addClass('disabled').prop('disabled',true);
    $('#close-fight').removeClass('disabled').prop('disabled', false);
  });

  $('#close-fight').on('click', function(e) {
    e.preventDefault();
    updateFightStatus('C');
    $(this).addClass('disabled').prop('disabled',true);
    $('#open-fight').removeClass('disabled').prop('disabled', false);
    $('#done-fight').removeClass('disabled').prop('disabled', false);
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
});
