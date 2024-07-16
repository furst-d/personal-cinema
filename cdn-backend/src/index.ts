import dotenv from 'dotenv';
dotenv.config();

import express from 'express';
import bodyParser from 'body-parser';
import videoRoutes from './routes/videos';
import projectRoutes from './routes/projects';
import uploadRoutes from './routes/upload';
import sequelize from './config/db';
import './helpers/video/videoProcessor';

const app = express();
app.use(bodyParser.json());

app.use('/upload', uploadRoutes);
app.use('/videos', videoRoutes);
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
