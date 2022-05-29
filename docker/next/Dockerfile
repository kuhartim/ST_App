FROM node:16-buster

RUN apt-get update


WORKDIR /app/

COPY frontend/package.json package.json
COPY frontend/yarn.lock yarn.lock

COPY frontend/next.config.js next.config.js
COPY frontend/hooks hooks/
COPY frontend/middleware middleware/
COPY frontend/public public/
COPY frontend/services services/
COPY frontend/settings settings/
COPY frontend/static static/
COPY frontend/styles styles/
COPY frontend/pages pages/
COPY frontend/components components/

RUN yarn install

RUN yarn build

# RUN yarn global add serve

# CMD ["serve", "-l", "8080", "-s", "build"]

CMD ["yarn", "start"]