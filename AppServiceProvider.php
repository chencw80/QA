<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator; //20181025 Change by SHC 20180816_CP_001 
use Illuminate\Support\Facades\DB; //20181025 Change by SHC 20180816_CP_001 

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        $this->validateUniqueSpaceCheck();   //20181030 changed by SHC 20180816_CP_001 
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
    //20181030 changed by SHC 20180816_CP_001 - Start
    public function validateUniqueSpaceCheck(){
        Validator::extend('unique_space_check',function($attribute,$value,$parameters){
            $attribute =(isset($paramters[1]))? $parameters[1]: $attribute;
            $value=trim(preg_replace('/\s\s+/', '', $value));
            $check= DB::table($parameters[0])->where($attribute,$value)->count();
            //$this->Fnlog('parameters:'.$parameters[0].' attribute:'.$attribute.' value:'.$value.' check:'.$check);
            return ($check>0)?false:true;
        }, 'The :attribute has already been taken.'
        );
    }


    //20181030 changed by SHC 20180816_CP_001 -End

}
