# Vue con vite

FROM node:22 as build
WORKDIR /app

# copiamos dependencias

COPY package*.json ./
RUN npm install

# Copiamos el resto del codigo ( vite genera una carpeta dist)

COPY . .
RUN npm run build


# NGINX

FROM nginx:alpine

# Copiamos el dist a la carpeta publica de nginx

COPY --from=build /app/dist /usr/share/nginx/html

EXPOSE 80


# dejamos encendido nginx para servir la aplicacion
CMD ["nginx", "-g", "daemon off;"]



