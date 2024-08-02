import React, { useEffect, useState } from "react";
import { Typography } from "@mui/material";
import VideoPreview from "./VideoPreview";
import axiosPrivate from "../../api/axiosPrivate";

interface RecommendationsProps {
    videoId: number;
}

const VideoRecommendations: React.FC<RecommendationsProps> = ({ videoId }) => {
    const [recommendations, setRecommendations] = useState<any[]>([]);
    const [loading, setLoading] = useState<boolean>(true);

    useEffect(() => {
        axiosPrivate.get(`/v1/personal/videos/${videoId}/recommend`, {
            params: { limit: 3 }
        })
            .then(recommendationsResponse => {
                setRecommendations(recommendationsResponse.data.payload.data);
            })
            .catch(err => {
                console.error("Error loading recommendations", err);
            })
            .finally(() => {
                setLoading(false);
            });
    }, [videoId]);

    return (
        <>
            <Typography variant="h6" gutterBottom>Další videa</Typography>
            {recommendations.map((recommendation) => (
                <VideoPreview key={recommendation.id} video={recommendation} />
            ))}
        </>
    );
};

export default VideoRecommendations;
