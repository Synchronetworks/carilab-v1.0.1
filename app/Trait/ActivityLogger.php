<?php
namespace App\Trait;

use \Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Authorizable;

trait ActivityLogger
{
    /**
     * Log activity for any model.
     *
     * @param string $type ('add' or 'update')
     * @param Model $model
     */
    public function logActivity(string $type, Model $model, string $logName = null)
    {
        $user = auth()->user(); // Get authenticated user
        $name = $model->fullname ?? $model->name ?? $model->title ?? $model->bank_name ??  '';
        $modelName = strtolower(class_basename($model));
        if($modelName == 'catlogmanagement')
        {
            $modelName = 'Test Case';
        }
        $logName =  "{$modelName}_{$type}" ?? $logName ; 
        activity()
            ->useLog($logName)
            ->performedOn($model)
            ->causedBy($user)
            ->withProperties(
                $model->toArray(), // Pass the model data as an array directly
            )
            ->tap(function (Activity $activity) use ($type, $model, $user,$name) {
                $activity->description = (class_basename($model) == 'CatlogManagement') ? 'Test Case'. ' ' . $model->id .' '. $name . ' is ' . $type : class_basename($model) . ' ' . $model->id .' '. $name . ' is ' . $type;
                $activity->subject_type = (class_basename($model) == 'CatlogManagement') ? 'Test Case' : class_basename($model);
                $activity->subject_id = $model->id;
                $activity->causer_type = $user->user_type ?? null;
                $activity->causer_id = $user->id;
            })
            ->log($type);
            
    }
}
?>