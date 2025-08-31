<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TODO アプリ</title>
</head>
<body>
    <h1>TODO アプリ</h1>

    <!-- 新規追加フォーム -->
    <x-form method="POST" action="/todos/add">
        <table>
            <tr>
                <td><label for="title">タイトル:</label></td>
                <td><input type="text" id="title" name="title" required></td>
            </tr>
            <tr>
                <td><label for="description">説明:</label></td>
                <td><input type="text" id="description" name="description"></td>
            </tr>
            <tr>
                <td></td>
                <td><button type="submit">追加</button></td>
            </tr>
        </table>
    </x-form>

    <hr>

    <!-- TODOリスト -->
    <h2>TODOリスト</h2>

    <?php if (empty($todos)): ?>
        <p>TODOがありません。</p>
    <?php else: ?>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>タイトル</th>
                    <th>説明</th>
                    <th>状態</th>
                    <th>作成日時</th>
                    <th>操作</th>
                    <th>編集</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($todos as $todo): ?>
                    <tr <?= $todo->completed ? 'style="background-color: #f0f0f0;"' : '' ?>>
                        <td><?= $todo->id ?></td>
                        <td <?= $todo->completed ? 'style="text-decoration: line-through;"' : '' ?>>
                            <?= htmlspecialchars($todo->title) ?>
                        </td>
                        <td <?= $todo->completed ? 'style="text-decoration: line-through;"' : '' ?>>
                            <?= htmlspecialchars($todo->description) ?>
                        </td>
                        <td>
                            <?= $todo->completed ? '✓ 完了' : '○ 未完了' ?>
                        </td>
                        <td><?= $todo->createdAt->format('Y-m-d H:i') ?></td>
                        <td>
                            <form method="POST" action="/todos/<?= $todo->id ?>/toggle" style="display: inline;">
                                <x-csrf-token />
                                <button type="submit">
                                    <?= $todo->completed ? '未完了に戻す' : '完了にする' ?>
                                </button>
                            </form>
                            <form method="POST" action="/todos/<?= $todo->id ?>/delete" style="display: inline;">
                                <x-csrf-token />
                                <button type="submit" onclick="return confirm('削除しますか？')">削除</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="/todos/<?= $todo->id ?>/update" style="display: inline;">
                                <x-csrf-token />
                                <input type="text" name="title" value="<?= htmlspecialchars($todo->title) ?>" size="15" required>
                                <input type="text" name="description" value="<?= htmlspecialchars($todo->description) ?>" size="20">
                                <button type="submit">更新</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
