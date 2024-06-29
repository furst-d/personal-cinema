const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const Callback = sequelize.define('Callback', {
    id: {
        type: DataTypes.UUID,
        defaultValue: DataTypes.UUIDV4,
        primaryKey: true,
    },
    notificationUrl: {
        type: DataTypes.STRING,
        allowNull: true,
    },
    thumbUrl: {
        type: DataTypes.STRING,
        allowNull: true,
    },
}, {
    timestamps: true,
});

module.exports = Callback;
