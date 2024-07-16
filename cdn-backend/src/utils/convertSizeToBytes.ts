export const convertSizeToBytes = (size: string): number => {
    const value = size.toUpperCase().trim();
    const unit = value.slice(-2); // Last two characters are the unit
    const numericValue = parseInt(value.slice(0, -2)); // Everything except the last two characters

    if (isNaN(numericValue)) {
        throw new Error('Invalid size format');
    }

    switch (unit) {
        case 'KB':
            return numericValue * 1024;
        case 'MB':
            return numericValue * 1024 * 1024;
        case 'GB':
            return numericValue * 1024 * 1024 * 1024;
        default:
            throw new Error('Unsupported unit');
    }
};