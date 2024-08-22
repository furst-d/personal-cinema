import React from 'react';
import { useRecordContext } from 'react-admin';
import {FieldProps} from "../../types/field/FieldProps";

const SizeField: React.FC<FieldProps> = ({ source = "" }) => {
    const record = useRecordContext();
    if (!record || !record[source]) return null;

    const size = record[source];

    const formatSize = (size: number) => {
        if (size < 1024) return `${size} B`;
        if (size < 1024 * 1024) return `${(size / 1024).toFixed(2)} KB`;
        if (size < 1024 * 1024 * 1024) return `${(size / (1024 * 1024)).toFixed(2)} MB`;
        return `${(size / (1024 * 1024 * 1024)).toFixed(2)} GB`;
    };

    return <span>{formatSize(size)}</span>;
};

export default SizeField;
