# Task Management API

This is a Task Management API built with Laravel. The API allows you to manage tasks including creating, reading, updating, deleting, and searching tasks.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)

## Features

- User authentication
- CRUD operations for tasks
- Full-text search on task descriptions
- Sorting by fields
- Task tree
- Swagger documentation for API endpoints

## Requirements

- PHP >= 8.0
- Composer
- Laravel >= 8.0
- MySQL or other supported database

## Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/yourusername/task-management-api.git
    cd tasks
    ```

2. Copy the `.env.example` file to `.env` and configure your environment variables:

    ```bash
    cp .env.example .env
    ```

3. Build the Docker containers:

    ```bash
    docker compose build
    ```

## Usage

1. Start the Docker containers:

    ```bash
    docker compose up -d
    ```

2. Initialize Laravel application and set application key:

    ```bash
    docker exec -it laravel-docker bash
    php artisan key:generate
    ```

3. Migrate the database:

    ```bash
    php artisan migrate
    ```
4. Seed the database with initial data (optional): \\
    ```bash
    php artisan db:seed
    ```

5. Access the application at `http://localhost:9000`.

6. Access the documentation at `http://localhost:9000/api/documentation`.

