import { Table, Column, Model, DataType } from 'sequelize-typescript';

@Table
class Callback extends Model {
    @Column({
        type: DataType.UUID,
        defaultValue: DataType.UUIDV4,
        primaryKey: true,
    })
    id!: string;

    @Column({
        type: DataType.STRING,
        allowNull: false,
    })
    notificationUrl!: string;

    @Column({
        type: DataType.STRING,
        allowNull: false,
    })
    thumbUrl!: string;
}

export default Callback;
