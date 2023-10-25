<?php

namespace App\Repositories;
use App\Models\Task;
use Exception;

class TaskRepository extends Repository {

    public function getAll($request){
        return Task::with($this->getWiths($request->with))
                    ->filter($request->all())
                    ->get();
    }

    public function getById($request, $id){
        return Task::findOrFail($id);
    }

    public function create($request){
        $task = Task::create($request->all());
        $task->save();
        return $task;
    }

    public function update($request, $id){
        $task = Task::findOrFail($id);
        $task->update($request->all());
        return ['task' => $task];
    }

    public function delete($id){
        Task::where('id', $id)
            ->delete();
        return [ 'message' => 'Task deleted' ];
    }

    public function getByCompany($companyId){
        return Task::where('company_id', $companyId)
                ->get();
    }

    public function updateCost($request, $id){
        $task = Task::findOrFail($id);
        $task->cost = $request->input('cost');
        $task->save();
        return ['task' => $task];
    }
    
}
