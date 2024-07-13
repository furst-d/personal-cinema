import { Sequelize } from 'sequelize-typescript';
import Project from '../entities/project';
import Callback from '../entities/callback';
import Md5 from '../entities/md5';
import Nonce from '../entities/nonce';
import Video from '../entities/video';

const sequelize = new Sequelize({
    dialect: 'postgres',
    host: process.env.POSTGRES_HOST,
    database: process.env.POSTGRES_DB,
    username: process.env.POSTGRES_USER,
    password: process.env.POSTGRES_PASSWORD,
    models: [Project, Callback, Md5, Nonce, Video],
});

export default sequelize;
