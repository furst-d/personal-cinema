import React from 'react';
import { useRecordContext } from 'react-admin';
import {FieldProps} from "../../types/field/FieldProps";

const PercentField: React.FC<FieldProps> = ({ source = "" }) => {
    const record = useRecordContext();
    if (!record || !record[source]) return <span>-</span>;

    const percent = record[source] || 0;

    return <span>{percent} %</span>;
};

export default PercentField;
