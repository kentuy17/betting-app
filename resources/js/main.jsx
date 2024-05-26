import { createRoot } from 'react-dom/client';
import React, { useEffect, useState } from 'react';
import { BrowserRouter, Route, Routes } from 'react-router-dom';
import PsuedoDashboard from './react/pages/dashboard';

import './../css/app.css'

const App = () => {
  const [agentType, setAgentType] = useState('agent');

  useEffect(() => {
    fetch('/master-agent/user')
      .then((res) => res.json())
      .then((data) => setAgentType(data.type));

    console.log(agentType);
  }, []);

  return (
    <Routes>
      <Route path="/users-list" element={<PsuedoDashboard />} />
      {/* <Route path="/master-agent/cashin" element={<CashIn />} />
      <Route
        path="/master-agent/players"
        element={agentType == 'master-agent' ? <MastersPlayers /> : <Players />}
      /> */}
    </Routes>
  );
};


// Render your React component instead
createRoot(document.getElementById('users-list')).render(
  <React.StrictMode>
    <BrowserRouter>
      <App />
    </BrowserRouter>
  </React.StrictMode>
);
