import React from 'react';
import { useRecordContext } from 'react-admin';
import {FieldProps} from "../../types/field/FieldProps";

const DurationField: React.FC<FieldProps> = ({ source = "" }) => {
    const record = useRecordContext();
    if (!record || !record[source]) return <span>-</span>;

    const duration = record[source];

    const formatDuration = (duration: number) => {
        const seconds = Math.floor(duration % 60);
        const minutes = Math.floor((duration / 60) % 60);
        const hours = Math.floor(duration / 3600);
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    };

    return <span>{formatDuration(duration)}</span>;
};

export default DurationField;
