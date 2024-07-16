import { Table, Column, Model, DataType } from 'sequelize-typescript';

@Table
class Settings extends Model {
    @Column({
        type: DataType.UUID,
        defaultValue: DataType.UUIDV4,
        primaryKey: true,
    })
    id!: string;

    @Column({
        type: DataType.STRING,
        allowNull: false,
        unique: true,
    })
    key!: string;

    @Column({
        type: DataType.STRING,
        allowNull: false,
    })
    value!: string;
}

export default Settings;
