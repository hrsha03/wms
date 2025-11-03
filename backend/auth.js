import express from 'express';
import bcrypt from 'bcryptjs';
import jwt from 'jsonwebtoken';
import pool from './db.js';

const router = express.Router();
const JWT_SECRET = process.env.JWT_SECRET || 'changeme';

// Helper to verify token and return decoded payload
function verifyTokenFromHeader(req) {
  const auth = req.headers.authorization;
  if (!auth) return null;
  const token = auth.split(' ')[1];
  try {
    return jwt.verify(token, JWT_SECRET);
  } catch (err) {
    return null;
  }
}

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

// GET wallet info for current user
router.get('/wallet', async (req, res) => {
  const decoded = verifyTokenFromHeader(req);
  if (!decoded) return res.status(401).json({ message: 'Invalid or missing token' });
  try {
    const [rows] = await pool.query('SELECT wms_id, wms_balance FROM users WHERE id = ?', [decoded.id]);
    if (rows.length === 0) return res.status(404).json({ message: 'User not found' });
    const user = rows[0];
    if (!user.wms_id) return res.json({ hasWallet: false });
    return res.json({ hasWallet: true, wms_id: user.wms_id, balance: user.wms_balance });
  } catch (err) {
    return res.status(500).json({ message: 'Server error' });
  }
});

// POST create wallet for current user
router.post('/wallet', async (req, res) => {
  const decoded = verifyTokenFromHeader(req);
  if (!decoded) return res.status(401).json({ message: 'Invalid or missing token' });
  const { walletId, pin } = req.body;
  if (!walletId || !pin) return res.status(400).json({ message: 'Missing walletId or pin' });
  if (!/^\d{4}$/.test(pin)) return res.status(400).json({ message: 'PIN must be 4 digits' });
  try {
    // Check if user already has a wallet
    const [existing] = await pool.query('SELECT wms_id FROM users WHERE id = ?', [decoded.id]);
    if (existing.length === 0) return res.status(404).json({ message: 'User not found' });
    if (existing[0].wms_id) return res.status(409).json({ message: 'Wallet already exists' });
    const initialBalance = 500;
    await pool.query('UPDATE users SET wms_id = ?, wms_pin = ?, wms_balance = ? WHERE id = ?', [walletId, pin, initialBalance, decoded.id]);
    return res.status(201).json({ message: 'Wallet created', wms_id: walletId, balance: initialBalance });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ message: 'Server error' });
  }
});

// Send Money API
router.post('/wallet/send', async (req, res) => {
  const decoded = verifyTokenFromHeader(req);
  if (!decoded) return res.status(401).json({ message: 'Invalid or missing token' });
  const { recipientId, amount, pin } = req.body;
  if (!recipientId || !amount || !pin) return res.status(400).json({ message: 'Missing fields' });
  try {
    const [sender] = await pool.query('SELECT wms_id, wms_pin, wms_balance FROM users WHERE id = ?', [decoded.id]);
    if (sender.length === 0) return res.status(404).json({ message: 'Sender not found' });
    const senderData = sender[0];
    
    if (pin!=senderData.wms_pin) return res.status(401).json({ message: 'Invalid PIN' });
    if (senderData.wms_balance < amount) return res.status(400).json({ message: 'Insufficient balance' });

    const [recipient] = await pool.query('SELECT id FROM users WHERE wms_id = ?', [recipientId]);
    if (recipient.length === 0) return res.status(404).json({ message: 'Recipient not found' });
    const recipientIdDb = recipient[0].id;

    await pool.query('UPDATE users SET wms_balance = wms_balance - ? WHERE id = ?', [amount, decoded.id]);
    await pool.query('UPDATE users SET wms_balance = wms_balance + ? WHERE id = ?', [amount, recipientIdDb]);
    await pool.query(
      'INSERT INTO transactions (user_id, verb, wms_id, amount) VALUES (?, ?, ?, ?)',
      [decoded.id, 'debit', recipientId, amount]
    );
    await pool.query(
      'INSERT INTO transactions (user_id, verb, wms_id, amount) VALUES (?, ?, ?, ?)',
      [recipientIdDb, 'credit', senderData.wms_id, amount]
    );

    return res.status(200).json({ message: 'Transaction successful' });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ message: 'Server error' });
  }
});

// Transaction History API
router.get('/wallet/transactions', async (req, res) => {
  const decoded = verifyTokenFromHeader(req);
  if (!decoded) {
    console.error('Token verification failed or missing token');
    return res.status(401).json({ message: 'Invalid or missing token' });
  }
  try {
    const [transactions] = await pool.query(
      'SELECT verb, wms_id, amount, created_at AS timestamp FROM transactions WHERE user_id = ? ORDER BY created_at DESC',
      [decoded.id]
    );
    return res.json({ transactions });
  } catch (err) {
    console.error('Error fetching transactions:', err);
    return res.status(500).json({ message: 'Server error' });
  }
});

