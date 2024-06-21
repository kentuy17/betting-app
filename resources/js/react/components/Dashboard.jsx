import CardCommission from './CardCommission';
// import MenuIcon from '@mui/icons-material/Menu';
import Typography from '@mui/material/Typography';
// import IconButton from '@mui/material/IconButton';
import Box from '@mui/material/Box';
import Drawer from '@mui/material/Drawer';
import List from '@mui/material/List';
// import Divider from '@mui/material/Divider';
import ListItem from '@mui/material/ListItem';
import ListItemButton from '@mui/material/ListItemButton';
import ListItemIcon from '@mui/material/ListItemIcon';
import ListItemText from '@mui/material/ListItemText';
// import InboxIcon from '@mui/icons-material/MoveToInbox';
// import MailIcon from '@mui/icons-material/Mail';
import { useState, useEffect } from 'react';
import axios from '@bundled-es-modules/axios/axios';
import { Link, useNavigate } from 'react-router-dom';
import LocalOfferRoundedIcon from '@mui/icons-material/LocalOfferRounded';
import AccountBalanceWalletRoundedIcon from '@mui/icons-material/AccountBalanceWalletRounded';
import PeopleRoundedIcon from '@mui/icons-material/PeopleRounded';
import AssignmentIndIcon from '@mui/icons-material/AssignmentInd';
import CurrencyExchangeIcon from '@mui/icons-material/CurrencyExchange';
import { FileCopyRounded } from '@mui/icons-material';
import { Avatar, TextField } from '@mui/material';
import { Money } from '@mui/icons-material';

const drawerStyle = {
  boxShadow:
    'rgba(0, 0, 0, 0.2) 0px 8px 10px -5px, rgba(0, 0, 0, 0.14) 0px 16px 24px 2px, rgba(0, 0, 0, 0.12) 0px 6px 30px 5px',
  backgroundColor: '#343a40',
  height: '100%',
};

const Dashboard = () => {
  const navigate = useNavigate();
  const [show, setShow] = useState(false);
  const [points, setPoints] = useState('0.00');
  const [commission, setCommission] = useState('0.00');
  const [playerCnt, setPlayerCnt] = useState('0');
  const [refLink, setRefLink] = useState('');
  const [refLinkTxt, setRefLinkTxt] = useState('');
  const [refCopyTxt, setRefCopyTxt] = useState('Copy Link');

  const toggleDrawer = (open) => (event) => {
    if (
      event.type === 'keydown' &&
      (event.key === 'Tab' || event.key === 'Shift')
    ) {
      return;
    }

    setShow(open);
  };

  const LinkItemComponent = ({ title, link, icon }) => {
    return (
      <ListItem key={title} disablePadding>
        <ListItemButton to={link} component={Link}>
          <ListItemIcon>{icon}</ListItemIcon>
          <ListItemText primary={title} />
        </ListItemButton>
      </ListItem>
    );
  };

  const list = () => (
    <Box
      sx={drawerStyle}
      role='presentation'
      onClick={toggleDrawer(false)}
      onKeyDown={toggleDrawer(false)}
    >
      <List>
        <LinkItemComponent
          title={'Cash-In'}
          link={'/master-agent/cashin'}
          icon={<Money color='primary' />}
        />
        <LinkItemComponent
          title={'Players'}
          link={'/master-agent/players'}
          icon={<AssignmentIndIcon color='primary' />}
        />
      </List>
    </Box>
  );

  useEffect(() => {
    axios.get('/master-agent/points').then((res) => {
      setPoints(res.data.points);
      setCommission(res.data.commission);
      setPlayerCnt(res.data.players);
      setRefLinkTxt(res.data.ref_link);
      setRefLink(
        <TextField
          disabled
          id='outlined-disabled'
          defaultValue={res.data.ref_link}
          fullWidth
          variant='standard'
          sx={{ fontStyle: 'italic', cursor: 'pointer' }}
        />
      );
    });
  }, []);

  const handlePointsClick = () => {
    window.location.href = '/deposit';
  };

  const handleCommissionClick = async (e) => {
    try {
      e.preventDefault();
      let pointsToConvert = prompt('Enter points to convert:', 0);
      if (
        pointsToConvert == null ||
        (pointsToConvert == '') | (pointsToConvert == 0)
      ) {
        alert('Minimum 200 points');
      }

      axios
        .post('/agent/commission-convert', {
          points: pointsToConvert,
        })
        .then((convert) => {
          setCommission(convert.data.data.current_commission);
          setPoints(convert.data.data.points);
          alert('Successfully converted into points!');
        });
    } catch (error) {
      let errMsg = error.response?.data?.message;
      alert(errMsg);
    }
  };

  const handlePlayerClick = () => {
    navigate('/master-agent/players');
  };

  const handleCopyLink = async () => {
    await navigator.clipboard.writeText(refLinkTxt);
    setRefCopyTxt('Copied');
  };

  return (
    <>
      <div className='container'>
        <div className='card'>
          <div
            className='card-header font-bold'
            style={{ display: 'flex', alignItems: 'center' }}
          >
            <Typography
              variant='h6'
              noWrap
              component='div'
              sx={{ flexGrow: 1, display: { sm: 'block' } }}
            >
              AGENT DASHBOARD
            </Typography>
          </div>
          <div className='card-body'>
            <div className='col-lg-12'>
              <CardCommission
                title={'Points'}
                amount={points}
                linkText={'Manage'}
                icon={<PointsIcon />}
                onClick={handlePointsClick}
                tooltip='Manage Points'
              />
              <CardCommission
                title={'Referral Link'}
                amount={refLink}
                linkText={refCopyTxt}
                icon={<CopyIcon />}
                onClick={handleCopyLink}
                tooltip='Copy Link'
              />
              <CardCommission
                title={'Commission'}
                amount={commission}
                linkText={'Convert to points'}
                linkIcon={
                  <CurrencyExchangeIcon
                    fontSize='small'
                    sx={{ marginRight: 0.5 }}
                  />
                }
                icon={<CommissionIcon />}
                onClick={handleCommissionClick}
                tooltip='Manage Commission'
              />
              <CardCommission
                title={'Players'}
                amount={playerCnt.toString()}
                linkText={'View'}
                icon={<PlayerIcon />}
                onClick={handlePlayerClick}
                tooltip='View Players'
              />
            </div>
          </div>
        </div>
      </div>
      <Drawer anchor={'left'} open={show} onClose={toggleDrawer(false)}>
        {list('left')}
      </Drawer>
    </>
  );
};

const CommissionIcon = () => {
  return (
    <Avatar sx={{ bgcolor: '#0d6efd', width: 60, height: 60 }}>
      <LocalOfferRoundedIcon />
    </Avatar>
  );
};

const CopyIcon = () => {
  return (
    <Avatar sx={{ bgcolor: '#0d6efd', width: 60, height: 60 }}>
      <FileCopyRounded />
    </Avatar>
  );
};

const PointsIcon = () => {
  return (
    <Avatar sx={{ bgcolor: '#0d6efd', width: 60, height: 60 }}>
      <AccountBalanceWalletRoundedIcon />
    </Avatar>
  );
};

const PlayerIcon = () => {
  return (
    <Avatar sx={{ bgcolor: '#0d6efd', width: 60, height: 60 }}>
      <PeopleRoundedIcon />
    </Avatar>
  );
};

export default Dashboard;
