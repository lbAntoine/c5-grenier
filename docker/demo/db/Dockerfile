FROM alpine:latest AS scriptgetter

# Install wget
RUN apk add --no-cache wget

# Get the initialization script
RUN wget https://raw.githubusercontent.com/lbAntoine/c5-grenier/master/sql/import.sql

FROM mysql:8.0

# Copy the initialization script to the container
COPY --from=scriptgetter import.sql /docker-entrypoint-initdb.d/import.sql

# SET the environment variables
ENV MYSQL_ROOT_PASSWORD=653rag9T
ENV MYSQL_DATABASE=videgrenierenligne
ENV MYSQL_USER=webapplication
ENV MYSQL_PASSWORD=653rag9T