// Deposit Funds Using Voucher
router.post('/wallet/deposit', async (req, res) => {
  const decoded = verifyTokenFromHeader(req);
  if (!decoded) return res.status(401).json({ message: 'Invalid or missing token' });
  const { voucherId } = req.body;
  if (!voucherId) return res.status(400).json({ message: 'Missing voucher ID' });
  try {
    const [voucher] = await pool.query('SELECT * FROM vouchers WHERE voucher_id = ? AND status = ?', [voucherId, 'active']);
    if (voucher.length === 0) return res.status(404).json({ message: 'Voucher not found or already redeemed' });
    const voucherData = voucher[0];

    await pool.query('UPDATE users SET wms_balance = wms_balance + ? WHERE id = ?', [voucherData.amount, decoded.id]);
    await pool.query('UPDATE vouchers SET status = ? WHERE voucher_id = ?', ['redeemed', voucherId]);
    await pool.query(
      'INSERT INTO transactions (user_id, verb, wms_id, amount) VALUES (?, ?, ?, ?)',
      [decoded.id, 'deposit', voucherId, voucherData.amount]
    );

    return res.status(200).json({ message: 'Deposit successful', amount: voucherData.amount });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ message: 'Server error' });
  }
});

// Withdraw Funds by Issuing Voucher
router.post('/wallet/withdraw', async (req, res) => {
  const decoded = verifyTokenFromHeader(req);
  if (!decoded) return res.status(401).json({ message: 'Invalid or missing token' });
  const { amount } = req.body;
  if (!amount || amount <= 0) return res.status(400).json({ message: 'Invalid amount' });
  try {
    const [user] = await pool.query('SELECT wms_balance FROM users WHERE id = ?', [decoded.id]);
    if (user.length === 0) return res.status(404).json({ message: 'User not found' });
    const userData = user[0];
    if (userData.wms_balance < amount) return res.status(400).json({ message: 'Insufficient balance' });

    const voucherId = `V-${Date.now()}`;
    await pool.query('UPDATE users SET wms_balance = wms_balance - ? WHERE id = ?', [amount, decoded.id]);
    await pool.query(
      'INSERT INTO vouchers (voucher_id, user_id, amount, status) VALUES (?, ?, ?, ?)',
      [voucherId, decoded.id, amount, 'active']
    );
    await pool.query(
      'INSERT INTO transactions (user_id, verb, wms_id, amount) VALUES (?, ?, ?, ?)',
      [decoded.id, 'withdraw', voucherId, amount]
    );

    return res.status(200).json({ message: 'Withdrawal successful', voucherId });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ message: 'Server error' });
  }
});

// Fetch Vouchers
router.get('/wallet/vouchers', async (req, res) => {
  const decoded = verifyTokenFromHeader(req);
  if (!decoded) return res.status(401).json({ message: 'Invalid or missing token' });
  try {
    const [vouchers] = await pool.query('SELECT voucher_id, amount, status FROM vouchers WHERE user_id = ?', [decoded.id]);
    return res.json({ vouchers });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ message: 'Server error' });
  }
});

// Update Profile
router.put('/update-profile', async (req, res) => {
  const decoded = verifyTokenFromHeader(req);
  if (!decoded) return res.status(401).json({ message: 'Invalid or missing token' });

  const { name, email } = req.body;
  if (!name && !email) return res.status(400).json({ message: 'No fields to update' });

  try {
    const updates = [];
    const values = [];

    if (name) {
      updates.push('name = ?');
      values.push(name);
    }
    if (email) {
      updates.push('email = ?');
      values.push(email);
    }

    values.push(decoded.id);

    await pool.query(`UPDATE users SET ${updates.join(', ')} WHERE id = ?`, values);
    return res.status(200).json({ message: 'Profile updated successfully' });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ message: 'Server error' });
  }
});

// Change Password
router.put('/change-password', async (req, res) => {
  const decoded = verifyTokenFromHeader(req);
  if (!decoded) return res.status(401).json({ message: 'Invalid or missing token' });

  const { currentPassword, newPassword } = req.body;
  if (!currentPassword || !newPassword) return res.status(400).json({ message: 'Missing fields' });

  try {
    const [rows] = await pool.query('SELECT password FROM users WHERE id = ?', [decoded.id]);
    if (rows.length === 0) return res.status(404).json({ message: 'User not found' });

    const user = rows[0];
    const match = await bcrypt.compare(currentPassword, user.password);
    if (!match) return res.status(401).json({ message: 'Current password is incorrect' });

    const hash = await bcrypt.hash(newPassword, 10);
    await pool.query('UPDATE users SET password = ? WHERE id = ?', [hash, decoded.id]);

    return res.status(200).json({ message: 'Password changed successfully' });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ message: 'Server error' });
  }
});

export default router;
