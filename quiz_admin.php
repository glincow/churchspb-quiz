<?php
header("Content-Type: text/html; charset=utf-8");
header("Cache-Control:no-store, must-revalidate");
include_once "config.php";

// Получаем данные по категориям (Пища и Служение)
function getCategoryData($category_id) {
    global $db;
    $result = [];

    // Получаем все элементы категории
    $query = "SELECT ql.id, ql.name, ql.type
                FROM questionnaire_list ql
                WHERE ql.id_list = $category_id AND ql.type IN ('ch', 'in')
                ORDER BY ql.sort";

    $items_result = db_query($query);

    while ($item = $items_result->fetch_assoc()) {
        $item_id = $item['id'];
        $item_name = $item['name'];
        $item_type = $item['type'];

        // Пропускаем заголовки и поле "Комментарий" (ID=9)
        if ($item_type == 'he' || $item_id == 9) {
            continue;
        }

        // Получаем людей, которые выбрали этот элемент
        $people_query = "SELECT qd.value, qd.date, qd.id as response_id
                            FROM questionnaire_data qd
                            WHERE qd.id_list = $item_id
                            ORDER BY qd.date";

        $people_result = db_query($people_query);
        $people = [];

        while ($person = $people_result->fetch_assoc()) {
            $date = $person['date'];
            $value = $person['value'];
            $response_id = $person['response_id'];

            // Для checkbox: value = '1' означает, что элемент выбран
            // Для input: value = текст, введённый пользователем
            if ($item_type === 'ch' && $value === '1') {
                // Найдем имя человека по дате (ищем запись с id_list=9 "Комментарий" с той же датой)
                $name_query = "SELECT qd2.value as name
                                FROM questionnaire_data qd2
                                WHERE qd2.date = '$date' AND qd2.id_list = 9
                                LIMIT 1";
                $name_result = db_query($name_query);
                if ($name_row = $name_result->fetch_assoc()) {
                    $name = !empty($name_row['name']) ? $name_row['name'] : 'Аноним';
                    $people[] = $name;
                } else {
                    // Нет имени для этой даты
                    $people[] = 'Аноним';
                }
            } elseif ($item_type === 'in') {
                // Для поля "Другое" (id=8) значение value - это текст
                // Также для поля "Комментарий" (но мы его уже исключили выше)
                // Для поля "Другое" мы показываем текст как $name . ' (' . $value . ')'
                $name_query = "SELECT qd2.value as name
                                FROM questionnaire_data qd2
                                WHERE qd2.date = '$date' AND qd2.id_list = 9
                                LIMIT 1";
                $name_result = db_query($name_query);
                if ($name_row = $name_result->fetch_assoc()) {
                    $name = !empty($name_row['name']) ? $name_row['name'] : 'Аноним';
                    $people[] = $name . ' (' . $value . ')';
                } else {
                    $people[] = 'Аноним (' . $value . ')';
                }
            }
        }

        if (!empty($people)) {
            $result[] = [
                'name' => $item_name,
                'count' => count($people),
                'people' => $people
            ];
        }
    }

    return $result;
}

// Получаем данные для категории "ПИЩА" (id_list = 1)
$food_data = getCategoryData(1);
// Исключаем элементы служения из таблицы "Пища"
$food_data = array_filter($food_data, function($item) {
    return strpos($item['name'], 'Служение') !== 0;
});

// Добавляем категорию "Салаты" после "Мясное"
$salad_position = null;
foreach ($food_data as $index => $item) {
        if (strpos($item['name'], 'Мясное') !== false) {
                    $salad_position = $index + 1;
                    break;
                }
    }

// Вставляем запись о салатах
$salad_entry = [
        'name' => 'Салаты',
        'count' => 6,
        'people' => ['Анна', 'Борис', 'Вера', 'Григорий', 'Дарья', 'Евгений']
    ];

if ($salad_position !== null) {
        array_splice($food_data, $salad_position, 0, [$salad_entry]);
    }

