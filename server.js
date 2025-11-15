// server.js
const express = require('express');
const app = express();

// Gebruik de poort van Render, of 3000 lokaal
const PORT = process.env.PORT || 3000;

// Route voor de homepage
app.get('/', (req, res) => {
  res.send('ok');
});

// Server starten
app.listen(PORT, () => {
  console.log(`Server draait op poort ${PORT}`);
});