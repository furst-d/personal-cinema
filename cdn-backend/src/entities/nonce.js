const { DataTypes } = require('sequelize');
const sequelize = require('../config/db');

const Nonce = sequelize.define('Nonce', {
    id: {
        type: DataTypes.UUID,
        defaultValue: DataTypes.UUIDV4,
        primaryKey: true,
    },
    value: {
        type: DataTypes.STRING,
        allowNull: false,
        unique: true,
    },
}, {
    timestamps: true,
});

module.exports = Nonce;
