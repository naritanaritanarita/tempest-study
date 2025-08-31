<?php

declare(strict_types=1);

namespace App;

use Tempest\Router\Get;
use Tempest\Router\Post;
use Tempest\Http\Request;
use Tempest\Http\Responses\Redirect;
use Tempest\View\View;

use function Tempest\view;

final readonly class TodoController
{
    public function __construct(
        private TodoService $todoService,
    ) {
    }

    #[Get('/todos')]
    public function index(): View
    {
        $todos = $this->todoService->getAll();
        return view('todo.view.php', todos: $todos);
    }

    #[Post('/todos/add')]
    public function add(Request $request): Redirect
    {
        $title = trim($request->get('title', ''));
        $description = trim($request->get('description', ''));

        if (!empty($title)) {
            $todo = $this->todoService->add($title, $description);
        }

        return new Redirect('/todos');
    }


    #[Post('/todos/{id}/toggle')]
    public function toggle(int $id): Redirect
    {
        $this->todoService->toggleComplete($id);
        return new Redirect('/todos');
    }

    #[Post('/todos/{id}/delete')]
    public function delete(int $id): Redirect
    {
        $this->todoService->delete($id);
        return new Redirect('/todos');
    }

    #[Post('/todos/{id}/update')]
    public function update(int $id, Request $request): Redirect
    {
        $title = trim($request->get('title', ''));
        $description = trim($request->get('description', ''));

        if (!empty($title)) {
            $this->todoService->update($id, $title, $description);
        }

        return new Redirect('/todos');
    }
}