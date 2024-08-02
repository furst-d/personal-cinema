import * as React from "react";
import videojs from "video.js";
import "video.js/dist/video-js.css";

interface IVideoPlayerProps {
    src: string;
}

const initialOptions = {
    controls: true,
    fluid: true,
    controlBar: {
        volumePanel: {
            inline: false
        }
    }
};

const VideoPlayer: React.FC<IVideoPlayerProps> = ({ src }) => {
    const videoNode = React.useRef<HTMLVideoElement | null>(null);
    const player = React.useRef<any | null>(null);

    React.useEffect(() => {
        if (videoNode.current) {
            player.current = videojs(videoNode.current, {
                ...initialOptions,
                sources: [{ src, type: "application/vnd.apple.mpegurl" }]
            }).ready(function (this) {
                return this;
            });
        }

        return () => {
            if (player.current) {
                player.current.dispose();
                player.current = null;
            }
        };
    }, [src]);

    return (
        <div data-vjs-player={true}>
            <video key={src} ref={videoNode} className="video-js" style={{ width: "100%" }} />
        </div>
    );
};

export default VideoPlayer;
