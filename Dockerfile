# Stage 1: Build the Node.js backend
FROM node:16.14.0 as express-server
WORKDIR /app-be
COPY ./backend/package.json ./backend/package-lock.json ./
RUN npm install
COPY ./backend .

# Expose port 3000 for the Node.js backend
EXPOSE 3000

# Start your Node.js application
CMD ["npm", "start"]
