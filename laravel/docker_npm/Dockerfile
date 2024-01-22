FROM node:16.17 as frontend

ARG PUSHER_APP_ID
ENV MIX_PUSHER_APP_ID=${PUSHER_APP_ID}

ARG PUSHER_APP_KEY
ENV MIX_PUSHER_APP_KEY=${PUSHER_APP_KEY}

ARG PUSHER_APP_SECRET
ENV MIX_PUSHER_APP_SECRET=${PUSHER_APP_SECRET}

ARG PUSHER_APP_CLUSTER
ENV MIX_PUSHER_APP_CLUSTER=${PUSHER_APP_CLUSTER}

ARG PUSHER_APP_SSL
ENV MIX_PUSHER_APP_SSL=${PUSHER_APP_SSL}

ARG PUSHER_APP_PORT
ENV MIX_PUSHER_APP_PORT=${PUSHER_APP_PORT}

ARG PUSHER_APP_HOST
ENV MIX_PUSHER_APP_HOST=${PUSHER_APP_HOST}

ARG PUSHER_APP_SCHEME
ENV MIX_PUSHER_APP_SCHEME=${PUSHER_APP_SCHEME}


RUN mkdir -p /app/public

COPY package.json yarn.lock webpack.mix.js /app/
COPY resources/js/ /app/resources/js/
COPY resources/css/ /app/resources/css/
COPY resources/lang/ /app/resources/lang/
COPY resources/views/ /app/resources/views/
COPY resources/dashmix/ /app/resources/dashmix/
#COPY resources/sass/ /app/resources/sass/
#COPY resources/fonts/ /app/resources/fonts/
#COPY resources/images/ /app/resources/images/
#COPY resources/packages/ /app/resources/packages/

WORKDIR /app
RUN yarn install
RUN yarn prod

