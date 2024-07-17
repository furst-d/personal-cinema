// components/VideoPlayer.tsx
import React, { useEffect, useRef } from 'react';
import videojs from 'video.js';
import 'video.js/dist/video-js.css';

const VideoPlayer: React.FC<{ src: string }> = ({ src }) => {
    const videoRef = useRef<HTMLVideoElement>(null);

    useEffect(() => {
        if (videoRef.current) {
            const player = videojs(videoRef.current, {
                autoplay: true,
                controls: true,
                sources: [{
                    src: src,
                    type: 'application/x-mpegURL'
                }]
            });

            return () => {
                player.dispose();
            };
        }
    }, [src]);

    return (
        <div data-vjs-player>
            <video ref={videoRef} className="video-js vjs-big-play-centered" />
        </div>
    );
};

export default VideoPlayer;
