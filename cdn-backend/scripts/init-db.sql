-- Insert data into Callbacks table
INSERT INTO Callbacks (notificationUrl, thumbUrl)
VALUES
    ('http://localhost:8080/v1/private/cdn/notification?token=testNotificationToken', 'http://localhost/v1/private/cdn/thumb?token=testThumbToken');

-- Insert data into Projects table with test API key
INSERT INTO Projects (name, apiKey, callbackId)
VALUES
    ('SoukromeKino', 'testApiKey12345', (SELECT id FROM Callbacks LIMIT 1));

-- Insert data into Settings table
INSERT INTO Settings (key, value)
VALUES
    ('video_size_limit', '100MB');
