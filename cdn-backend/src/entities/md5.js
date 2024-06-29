const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const Md5 = sequelize.define('Md5', {
    id: {
        type: DataTypes.UUID,
        defaultValue: DataTypes.UUIDV4,
        primaryKey: true,
    },
    md5: {
        type: DataTypes.STRING,
        allowNull: false,
        unique: true,
    },
    isBlacklisted: {
        type: DataTypes.BOOLEAN,
        defaultValue: false,
    }
}, {
    timestamps: true,
});

module.exports = Md5;
