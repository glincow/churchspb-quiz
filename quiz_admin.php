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
            
            // Если это салат (тип 'in'), то value - это название салата
            if ($item_type === 'in' && $item_name === 'Салат — 1-1,5 кг') {
                // Найдем имя человека по дате
                $name_query = "SELECT qd2.value as name
                              FROM questionnaire_data qd2
                              INNER JOIN questionnaire_list ql2 ON qd2.id_list = ql2.id
                              WHERE qd2.date = '$date' AND ql2.type = 'in' 
                               AND ql2.name = 'Комментарий'                              LIMIT 1";
                $name_result = db_query($name_query);
                if ($name_row = $name_result->fetch_assoc()) {
                   $name = !empty($name_row['name']) ? $name_row['name'] : 'Аноним';
                    $people[] = $name . ' (' . $value . ')';                }
            } else {
                // Найдем имя человека по дате
                $name_query = "SELECT qd2.value as name
                              FROM questionnaire_data qd2
                              INNER JOIN questionnaire_list ql2 ON qd2.id_list = ql2.id
                              WHERE qd2.date = '$date' AND ql2.type = 'in' 
                               AND ql2.name = 'Комментарий'                              LIMIT 1";
                $name_result = db_query($name_query);
                if ($name_row = $name_result->fetch_assoc()) {
                   $name = !empty($name_row['name']) ? $name_row['name'] : 'Аноним';
                    $people[] = $name;                }
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

$food_data = getCategoryData(1); // Категория "ЕДА"
$service_data = getCategoryData(1); // Категория "СЛУЖЕНИЕ" (будет фильтроваться по типу)

// Фильтруем: первые 8 - это еда, остальное - служение
$food_items = array_slice($food_data, 0, 8);
$service_items = array_slice($service_data, 8);

?>
<!DOCTYPE html>
<html>
<head>
    <title>ПИР ЛЮБВИ - Администрирование</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .category-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .category-title {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 20px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        .table-hover tbody tr:hover {
            background-color: #f0f4ff;
        }
        .badge-count {
            font-size: 18px;
        }
        .people-list {
            color: #6c757d;
            font-size: 14px;
        }
        .btn-refresh {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-size: 16px;
        }
        .btn-refresh:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .total-summary {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 5px solid #667eea;
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        <!-- Header -->
        <div class="header-section text-center">
            <h1 class="display-4 mb-3">🍞 ПИР ЛЮБВИ 🍷</h1>
            <p class="lead mb-0">Панель администратора</p>
        </div>

        <!-- Buttons -->
        <div class="text-center mb-4">
            <button onclick="location.reload()" class="btn btn-primary btn-refresh">
                <i class="bi bi-arrow-clockwise"></i> Обновить данные
            </button>
        </div>

        <div class="row">
            <!-- ПИЩА -->
            <div class="col-lg-6">
                <div class="category-card">
                    <h2 class="category-title">🍽️ ПИЩА</h2>
                    
                    <?php 
                    $total_food = 0;
                    foreach ($food_items as $item) {
                        $total_food += $item['count'];
                    }
                    ?>
                    
                    <div class="total-summary">
                        <h5>Всего участников: <strong><?php echo $total_food; ?></strong> чел.</h5>
                    </div>

                    <table class="table table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th style="width: 40%">Блюдо</th>
                                <th style="width: 10%" class="text-center">Кол-во</th>
                                <th style="width: 50%">Участники</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($food_items as $item): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                                <td class="text-center">
                                    <span class="badge bg-primary badge-count"><?php echo $item['count']; ?></span>
                                </td>
                                <td>
                                    <div class="people-list">
                                        <?php 
                                        foreach ($item['people'] as $index => $person) {
                                            echo htmlspecialchars($person);
                                            if ($index < count($item['people']) - 1) echo ', ';
                                        }
                                        ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- СЛУЖЕНИЕ -->
            <div class="col-lg-6">
                <div class="category-card">
                    <h2 class="category-title">⛪ СЛУЖЕНИЕ</h2>
                    
                    <?php 
                    $total_service = 0;
                    foreach ($service_items as $item) {
                        $total_service += $item['count'];
                    }
                    ?>
                    
                    <div class="total-summary">
                        <h5>Всего участников: <strong><?php echo $total_service; ?></strong> чел.</h5>
                    </div>

                    <table class="table table-hover">
                        <thead class="table-success">
                            <tr>
                                <th style="width: 40%">Служение</th>
                                <th style="width: 10%" class="text-center">Кол-во</th>
                                <th style="width: 50%">Участники</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($service_items as $item): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                                <td class="text-center">
                                    <span class="badge bg-success badge-count"><?php echo $item['count']; ?></span>
                                </td>
                                <td>
                                    <div class="people-list">
                                        <?php 
                                        foreach ($item['people'] as $index => $person) {
                                            echo htmlspecialchars($person);
                                            if ($index < count($item['people']) - 1) echo ', ';
                                        }
                                        ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
