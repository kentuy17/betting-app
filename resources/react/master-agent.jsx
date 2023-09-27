import { createRoot } from "react-dom/client";
import React from "react";
import { BrowserRouter } from "react-router-dom";

// components
import Dashboard from "./components/Dashboard";

// Render your React component instead
createRoot(document.getElementById("master-agent")).render(
  <React.StrictMode>
    <BrowserRouter>
      <Dashboard />
    </BrowserRouter>
  </React.StrictMode>
);
