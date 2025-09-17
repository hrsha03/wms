import express from 'express';
import bcrypt from 'bcryptjs';
import jwt from 'jsonwebtoken';
import pool from './db.js';

const router = express.Router();
const JWT_SECRET = process.env.JWT_SECRET || 'changeme';

// Signup
router.post('/signup', async (req, res) => {
  const { name, email, username, password } = req.body;
  if (!name || !email || !username || !password) return res.status(400).json({ message: 'Missing fields' });
  try {
    const [rows] = await pool.query('SELECT id FROM users WHERE username = ?', [username]);
    if (rows.length > 0) return res.status(409).json({ message: 'Username exists' });
    const hash = await bcrypt.hash(password, 10);
    await pool.query(
      'INSERT INTO users (name, email, username, password) VALUES (?, ?, ?, ?)',
      [name, email, username, hash]
    );
    return res.status(201).json({ message: 'Signup successful' });
  } catch (err) {
    return res.status(500).json({ message: 'Server error' });
  }
});

// Login
router.post('/login', async (req, res) => {
  const { username, password } = req.body;
  if (!username || !password) return res.status(400).json({ message: 'Missing fields' });
  try {
    const [rows] = await pool.query('SELECT * FROM users WHERE username = ?', [username]);
    if (rows.length === 0) return res.status(401).json({ message: 'Invalid credentials' });
    const user = rows[0];
    const match = await bcrypt.compare(password, user.password);
    if (!match) return res.status(401).json({ message: 'Invalid credentials' });
    const token = jwt.sign({
      id: user.id,
      username: user.username,
      name: user.name,
      email: user.email,
      member_since: user.member_since
    }, JWT_SECRET, { expiresIn: '1h' });
    return res.json({
      token,
      username: user.username,
      name: user.name,
      email: user.email,
      member_since: user.member_since
    });
  } catch (err) {
    return res.status(500).json({ message: 'Server error' });
  }
});

// Logout (client should just delete token)
router.post('/logout', (req, res) => {
  // JWT is stateless; instruct client to delete token
  return res.json({ message: 'Logged out' });
});

// Auth check (returns all user details)
router.get('/me', async (req, res) => {
  const auth = req.headers.authorization;
  if (!auth) return res.status(401).json({ message: 'No token' });
  const token = auth.split(' ')[1];
  try {
    const decoded = jwt.verify(token, JWT_SECRET);
    // Fetch user details from DB to ensure up-to-date info
    const [rows] = await pool.query('SELECT name, email, username, member_since FROM users WHERE id = ?', [decoded.id]);
    if (rows.length === 0) return res.status(404).json({ message: 'User not found' });
    return res.json({ user: rows[0] });
  } catch {
    return res.status(401).json({ message: 'Invalid token' });
  }
});

export default router;
