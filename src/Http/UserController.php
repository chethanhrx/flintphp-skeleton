<?php

declare(strict_types=1);

namespace App\Http;

use FlintPHP\Framework\Database\ConnectionInterface;
use FlintPHP\Framework\Http\Exception\HttpException;
use FlintPHP\Framework\Http\Request;
use FlintPHP\Framework\Http\Response;
use FlintPHP\Framework\Validation\Validator;

final class UserController
{
    public function __construct(
        private readonly ConnectionInterface $db,
        private readonly Validator $validator,
    ) {
    }

    public function index(Request $request): Response
    {
        $users = $this->db->fetchAll('SELECT id, name, email, created_at FROM users ORDER BY id ASC');

        return Response::json(['data' => $users]);
    }

    public function show(Request $request, int $id): Response
    {
        $user = $this->db->fetch(
            'SELECT id, name, email, created_at FROM users WHERE id = ?',
            [$id]
        );

        if ($user === null) {
            return Response::error('User not found', 404, 'not_found');
        }

        return Response::json(['data' => $user]);
    }

    public function store(Request $request): Response
    {
        // Request::json() throws HttpException 400 on invalid JSON
        $payload = $request->json();

        $result = $this->validator->validate($payload, [
            'name' => ['required', 'string', 'min:1', 'max:100'],
            'email' => ['required', 'string', 'email'],
        ]);

        if (!$result->isValid()) {
            return Response::json([
                'error' => [
                    'message' => 'Validation failed',
                    'status' => 422,
                    'code' => 'validation_failed',
                    'details' => $result->errors(),
                ],
            ], 422);
        }

        $data = $result->validated();

        $this->db->execute(
            'INSERT INTO users (name, email, created_at) VALUES (?, ?, ?)',
            [$data['name'], $data['email'], date('c')]
        );

        $id = (int) $this->db->pdo()->lastInsertId();

        $user = $this->db->fetch(
            'SELECT id, name, email, created_at FROM users WHERE id = ?',
            [$id]
        );

        return Response::json(['data' => $user], 201);
    }

    public function destroy(Request $request, int $id): Response
    {
        $existing = $this->db->fetch('SELECT id FROM users WHERE id = ?', [$id]);
        if ($existing === null) {
            return Response::error('User not found', 404, 'not_found');
        }

        $this->db->execute('DELETE FROM users WHERE id = ?', [$id]);

        return Response::json(['deleted' => true, 'id' => $id]);
    }
}
