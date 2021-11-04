
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
</head>
<body>
    <div style="display: flex; flex-direction:row">
        <div style="margin-right: 10px">
            <h2>Recepciones en la última semana</h2>
            <img src="https://quickchart.io/chart?c={{$reception_by_days}}" width="500px" alt="">
        </div>
        <div>
            <h2>Recepciones en los últimos meses</h2>
            <img src="https://quickchart.io/chart?c={{$reception_by_months}}" width="500px" alt="">
        </div>
    </div>
    <div style="display: flex; flex-direction:row">
        <div style="margin-right: 10px">
            <h2>Tareas iniciadas en la última semana</h2>
            <img src="https://quickchart.io/chart?c={{$pending_tasks_by_days}}" width="500px" alt="">
        </div>
        <div>
            <h2>Tareas iniciadas en los últimos meses</h2>
            <img src="https://quickchart.io/chart?c={{$pending_tasks_by_months}}" width="500px" alt="">
        </div>
    </div>
    <div style="display: flex; flex-direction:row">
        <div style="margin-right: 10px">
            <h2>Tareas finalizadas en la última semana</h2>
            <img src="https://quickchart.io/chart?c={{$pending_tasks_end_by_days}}" width="500px" alt="">
        </div>
        <div>
            <h2>Tareas finalizadas en los últimos meses</h2>
            <img src="https://quickchart.io/chart?c={{$pending_tasks_end_by_months}}" width="500px" alt="">
        </div>
    </div>
    <div style="display: flex; flex-direction:row">
        <div style="margin-right: 10px">
            <h2>Fotos gesionadas en la última semana</h2>
            <img src="https://quickchart.io/chart?c={{$images_by_days}}" width="500px" alt="">
        </div>
        <div>
            <h2>Fotos gesionadas en la últimos meses</h2>
            <img src="https://quickchart.io/chart?c={{$images_by_months}}" width="500px" alt="">
        </div>
    </div>
    <div style="display: flex; flex-direction:row">
        <div style="margin-right: 10px">
            <h2>Presupuestos de tareas gesionados en la última semana</h2>
            <img src="https://quickchart.io/chart?c={{$budget_pending_tasks_by_days}}" width="500px" alt="">
        </div>
        <div>
            <h2>Presupuestos de tareas gesionados en la últimos meses</h2>
            <img src="https://quickchart.io/chart?c={{$budget_pending_tasks_by_months}}" width="500px" alt="">
        </div>
    </div>
    <table border="1">
        <thead>
            <tr>
                <th>Total de vehículos</th>
                <th>Total de recepciones</th>
                <th>Total de tareas creadas</th>
                <th>Total de fotos</th>
                <th>Total de presupuestos</th>
            </tr>
        </thead>
        <tbody>
            <tr>    
                <td>{{$total_vehicles}}</td>
                <td>{{$total_receptions}}</td>
                <td>{{$total_task_created}}</td>
                <td>{{$total_images}}</td>
                <td>{{$total_budget_pending_task}}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>