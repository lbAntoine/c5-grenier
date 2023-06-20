FROM mysql:8.0

# Make sure wget is installed
RUN apt-get update && apt-get install -y wget

# Get the initialization script
RUN wget https://raw.githubusercontent.com/lbAntoine/c5-grenier/master/sql/import.sql

# Copy the initizalization script to the container
COPY ./import.sql /docker-entrypoint-initdb.d/import.sql

# SET the environment variables
ENV MYSQL_ROOT_PASSWORD=653rag9T
ENV MYSQL_DATABASE=videgrenierenligne
ENV MYSQL_USER=webapplication
ENV MYSQL_PASSWORD=653rag9T