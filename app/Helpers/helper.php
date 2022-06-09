<?php

use App\Models\DualControlApproval;
use App\Models\DualControlTask;
use Illuminate\Support\MessageBag;
use Illuminate\Http\Exceptions\HttpResponseException;

if (! function_exists('zpc_parse_message_bag')) {
    /**
     * Get an request body content converted to JSON
     *
     * @param MessageBag $message_bag
     * @return string
     */
    function zpc_parse_message_bag(MessageBag $message_bag)
    {
        $errors = '';
        foreach ($message_bag->messages() as $message){
            $errors.=implode(",\n",$message)."\n";
        }
        return trim(trim($errors),",");
    }
}

if (! function_exists('zpc_abort_if')) {

    function zpc_abort_if($boolean,string $message,int $http_status = 400) {
        if (is_callable($boolean) && $boolean()){
            zpc_abort($message,$http_status);
        } else if (!is_callable($boolean) && $boolean){
            zpc_abort($message,$http_status);
        }
    }
}

if (!function_exists('zpc_abort')){

    /**
     * @throws HttpResponseException
     */
    function zpc_abort(string $message, int $http_status = 400) {
        throw new HttpResponseException(zpc_failure_response($message,$http_status));
    }
}

if (! function_exists('zpc_failure_response')) {
    function zpc_failure_response(string $message,int $http_status = 400) {
        return response(['status'=>'failed','message'=>$message],$http_status);
    }
}


if (!function_exists('is_dual_control_active')){
    function is_dual_control_active(string $code){
        $control = DualControlTask::query()->where('code', $code)->first();
        return $control && $control->is_active;
    }
}

if (!function_exists('is_dual_control_pending')){
    function is_dual_control_pending(string $code, int $id, string $dual_code){
        $approves = [];
        if ($code == 'PE001') {
            $approval = DualControlApproval::query()->where([
                'object_id' => $id,
                'dual_control_task_code' => $dual_code,
                'status' => 'PENDING'
            ])->first();
            if ($approval->approvers != null){
                $approves = json_decode($approval->approvers);
            }
        }
        return $approves;
    }
}
