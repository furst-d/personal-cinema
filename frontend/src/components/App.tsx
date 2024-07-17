import React, {useEffect, useState} from 'react';
import VideoPlayer from "./player/VideoPlayer";
import axios from "axios";

const videoJsOptions = {
    sources: [
        {
            src: "http://172.26.86.206:8080/v1/private/videos/url?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3MjEyMTc5MjYsImV4cCI6MTcyMTMwNDMyNiwidXNlcl9pZCI6NiwidXNhZ2UiOjYsInZpZGVvX2lkIjo0M30.id7AJi9m8xt3NDqNx9MhjUu9Yt1N_Q20d5LjjVAiDmafYzGlH6h9hD8wdkfO4Zj3GnZLNgZwvLD_o5AGpnF4uLU0GMt4ivjx09Zpyjc7-kPyixYnZib_beZWBDSaq7mIm4cWZiK038HAJbqPk65LcYq4iJ90FMuCEfCIa4ThWiBIuVsQqe8BlzhszY-ojaWcYQqJai1cA-VdZUAf8LpYg_qPuy4UFBgm86z48C09m-e-Yt7qRYHLt800w28ouHZ8vAaKuiEUC8CMBYbP-h0yOqXVy3AXC_KLXAGYToRpL51TggRe0BL_I1uA9iZ9lN3XUmaL4QNSk5ZS-AUJG5YM7A",
            type: "application/x-mpegURL"
        }
    ]
};

const App: React.FC = () => {
    const apiUrl = import.meta.env.VITE_API_URL;
    const [videoUrl, setVideoUrl] = useState('');
    const [connectionMessage, setConnectionMessage] = useState('');

    useEffect(() => {

        axios.get(`http://localhost:8080`)
            .then(response => {
                setConnectionMessage(response.data.payload.message);
            }).catch(error => {
                console.error('Error loading connection message:', error);
            });
    }, []);

    return (
        <>
            <p>Test 1</p>
            <p>API URL: {apiUrl}</p>
            <p>Connection message: {connectionMessage}</p>
            <div style={{ width: '600px' }}>
                <VideoPlayer options={videoJsOptions} />
            </div>
        </>
    );
}

export default App;
