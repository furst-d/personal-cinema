# Use the official Node.js image as a parent image
FROM node:20-alpine

# Set the working directory to /app
WORKDIR /var/www

# Copy package.json and package-lock.json to the working directory
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy the rest of the application code
COPY . .

# Build the application
RUN npm run build

# Expose port
EXPOSE 4173

# Start the app
CMD ["npm", "run", "preview"]