import { Table, Column, Model, DataType } from 'sequelize-typescript';

@Table
class Md5 extends Model {
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
    md5!: string;

    @Column({
        type: DataType.BOOLEAN,
        defaultValue: false,
    })
    isBlacklisted!: boolean;
}

export default Md5;
