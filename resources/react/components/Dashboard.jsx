import CardCommission from "./CardCommission";
import MenuIcon from "@mui/icons-material/Menu";
import Typography from "@mui/material/Typography";
import IconButton from "@mui/material/IconButton";
import Box from "@mui/material/Box";
import Drawer from "@mui/material/Drawer";
import List from "@mui/material/List";
import Divider from "@mui/material/Divider";
import ListItem from "@mui/material/ListItem";
import ListItemButton from "@mui/material/ListItemButton";
import ListItemIcon from "@mui/material/ListItemIcon";
import ListItemText from "@mui/material/ListItemText";
import InboxIcon from "@mui/icons-material/MoveToInbox";
import MailIcon from "@mui/icons-material/Mail";
import { useState, useEffect } from "react";
import axios from "@bundled-es-modules/axios/axios";

const drawerStyle = {
  boxShadow:
    "rgba(0, 0, 0, 0.2) 0px 8px 10px -5px, rgba(0, 0, 0, 0.14) 0px 16px 24px 2px, rgba(0, 0, 0, 0.12) 0px 6px 30px 5px",
  backgroundColor: "#343a40",
  height: "100%",
};

const Dashboard = () => {
  const [show, setShow] = useState(false);
  const [points, setPoints] = useState("0.00");
  const [commission, setCommission] = useState("0.00");
  const toggleDrawer = (open) => (event) => {
    if (
      event.type === "keydown" &&
      (event.key === "Tab" || event.key === "Shift")
    ) {
      return;
    }

    setShow(open);
  };

  const list = () => (
    <Box
      sx={drawerStyle}
      role="presentation"
      onClick={toggleDrawer(false)}
      onKeyDown={toggleDrawer(false)}
    >
      <List>
        {["Inbox", "Starred", "Send email", "Drafts"].map((text, index) => (
          <ListItem key={text} disablePadding>
            <ListItemButton>
              <ListItemIcon>
                {index % 2 === 0 ? (
                  <InboxIcon color="primary" />
                ) : (
                  <MailIcon color="primary" />
                )}
              </ListItemIcon>
              <ListItemText primary={text} />
            </ListItemButton>
          </ListItem>
        ))}
      </List>
      <Divider />
      <List>
        {["All mail", "Trash", "Spam"].map((text, index) => (
          <ListItem key={text} disablePadding>
            <ListItemButton>
              <ListItemIcon>
                {index % 2 === 0 ? (
                  <InboxIcon color="primary" />
                ) : (
                  <MailIcon color="primary" />
                )}
              </ListItemIcon>
              <ListItemText primary={text} />
            </ListItemButton>
          </ListItem>
        ))}
      </List>
    </Box>
  );

  useEffect(() => {
    axios.get("/master-agent/points").then((res) => {
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
            style={{ display: "flex", alignItems: "center" }}
          >
            <Typography
              variant="h6"
              noWrap
              component="div"
              sx={{ flexGrow: 1, display: { sm: "block" } }}
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
                title={"Points"}
                amount={points}
                linkText={"Manage"}
              />
              <CardCommission
                title={"Commission"}
                amount={commission}
                linkText={"Manage"}
              />
            </div>
          </div>
        </div>
      </div>
      <Drawer anchor={"left"} open={show} onClose={toggleDrawer(false)}>
        {list("left")}
      </Drawer>
    </>
  );
};

export default Dashboard;
