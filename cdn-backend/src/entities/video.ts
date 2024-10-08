import { Table, Column, Model, DataType, ForeignKey, BelongsTo } from 'sequelize-typescript';
import Md5 from './md5';
import Project from './project';

@Table
class Video extends Model {
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
    title!: string;

    @Column({
        type: DataType.STRING,
        allowNull: false,
        defaultValue: 'pending',
    })
    status!: string;

    @Column({
        type: DataType.STRING,
        allowNull: true,
    })
    originalPath!: string;

    @Column({
        type: DataType.STRING,
        allowNull: true,
    })
    hlsPath!: string;

    @Column({
        type: DataType.STRING,
        allowNull: true,
    })
    thumbnailPath!: string;

    @Column({
        type: DataType.STRING,
        allowNull: true,
    })
    codec!: string;

    @Column({
        type: DataType.STRING,
        allowNull: false,
    })
    extension!: string;

    @Column({
        type: DataType.INTEGER,
        allowNull: false,
    })
    size!: number;

    @Column({
        type: DataType.INTEGER,
        allowNull: true,
    })
    length!: number;

    @Column({
        type: DataType.INTEGER,
        allowNull: true,
    })
    originalWidth!: number;

    @Column({
        type: DataType.INTEGER,
        allowNull: true,
    })
    originalHeight!: number;

    @Column({
        type: DataType.JSON,
        allowNull: true,
    })
    parameters!: object;

    @Column({
        type: DataType.BOOLEAN,
        allowNull: false,
        defaultValue: false,
    })
    isDeleted!: boolean;

    @Column({
        type: DataType.JSON,
        allowNull: true,
    })
    conversions!: object;

    @ForeignKey(() => Md5)
    @Column({
        type: DataType.UUID,
    })
    md5Id!: string;

    @BelongsTo(() => Md5)
    md5!: Md5;

    @ForeignKey(() => Project)
    @Column({
        type: DataType.UUID,
    })
    projectId!: string;

    @BelongsTo(() => Project)
    project!: Project;
}

export default Video;
