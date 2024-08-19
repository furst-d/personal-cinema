import React from 'react';

import videojs from 'video.js';
import 'video.js/dist/video-js.css';
import 'videojs-hotkeys';

import "videojs-contrib-quality-levels";
// @ts-ignore
import qualitySelectorHls from "videojs-quality-selector-hls";

videojs.registerPlugin('qualitySelectorHls',qualitySelectorHls);

import {VideoPlayerContainerStyle} from "../../styles/player/VideoPlayer";

interface IVideoPlayerProps {
    src: string;
}

const VideoPlayer: React.FC<IVideoPlayerProps> = ({ src }) => {
    const videoRef = React.useRef<HTMLDivElement | null>(null);
    const playerRef = React.useRef<any | null>(null);
    const options = {
        controls: true,
        fluid: true,
        sources: [{ src, type: "application/vnd.apple.mpegurl" }],
        controlBar: {
            children: [
                "playToggle",
                "volumePanel",
                "currentTimeDisplay",
                "timeDivider",
                "durationDisplay",
                "progressControl",
                "remainingTimeDisplay",
                "pictureInPictureToggle",
                "captionsButton",
                "fullscreenToggle",
                "qualitySelector"
            ]
        },
        plugins: {
            hotkeys: {
                volumeStep: 0.1,
                seekStep: 5
            },
            qualitySelectorHls: {
                vjsIconClass: 'vjs-icon-cog'
            }
        }
    };

    React.useEffect(() => {
        if (!playerRef.current) {
            const videoElement = document.createElement("video-js");

            videoElement.classList.add('vjs-big-play-centered');
            if (videoRef.current) {
                videoRef.current.appendChild(videoElement);
            }

            playerRef.current = videojs(videoElement, options, () => {
                videojs.log('player is ready');
            });

        } else {
            const player = playerRef.current;

            player.src(options.sources);
        }
    }, [options, src]);


    React.useEffect(() => {
        const player = playerRef.current;

        return () => {
            if (player && !player.isDisposed()) {
                player.dispose();
                playerRef.current = null;
            }
        };
    }, []);

    return (
        <VideoPlayerContainerStyle data-vjs-player={true}>
            <div ref={videoRef} />
        </VideoPlayerContainerStyle>
    );
}

export default VideoPlayer;
