import { createRoot } from 'react-dom/client';
import React, { useEffect, useState } from 'react';
import { BrowserRouter, Route, Routes } from 'react-router-dom';

// components
import Dashboard from './components/Dashboard';
import CashIn from './components/CashIn';
import Players from './components/Players';
import MastersPlayers from './components/MastersPlayers';

const MasterAgent = () => {
  const [agentType, setAgentType] = useState('agent');

  useEffect(() => {
    fetch('/master-agent/user')
      .then((res) => res.json())
      .then((data) => setAgentType(data.type));
  }, []);

  return (
    <Routes>
      <Route path="/master-agent" element={<Dashboard />} />
      <Route path="/master-agent/cashin" element={<CashIn />} />
      <Route
        path="/master-agent/players"
        element={agentType == 'master-agent' ? <MastersPlayers /> : <Players />}
      />
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
