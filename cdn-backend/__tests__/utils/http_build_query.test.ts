import http_build_query from '../../src/utils/http_build_query';

describe('http_build_query', () => {

    it('should convert a flat object to a query string', () => {
        const obj = { name: 'John', age: 30, city: 'New York' };
        const result = http_build_query(obj);
        expect(result).toBe('name=John&age=30&city=New%20York');
    });

    it('should convert a nested object to a query string', () => {
        const obj = { user: { name: 'John', age: 30 }, city: 'New York' };
        const result = http_build_query(obj);
        expect(result).toBe('user%5Bname%5D=John&user%5Bage%5D=30&city=New%20York');
    });

    it('should handle objects with arrays', () => {
        const obj = { items: ['apple', 'banana', 'orange'] };
        const result = http_build_query(obj);
        expect(result).toBe('items%5B0%5D=apple&items%5B1%5D=banana&items%5B2%5D=orange');
    });

    it('should handle empty objects', () => {
        const obj = {};
        const result = http_build_query(obj);
        expect(result).toBe('');
    });

    it('should handle objects with null values', () => {
        const obj = { name: 'John', age: null };
        const result = http_build_query(obj);
        expect(result).toBe('name=John&age=null');
    });

    it('should handle undefined values gracefully', () => {
        const obj = { name: 'John', age: undefined };
        const result = http_build_query(obj);
        expect(result).toBe('name=John&age=undefined');
    });

});
