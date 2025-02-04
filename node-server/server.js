const express = require('express');
const multer = require('multer');
const xlsx = require('xlsx');
const mysql = require('mysql2');
const cors = require('cors');
const dotenv = require('dotenv');

// Initialize environment variables
dotenv.config();

// Initialize app
const app = express();
const port = 4000;

// Use CORS to allow requests from Laravel
app.use(cors());

// Set up file upload using Multer
const storage = multer.memoryStorage();
const upload = multer({ storage: storage });

// MySQL Database Connection
const db = mysql.createConnection({
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASS,
    database: process.env.DB_NAME,
});

db.connect((err) => {
    if (err) throw err;
    console.log('Connected to MySQL database');
});

// POST /upload route to handle file upload
app.post('/upload', upload.single('file'), (req, res) => {
    if (!req.file) {
        return res.status(400).send({ message: 'No file uploaded' });
    }

    // Read the Excel file
    const workbook = xlsx.read(req.file.buffer, { type: 'buffer' });
    const sheet_name_list = workbook.SheetNames;
    const users = xlsx.utils.sheet_to_json(workbook.Sheets[sheet_name_list[0]]);

    // Insert users into the database
    users.forEach((user) => {
        const { name, email, status } = user;

        // SQL query to insert data into the 'users' table
        const query = `INSERT INTO users (name, email, status) VALUES (?, ?, ?)`;
        db.query(query, [name, email, status], (err, result) => {
            if (err) {
                console.error('Error inserting data:', err);
                return res.status(500).send({ message: 'Error inserting data into database' });
            }
        });
    });

    return res.send({ message: 'Users imported successfully' });
});

// Start the server
app.listen(port, () => {
    console.log(`Node.js server running on http://localhost:${port}`);
});
