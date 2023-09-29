import { createRoot } from 'react-dom/client';
import React from 'react';
import { BrowserRouter, Route, Routes } from 'react-router-dom';

// components
import Dashboard from './components/Dashboard';
import CashIn from './components/CashIn';
import Players from './components/Players';

const MasterAgent = () => {
  return (
    <Routes>
      <Route path="/master-agent" element={<Dashboard />} />
      <Route path="/master-agent/cashin" element={<CashIn />} />
      <Route path="/master-agent/players" element={<Players />} />
    </Routes>
  );
};

// Render your React component instead
createRoot(document.getElementById('master-agent')).render(
  <React.StrictMode>
    <BrowserRouter>
      <MasterAgent />
    </BrowserRouter>
  </React.StrictMode>
);
