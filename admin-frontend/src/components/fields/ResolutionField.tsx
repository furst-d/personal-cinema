import React from 'react';
import { useRecordContext } from 'react-admin';

interface ResolutionFieldProps {
    source: string;
    label?: string;
}

const ResolutionField: React.FC<ResolutionFieldProps> = ({ source, label }) => {
    const record = useRecordContext();
    if (!record) return null;

    const width = record['originalWidth'];
    const height = record['originalHeight'];

    return <span>{width}x{height}</span>;
};

export default ResolutionField;
