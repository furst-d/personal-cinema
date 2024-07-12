const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');
const Md5 = require('./md5');
const Project = require('./project');

const Video = sequelize.define('Video', {
    id: {
        type: DataTypes.UUID,
        defaultValue: DataTypes.UUIDV4,
        primaryKey: true,
    },
    title: {
        type: DataTypes.STRING,
        allowNull: false,
    },
    status: {
        type: DataTypes.STRING,
        allowNull: false,
        defaultValue: 'pending',
    },
    originalPath: {
        type: DataTypes.STRING,
        allowNull: true,
    },
    hlsPath: {
        type: DataTypes.STRING,
        allowNull: true,
    },
    hash: {
        type: DataTypes.STRING,
        allowNull: true,
    },
    extension: {
        type: DataTypes.STRING,
        allowNull: false,
    },
    size: {
        type: DataTypes.INTEGER,
        allowNull: false,
    },
    length: {
        type: DataTypes.INTEGER,
        allowNull: true,
    },
    resolution: {
        type: DataTypes.STRING,
        allowNull: true,
    },
    parameters: {
        type: DataTypes.JSON,
        allowNull: true,
    },
    md5Id: {
        type: DataTypes.UUID,
        references: {
            model: Md5,
            key: 'id'
        }
    },
    projectId: {
        type: DataTypes.UUID,
        references: {
            model: Project,
            key: 'id'
        }
    }
}, {
    timestamps: true,
});

Md5.hasMany(Video, { foreignKey: 'md5Id' });
Video.belongsTo(Md5, { foreignKey: 'md5Id' });

Project.hasMany(Video, { foreignKey: 'projectId' });
Video.belongsTo(Project, { foreignKey: 'projectId' });

module.exports = Video;
