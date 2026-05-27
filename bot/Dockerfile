FROM node:20

RUN apt-get update && apt-get install -y \
    chromium \
    --no-install-recommends \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /bot

RUN npm install puppeteer-core

COPY visit_forum.js .

CMD ["node", "visit_forum.js"]
