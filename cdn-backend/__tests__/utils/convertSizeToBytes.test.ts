import { convertSizeToBytes } from '../../src/utils/convertSizeToBytes';

describe('convertSizeToBytes', () => {

    it('should convert KB to bytes', () => {
        const result = convertSizeToBytes('10KB');
        expect(result).toBe(10240); // 10 KB = 10 * 1024
    });

    it('should convert MB to bytes', () => {
        const result = convertSizeToBytes('5MB');
        expect(result).toBe(5242880); // 5 MB = 5 * 1024 * 1024
    });

    it('should convert GB to bytes', () => {
        const result = convertSizeToBytes('2GB');
        expect(result).toBe(2147483648); // 2 GB = 2 * 1024 * 1024 * 1024
    });

    it('should throw an error for unsupported units', () => {
        expect(() => convertSizeToBytes('5TB')).toThrow('Unsupported unit');
    });

    it('should throw an error for invalid format', () => {
        expect(() => convertSizeToBytes('5XB')).toThrow('Unsupported unit'); // Invalid unit
        expect(() => convertSizeToBytes('size')).toThrow('Invalid size format'); // No numeric value
    });

    it('should trim whitespace and convert lowercase units', () => {
        const result = convertSizeToBytes('  15 mb  ');
        expect(result).toBe(15728640); // 15 MB = 15 * 1024 * 1024
    });

});
