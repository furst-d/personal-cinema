# Use the official Node.js image as a parent image
FROM node:20-alpine

# Set the working directory to /var/www
WORKDIR /var/www

# Copy package.json and package-lock.json to the working directory
COPY package*.json ./

# Install dependencies including nodemon
RUN npm install

# Copy the rest of the application code
COPY . .

# Install nodemon globally
RUN npm install -g nodemon

# Install ffmpeg
RUN apk add --no-cache ffmpeg

# Expose the port the app runs on
EXPOSE 4000

# Start the app using npm run dev
CMD ["npm", "run", "dev"]
