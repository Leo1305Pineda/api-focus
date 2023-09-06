<?php

namespace App\Repositories;

use App\Mail\DamageVehicleMail;
use App\Mail\NotificationMail;
use App\Models\Damage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DamageRepository extends Repository {

    public function __construct(
        PendingTaskRepository $pendingTaskRepository,
        DamageVehicleMail $damageVehicleMail,
        DamageRoleRepository $damageRoleRepository,
        VehicleRepository $vehicleRepository,
        DamageTaskRepository $damageTaskRepository,
        NotificationMail $notificationMail,
        StateChangeRepository $stateChangeRepository,
    )
    {
        $this->pendingTaskRepository = $pendingTaskRepository;
        $this->damageVehicleMail = $damageVehicleMail;
        $this->damageRoleRepository = $damageRoleRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->damageTaskRepository = $damageTaskRepository;
        $this->notificationMail = $notificationMail;
        $this->stateChangeRepository = $stateChangeRepository;
    }

    public function index($request){
        return Damage::with($this->getWiths($request->with))
            ->filter($request->all())
            ->where('status_damage_id', 1)
            ->orderBy('severity_damage_id', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page'));
    }

    public function store($request){
        $budgetPendingTasks = $request->input('budgetPendingTasks');
        $damage = Damage::create($request->all());
        $damage->user_id = Auth::id();
        $damage->vehicle_id = $request->input('vehicle_id');
        $damage->save();

        $vehicle = $damage->vehicle;
        $damage->reception_id = $vehicle->lastReception->id;
        $damage->save();

        $isDamageTask = false;

        foreach($request->input('tasks') as $task){
            if (!!$budgetPendingTasks && $task == $budgetPendingTasks['task_id']){
                $this->pendingTaskRepository->addPendingTaskFromIncidence($damage->vehicle_id, $task, $damage, $budgetPendingTasks);
            } else {
                $this->pendingTaskRepository->addPendingTaskFromIncidence($damage->vehicle_id, $task, $damage);
            }

            $this->damageTaskRepository->create($damage->id, $task);
            $isDamageTask = true;
        }

        if($isDamageTask) {
            $this->stateChangeRepository->updateSubStateVehicle($vehicle);
        }

        foreach($request->input('roles') as $role){
            $this->damageRoleRepository->create($damage->id, $role);
            if(env('APP_ENV') == 'production' && !env('DISABLED_SEND_MAIL', false)) {
                $this->notificationMail->build($role, $damage->id);
            }
        }

        return $damage;
    }

    public function update($request, $id){
        $damage = Damage::findOrFail($id);
        $damage->update($request->all());
        $damage->datetime_close = Carbon::now();
        $damage->save();
        return $damage;
    }

}
