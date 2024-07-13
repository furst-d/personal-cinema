import React from 'react';

const App: React.FC = () => {
    const apiUrl = import.meta.env.VITE_API_URL;

    return (
        <>
            <p>Test 1</p>
            <p>API URL: {apiUrl}</p>
        </>
    );
}

export default App;
