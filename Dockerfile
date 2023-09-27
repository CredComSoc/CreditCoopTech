# Stage 1: Build the Vue.js frontend
FROM node:16.14.0 as vue-build
WORKDIR /app-fe
COPY ./sb.web.app/package.json ./sb.web.app/package-lock.json ./
RUN npm install
COPY ./sb.web.app .
RUN npm run build

# Stage 2: Build the Node.js backend
FROM node:16.14.0 as express-server
WORKDIR /app-be
COPY ./backend/package.json ./backend/package-lock.json ./
RUN npm install
COPY ./backend .

# Copy the built Vue.js frontend from the previous stage
COPY --from=vue-build /app-fe/dist ./public

# Set environment variables if needed
# ENV NODE_ENV production

# Expose port 3000 for the Node.js backend (not necessary in Dockerfile)
# EXPOSE 3000

# Start your Node.js application
CMD ["npm", "start"]
