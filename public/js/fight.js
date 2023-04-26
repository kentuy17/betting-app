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
        console.log(fightStatus(), 'fightStatus');
        $('#done-fight').removeClass('disabled').prop('disabled',false);
      }

      if(response.data.status == 'O') {
        await setFightStatus('OPEN');
      }

      $('#fight-status').html(fightStatus());
    }
  });

  function updateFightStatus(status) {
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:'POST',
      data: {status: status},
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
    updateFightStatus('D');
    $(this).addClass('disabled').prop('disabled',true);
    $('#open-fight').removeClass('disabled').prop('disabled', false);
    $('#done-fight').removeClass('disabled').prop('disabled', false);
  });
});