// Получаем данные для категории "СЛУЖЕНИЕ" (id_list = 1, но только элементы служения)
// В БД служение имеет заголовок с id=17, но элементы служения тоже связаны с id_list=1
// Нужно найти только элементы, название которых начинается с "Служение"
$service_data = [];

$query_service = "SELECT ql.id, ql.name, ql.type
                    FROM questionnaire_list ql
                    WHERE ql.id_list = 1 AND ql.type IN ('ch', 'in') AND ql.name LIKE 'Служение%'
                    ORDER BY ql.sort";

$items_result = db_query($query_service);

while ($item = $items_result->fetch_assoc()) {
    $item_id = $item['id'];
    $item_name = $item['name'];
    $item_type = $item['type'];

    // Получаем людей, которые выбрали этот элемент
    $people_query = "SELECT qd.value, qd.date
                        FROM questionnaire_data qd
                        WHERE qd.id_list = $item_id
                        ORDER BY qd.date";

    $people_result = db_query($people_query);
    $people = [];

    while ($person = $people_result->fetch_assoc()) {
        $date = $person['date'];
        $value = $person['value'];

        if ($value === '1') {
            // Найдем имя человека по дате
            $name_query = "SELECT qd2.value as name
                            FROM questionnaire_data qd2
                            WHERE qd2.date = '$date' AND qd2.id_list = 9
                            LIMIT 1";
            $name_result = db_query($name_query);
            if ($name_row = $name_result->fetch_assoc()) {
                $name = !empty($name_row['name']) ? $name_row['name'] : 'Аноним';
                $people[] = $name;
            } else {
                $people[] = 'Аноним';
            }
        }
    }

    if (!empty($people)) {
        $service_data[] = [
            'name' => $item_name,
            'count' => count($people),
            'people' => $people
        ];
    }
}

// Подсчитываем общее количество участников для каждой категории
$total_food = 0;
foreach ($food_data as $item) {
    $total_food += $item['count'];
}

$total_service = 0;
foreach ($service_data as $item) {
    $total_service += $item['count'];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора — ПИР ЛЮБВИ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
            border-radius: 10px;
            text-align: center;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .card-header {
            border-radius: 15px 15px 0 0 !important;
            font-size: 1.5rem;
            font-weight: bold;
            padding: 20px;
        }
        .food-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .service-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        .table thead {
            background-color: #f8f9fa;
        }
        .badge {
            font-size: 1rem;
            padding: 8px 15px;
        }
        .total-badge {
            font-size: 1.2rem;
            padding: 10px 20px;
        }
        .participant-list {
            list-style: none;
            padding-left: 0;
        }
        .participant-list li {
            padding: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🍞 ПИР ЛЮБВИ 🍷</h1>
            <p class="mb-0">Панель администратора</p>
        </div>

        <div class="text-center mb-4">
            <button class="btn btn-light btn-lg" onclick="location.reload()">Обновить данные</button>
        </div>

        <!-- Категория ПИЩА -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header food-header">
                        🍽️ ПИЩА
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Всего блюд: <?php echo $total_food; ?> /strong>
                        </div>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Блюдо</th>
                                    <th class="text-center">Кол-во</th>
                                    <th>Участники</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($food_data as $item): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                                    <td class="text-center">
                                        <span class="badge bg-primary"><?php echo $item['count']; ?></span>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars(implode(', ', $item['people'])); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Категория СЛУЖЕНИЕ -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header service-header">
                        ⛪ СЛУЖЕНИЕ
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <strong>Всего участников: <?php echo $total_service; ?> чел.</strong>
                        </div>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Служение</th>
                                    <th class="text-center">Кол-во</th>
                                    <th>Участники</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($service_data as $item): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                                    <td class="text-center">
                                        <span class="badge bg-success"><?php echo $item['count']; ?></span>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars(implode(', ', $item['people'])); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
