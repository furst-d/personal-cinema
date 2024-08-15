import React from "react";

interface VideoSearchProps {
    phrase: string;
}

const VideoSearch: React.FC<VideoSearchProps> = ({ phrase }) => {
    return (
        <div>
            Hledám: {phrase}
        </div>
    )
}

export default VideoSearch;