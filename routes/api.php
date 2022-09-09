<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RouterController;
use App\Http\Controllers\InterfaceController;

use App\Models\Router;
use RouterOS\Client;
use RouterOS\Query;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->group(function () {
    Route::post('register', [AuthController::class, 'register']); 
    Route::get('logout', [AuthController::class, 'logout']);
    Route::resource('/users', UserController::class);
    Route::resource('/routers', RouterController::class);
});

Route::group(['prefix'=>'/router/{router}/bridges'],function(){

    Route::get('/', function(Router $router) {
        $client = new Client(['host' => $router->host,'user' => $router->user,'pass' => $router->pass,'port' => (int)$router->port,]);
        $bridge = $client->query('/interface/bridge/print')->read();
        return $bridge;
    });
    Route::post('/', function(Request $request,Router $router) {
        $client = new Client(['host' => $router->host,'user' => $router->user,'pass' => $router->pass,'port' => (int)$router->port,]);
        $query = (new Query('/interface/bridge/add'))->equal('name', $request->name);
        $rs = $client->query($query)->read();
        return $rs;
    });
    // Route::get('/hotspot', function(Request $request, Router $router){
    //     $client = RouterOS::client(['host' => $router->host,'user' => $router->user,'pass' => $router->pass,'port' => (int)$router->port,]);
    //     $hotspot = $client->query('/ip/hotspot/getall')->read();
    //     return $hotspot;
    // });
});

