import { Table, Column, Model, DataType, ForeignKey, BelongsTo } from 'sequelize-typescript';
import Callback from './callback';

@Table
class Project extends Model {
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
    name!: string;

    @Column({
        type: DataType.STRING,
        allowNull: false,
        unique: true,
    })
    apiKey!: string;

    @ForeignKey(() => Callback)
    @Column({
        type: DataType.UUID,
    })
    callbackId!: string;

    @BelongsTo(() => Callback)
    callback!: Callback;
}

export default Project;
