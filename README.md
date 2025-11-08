# Wallet Management System (WMS)

## Overview
The Wallet Management System (WMS) is a web-based application designed to manage digital wallets. It provides users with the ability to perform various wallet-related operations such as viewing transaction history, sending money, managing vouchers, and more. This project is built using modern web technologies and follows a modular structure for scalability and maintainability.

## Features

### User Authentication
- Secure login and registration system.
- Token-based authentication for API requests.

### Wallet Management
- Perform wallet actions such as sending money and managing vouchers.

#### Secure transactions
- Create a wallet and secure it using a PIN.
- Use this PIN for split-second payments!

#### Vouchers Management
- Deposit and Withdrawals are implemented using vouchers.
- Withdraw any amount in the form of a voucher that can be shared/redeemed.
- Redeem a valid voucher, securely deposit into your wallet. 
- Now you can view your vouchers' history as well.

#### Transaction History
- Fetch and display transaction history without needing to refresh everytime.
- Includes details such as transaction type, ID, amount, and timestamp.

### Modular Frontend
- Organized CSS and JavaScript files for each feature.
- Responsive design for seamless user experience across devices.

### Backend API
- Node.js-based backend for handling authentication and wallet operations.
- Database integration for storing user and transaction data.

### Session Management
- PHP-based session handling for secure user sessions.

## Folder Structure
```
assets/
  - CSS and JavaScript files for frontend styling and functionality.
backend/
  - Node.js backend scripts and database migrations.
session/
  - PHP scripts for session management.
```

## Getting Started

### Prerequisites
- Node.js
- PHP
- MySQL
- XAMPP (for local development)

### Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/hrsha03/wms.git
   ```
2. Navigate to the backend folder and install dependencies:
   ```bash
   cd backend
   npm install
   ```
3. Start the backend server:
   ```bash
   npm start
   ```
4. Set up the database using the migration scripts in `backend/sql/`.
5. Configure XAMPP to serve the PHP files in the `session/` folder.

### Usage
- Open the application in your browser.
- Log in or register to access wallet features.
- Use the navigation bar to explore different functionalities.

## Contributing
Contributions are welcome! Please fork the repository and submit a pull request for review.

## Author
- **hrsha03**