const express = require('express');
const bodyParser = require('body-parser');
const videoRoutes = require('./routes/videos');
const projectRoutes = require('./routes/projects');
const sequelize = require('./config/db');
const authMiddleware = require('./middleware/auth');
const videoQueue = require('./libs/video/videoProcessor');
const Video = require('./entities/video');
const Md5 = require('./entities/md5');
const Project = require('./entities/project');
const Callback = require('./entities/callback');

const app = express();
app.use(bodyParser.json());

// Test route
app.get('/', (req, res) => {
    res.json({ message: 'Up and running!' });
});

// Apply auth middleware to video routes
app.use('/videos', authMiddleware, videoRoutes);

// No auth middleware for project creation routes (admin only)
app.use('/projects', projectRoutes);

const PORT = process.env.PORT || 4000;

sequelize.sync({ alter: true }).then(() => {
    console.log('Database connected and synchronized');
    app.listen(PORT, () => {
        console.log(`Server is running on port ${PORT}`);
    });
}).catch(error => {
    console.log('Unable to connect to the database:', error);
});
