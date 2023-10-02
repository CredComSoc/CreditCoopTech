
# Stage 1: Build the Node.js backend
FROM node:16.14.0 as express-server
WORKDIR /app
COPY ./package.json ./package-lock.json ./
RUN npm install
COPY . .

EXPOSE 3000

# Start your Node.js application
CMD ["npm", "start"]
