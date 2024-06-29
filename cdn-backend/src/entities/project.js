const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');
const Callback = require('./callback');

const Project = sequelize.define('Project', {
    id: {
        type: DataTypes.UUID,
        defaultValue: DataTypes.UUIDV4,
        primaryKey: true,
    },
    name: {
        type: DataTypes.STRING,
        allowNull: false,
    },
    apiKey: {
        type: DataTypes.STRING,
        allowNull: false,
        unique: true,
    },
    callbackId: {
        type: DataTypes.UUID,
        references: {
            model: Callback,
            key: 'id',
        },
    },
}, {
    timestamps: true,
});

Project.belongsTo(Callback, { foreignKey: 'callbackId' });

module.exports = Project;
