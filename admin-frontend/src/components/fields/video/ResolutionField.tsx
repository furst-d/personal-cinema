import React from 'react';
import { useRecordContext } from 'react-admin';
import {FieldProps} from "../../types/field/FieldProps";

const ResolutionField: React.FC<FieldProps> = () => {
    const record = useRecordContext();
    if (!record) return <span>-</span>;

    const width = record['originalWidth'];
    const height = record['originalHeight'];

    return <span>{width}x{height}</span>;
};

export default ResolutionField;
