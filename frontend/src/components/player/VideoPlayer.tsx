import * as React from "react";
import videojs from "video.js";
import "video.js/dist/video-js.css";

interface IVideoPlayerProps {
    options: videojs.PlayerOptions;
}

const initialOptions: videojs.PlayerOptions = {
    controls: true,
    fluid: true,
    controlBar: {
        volumePanel: {
            inline: false
        }
    }
};

const VideoPlayer: React.FC<IVideoPlayerProps> = ({ options }) => {
    const videoNode = React.useRef<HTMLVideoElement | null>(null);
    const player = React.useRef<videojs.Player | null>(null);

    React.useEffect(() => {
        if (videoNode.current) {
            player.current = videojs(videoNode.current!, {
                ...initialOptions,
                ...options
            }).ready(function () {
                console.log("Ready");
            });
        }

        return () => {
            if (player.current) {
                if ("dispose" in player.current) {
                    player.current.dispose();
                }
            }
        };
    }, [options]);

    return <video ref={videoNode} className="video-js" style={{ width: "600px" }} />;
};

export default VideoPlayer;
