<?php

declare(strict_types=1);

namespace App;

use Tempest\Http\Session\Session;

class TodoService
{
    private const SESSION_KEY = 'todos';
    private const NEXT_ID_KEY = 'next_todo_id';

    public function __construct(
        private Session $session,
    ) {
    }

    public function add(string $title, string $description = ''): Todo
    {
        $todos = $this->getTodos();
        $nextId = $this->getNextId();

        $todo = new Todo(
            id: $nextId,
            title: $title,
            description: $description
        );

        $todos[$todo->id] = $todo;
        $this->saveTodos($todos);
        $this->setNextId($nextId + 1);

        error_log("TodoService::add() - Added todo with ID: " . $todo->id);

        return $todo;
    }

    public function getAll(): array
    {
        $todos = array_values($this->getTodos());
        error_log("TodoService::getAll() - Returning " . count($todos) . " todos");
        return $todos;
    }

    public function toggleComplete(int $id): void
    {
        $todos = $this->getTodos();
        if (isset($todos[$id])) {
            $todos[$id]->completed = !$todos[$id]->completed;
            $this->saveTodos($todos);
        }
    }

    public function delete(int $id): void
    {
        $todos = $this->getTodos();
        unset($todos[$id]);
        $this->saveTodos($todos);
    }

    public function update(int $id, string $title, string $description = ''): void
    {
        $todos = $this->getTodos();
        if (isset($todos[$id])) {
            $todos[$id]->title = $title;
            $todos[$id]->description = $description;
            $this->saveTodos($todos);
        }
    }

    private function getTodos(): array
    {
        $sessionData = $this->session->get(self::SESSION_KEY, []);
        error_log("getTodos() - Session data type: " . gettype($sessionData));
        error_log("getTodos() - Session data count: " . count($sessionData));
        error_log("getTodos() - Session data: " . json_encode($sessionData));

        $todos = [];

        foreach ($sessionData as $key => $todoData) {
            error_log("Processing item $key - type: " . gettype($todoData));

            if ($todoData instanceof Todo) {
                error_log("Item $key is Todo object with ID: " . $todoData->id);
                $todos[$todoData->id] = $todoData;
            } elseif (is_array($todoData)) {
                error_log("Item $key is array with ID: " . ($todoData['id'] ?? 'unknown'));
                $todos[$todoData['id']] = new Todo(
                    id: $todoData['id'],
                    title: $todoData['title'],
                    description: $todoData['description'] ?? '',
                    completed: $todoData['completed'] ?? false,
                    createdAt: new \DateTimeImmutable($todoData['createdAt'] ?? 'now')
                );
            } else {
                error_log("Item $key has unexpected type: " . gettype($todoData));
            }
        }

        error_log("getTodos() - Returning " . count($todos) . " todos");
        return $todos;
    }

    private function saveTodos(array $todos): void
    {
        $todoData = [];
        foreach ($todos as $todo) {
            $todoData[$todo->id] = [
                'id' => $todo->id,
                'title' => $todo->title,
                'description' => $todo->description,
                'completed' => $todo->completed,
                'createdAt' => $todo->createdAt->format('Y-m-d H:i:s')
            ];
        }

        error_log("saveTodos() - Saving " . count($todoData) . " todos to session");
        error_log("saveTodos() - Data: " . json_encode($todoData));

        $this->session->set(self::SESSION_KEY, $todoData);
    }

    private function getNextId(): int
    {
        return $this->session->get(self::NEXT_ID_KEY, 1);
    }

    private function setNextId(int $id): void
    {
        $this->session->set(self::NEXT_ID_KEY, $id);
    }
}