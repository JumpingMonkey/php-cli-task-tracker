# Task Tracker CLI

A simple command-line task tracker application built with PHP that helps you manage your tasks.
Project URL: [roadmap.sh Task Tracker Project](https://roadmap.sh/projects/task-tracker)

## Features

- Add, update, and delete tasks
- Mark tasks as in progress or done
- List all tasks
- Filter tasks by status (todo, in-progress, done)
- Persistent storage using JSON file

## Requirements

- Docker
- Docker Compose

## Setup

### Option 1: Running as a permanent container (recommended)

1. Start the container in the background:
```bash
docker-compose up -d
```

2. Execute commands using the running container:
```bash
docker exec task-tracker /app/task-cli <command>
```

For example:
```bash
# Add a task
docker exec task-tracker /app/task-cli add "Buy groceries"

# List all tasks
docker exec task-tracker /app/task-cli list
```

3. To stop the container:
```bash
docker-compose down
```

### Option 2: Running one-off commands

1. Build the Docker image:
```bash
docker build -t task-tracker .
```

2. Run individual commands:
```bash
docker run -it --rm -v $(pwd):/app task-tracker /app/task-cli <command>
```

## Usage

```bash
# Adding a new task
docker exec task-tracker /app/task-cli add "Buy groceries"

# Updating a task
docker exec task-tracker /app/task-cli update 1 "Buy groceries and cook dinner"

# Deleting a task
docker exec task-tracker /app/task-cli delete 1

# Marking a task as in progress
docker exec task-tracker /app/task-cli mark-in-progress 1

# Marking a task as done
docker exec task-tracker /app/task-cli mark-done 1

# Listing all tasks
docker exec task-tracker /app/task-cli list

# Listing tasks by status
docker exec task-tracker /app/task-cli list done
docker exec task-tracker /app/task-cli list todo
docker exec task-tracker /app/task-cli list in-progress
```

## Data Storage

Tasks are stored in a `tasks.json` file in the current directory. The file is created automatically when you add your first task.
