FROM php:8.2-cli

WORKDIR /app

COPY . /app

RUN chmod +x /app/task-cli

CMD ["php", "-v"]
