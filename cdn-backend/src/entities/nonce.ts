import { Table, Column, Model, DataType } from 'sequelize-typescript';

@Table
class Nonce extends Model {
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
    value!: string;
}

export default Nonce;
