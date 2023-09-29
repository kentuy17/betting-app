import CardCommission from './CardCommission';
import MenuIcon from '@mui/icons-material/Menu';
import Typography from '@mui/material/Typography';
import IconButton from '@mui/material/IconButton';
import Box from '@mui/material/Box';
import Drawer from '@mui/material/Drawer';
import List from '@mui/material/List';
import Divider from '@mui/material/Divider';
import ListItem from '@mui/material/ListItem';
import ListItemButton from '@mui/material/ListItemButton';
import ListItemIcon from '@mui/material/ListItemIcon';
import ListItemText from '@mui/material/ListItemText';
import InboxIcon from '@mui/icons-material/MoveToInbox';
import MailIcon from '@mui/icons-material/Mail';
import { useState, useEffect } from 'react';
import axios from '@bundled-es-modules/axios/axios';
import { Link } from 'react-router-dom';
import LocalOfferRoundedIcon from '@mui/icons-material/LocalOfferRounded';
import AccountBalanceWalletRoundedIcon from '@mui/icons-material/AccountBalanceWalletRounded';
import AssignmentIndIcon from '@mui/icons-material/AssignmentInd';
import { Avatar } from '@mui/material';
import { Money } from '@mui/icons-material';

const drawerStyle = {
  boxShadow:
    'rgba(0, 0, 0, 0.2) 0px 8px 10px -5px, rgba(0, 0, 0, 0.14) 0px 16px 24px 2px, rgba(0, 0, 0, 0.12) 0px 6px 30px 5px',
  backgroundColor: '#343a40',
  height: '100%',
};

const Dashboard = () => {
  const [show, setShow] = useState(false);
  const [points, setPoints] = useState('0.00');
  const [commission, setCommission] = useState('0.00');
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
      role="presentation"
      onClick={toggleDrawer(false)}
      onKeyDown={toggleDrawer(false)}
    >
      <List>
        <LinkItemComponent
          title={'Cash-In'}
          link={'/master-agent/cashin'}
          icon={<Money color="primary" />}
        />
        <LinkItemComponent
          title={'Players'}
          link={'/master-agent/players'}
          icon={<AssignmentIndIcon color="primary" />}
        />
      </List>
    </Box>
  );

  useEffect(() => {
    axios.get('/master-agent/points').then((res) => {
      setPoints(res.data.points);
      setCommission(res.data.commission);
    });
  }, []);

  return (
    <>
      <div className="container">
        <div className="card">
          <div
            className="card-header font-bold"
            style={{ display: 'flex', alignItems: 'center' }}
          >
            <Typography
              variant="h6"
              noWrap
              component="div"
              sx={{ flexGrow: 1, display: { sm: 'block' } }}
            >
              DASHBOARD
            </Typography>
            <IconButton
              size="large"
              edge="start"
              color="inherit"
              aria-label="open drawer"
              sx={{ mr: 2 }}
              onClick={toggleDrawer(true)}
            >
              <MenuIcon />
            </IconButton>
          </div>
          <div className="card-body">
            <div className="col-lg-12">
              <CardCommission
                title={'Points'}
                amount={points}
                linkText={'Manage'}
                icon={<PointsIcon />}
              />
              <CardCommission
                title={'Commission'}
                amount={commission}
                linkText={'Manage'}
                icon={<CommissionIcon />}
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

const PointsIcon = () => {
  return (
    <Avatar sx={{ bgcolor: '#0d6efd', width: 60, height: 60 }}>
      <AccountBalanceWalletRoundedIcon />
    </Avatar>
  );
};

export default Dashboard;
