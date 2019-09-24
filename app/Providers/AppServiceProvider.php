<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(env('QUERY_DEBUG'))
        {
            DB::listen(function($query) {
                Log::info(
                    $query->sql,
                    $query->bindings,
                    $query->time
                );
            });
        }

        // Permite realizar busquedas con arrays en una columna
        Builder::macro('whereArr', function($attribute, $searchTerm) {
            if(is_array($searchTerm)) {
                return $this->where(function($subquery) use($attribute, $searchTerm){
                    foreach($searchTerm as $arr_param) {
                        $subquery->orWhere($attribute,$arr_param);
                    }
                });
            } else {
                return $this->where($attribute,$searchTerm);
            }
        });
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
}
