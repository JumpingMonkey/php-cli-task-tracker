<?php

class TaskManager {
    private string $filename = 'tasks.json';
    private array $tasks = [];

    public function __construct() {
        $this->loadTasks();
    }

    private function loadTasks(): void {
        if (file_exists($this->filename)) {
            $content = file_get_contents($this->filename);
            $this->tasks = json_decode($content, true) ?? [];
        }
    }

    private function saveTasks(): void {
        file_put_contents($this->filename, json_encode($this->tasks, JSON_PRETTY_PRINT));
    }

    private function getNextId(): int {
        if (empty($this->tasks)) {
            return 1;
        }
        return max(array_column($this->tasks, 'id')) + 1;
    }

    public function addTask(string $description): int {
        $id = $this->getNextId();
        $this->tasks[] = [
            'id' => $id,
            'description' => $description,
            'status' => 'todo',
            'createdAt' => date('Y-m-d H:i:s'),
            'updatedAt' => date('Y-m-d H:i:s')
        ];
        $this->saveTasks();
        return $id;
    }

    public function updateTask(int $id, string $description): bool {
        foreach ($this->tasks as &$task) {
            if ($task['id'] === $id) {
                $task['description'] = $description;
                $task['updatedAt'] = date('Y-m-d H:i:s');
                $this->saveTasks();
                return true;
            }
        }
        return false;
    }

    public function deleteTask(int $id): bool {
        foreach ($this->tasks as $key => $task) {
            if ($task['id'] === $id) {
                unset($this->tasks[$key]);
                $this->tasks = array_values($this->tasks);
                $this->saveTasks();
                return true;
            }
        }
        return false;
    }

    public function markTaskStatus(int $id, string $status): bool {
        foreach ($this->tasks as &$task) {
            if ($task['id'] === $id) {
                $task['status'] = $status;
                $task['updatedAt'] = date('Y-m-d H:i:s');
                $this->saveTasks();
                return true;
            }
        }
        return false;
    }

    public function listTasks(?string $status = null): array {
        if ($status === null) {
            return $this->tasks;
        }
        return array_filter($this->tasks, fn($task) => $task['status'] === $status);
    }
}

function printUsage() {
    echo "Usage:\n";
    echo "  php task-cli.php add \"Task description\"\n";
    echo "  php task-cli.php update <id> \"New description\"\n";
    echo "  php task-cli.php delete <id>\n";
    echo "  php task-cli.php mark-in-progress <id>\n";
    echo "  php task-cli.php mark-done <id>\n";
    echo "  php task-cli.php list [done|todo|in-progress]\n";
}

function printTasks(array $tasks) {
    if (empty($tasks)) {
        echo "No tasks found.\n";
        return;
    }

    foreach ($tasks as $task) {
        echo sprintf(
            "ID: %d | Status: %s | Created: %s | Updated: %s\n%s\n\n",
            $task['id'],
            $task['status'],
            $task['createdAt'],
            $task['updatedAt'],
            $task['description']
        );
    }
}

if ($argc < 2) {
    printUsage();
    exit(1);
}

$taskManager = new TaskManager();
$command = $argv[1];

try {
    switch ($command) {
        case 'add':
            if ($argc !== 3) {
                throw new Exception("Please provide a task description");
            }
            $id = $taskManager->addTask($argv[2]);
            echo "Task added successfully (ID: $id)\n";
            break;

        case 'update':
            if ($argc !== 4) {
                throw new Exception("Please provide task ID and new description");
            }
            if ($taskManager->updateTask((int)$argv[2], $argv[3])) {
                echo "Task updated successfully\n";
            } else {
                echo "Task not found\n";
            }
            break;

        case 'delete':
            if ($argc !== 3) {
                throw new Exception("Please provide task ID");
            }
            if ($taskManager->deleteTask((int)$argv[2])) {
                echo "Task deleted successfully\n";
            } else {
                echo "Task not found\n";
            }
            break;

        case 'mark-in-progress':
            if ($argc !== 3) {
                throw new Exception("Please provide task ID");
            }
            if ($taskManager->markTaskStatus((int)$argv[2], 'in-progress')) {
                echo "Task marked as in progress\n";
            } else {
                echo "Task not found\n";
            }
            break;

        case 'mark-done':
            if ($argc !== 3) {
                throw new Exception("Please provide task ID");
            }
            if ($taskManager->markTaskStatus((int)$argv[2], 'done')) {
                echo "Task marked as done\n";
            } else {
                echo "Task not found\n";
            }
            break;

        case 'list':
            $status = $argc === 3 ? $argv[2] : null;
            if ($status && !in_array($status, ['done', 'todo', 'in-progress'])) {
                throw new Exception("Invalid status. Use: done, todo, or in-progress");
            }
            printTasks($taskManager->listTasks($status));
            break;

        default:
            throw new Exception("Unknown command: $command");
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    printUsage();
    exit(1);
}
